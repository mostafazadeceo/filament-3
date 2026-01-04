from __future__ import annotations

"""Selfie forensics and face quality heuristics."""

from dataclasses import dataclass, field
from pathlib import Path
from typing import List, Optional, Tuple

import numpy as np
from PIL import Image, ImageDraw

from .recapture import RecaptureResult, compute_recapture_signals
from .io import save_image

try:
    import cv2  # type: ignore
except Exception:
    cv2 = None


@dataclass
class FaceBox:
    """Face bounding box in pixel coordinates."""

    x: int
    y: int
    w: int
    h: int

    def area(self) -> int:
        return max(0, self.w) * max(0, self.h)


@dataclass
class SelfieForensicsResult:
    """Forensics indicators derived from a selfie image."""

    face_detected: bool
    face_box: Optional[FaceBox]
    face_ratio: float
    yaw_score: float
    pitch_score: float
    roll_deg: float
    illumination_std: float
    background_uniformity: float
    recapture: RecaptureResult
    issues: List[str] = field(default_factory=list)
    preview_path: Optional[str] = None


def _to_gray_np(image: Image.Image) -> np.ndarray:
    if image.mode != "L":
        image = image.convert("L")
    return np.asarray(image, dtype=np.float32)


def _detect_face(image: Image.Image) -> Optional[FaceBox]:
    if cv2 is None:
        return None
    gray = _to_gray_np(image).astype(np.uint8)
    cascade_path = getattr(cv2.data, "haarcascades", None)
    if cascade_path is None:
        return None
    cascade_file = str(Path(cascade_path) / "haarcascade_frontalface_default.xml")
    try:
        face_cascade = cv2.CascadeClassifier(cascade_file)
    except Exception:
        return None
    if face_cascade.empty():
        return None
    faces = face_cascade.detectMultiScale(gray, scaleFactor=1.1, minNeighbors=4, minSize=(40, 40))
    if faces is None or len(faces) == 0:
        return None
    x, y, w, h = max(faces, key=lambda f: f[2] * f[3])
    return FaceBox(int(x), int(y), int(w), int(h))


def _pose_scores(face_gray: np.ndarray) -> Tuple[float, float, float]:
    h, w = face_gray.shape
    if h < 8 or w < 8:
        return 0.0, 0.0, 0.0
    left = face_gray[:, : w // 2]
    right = face_gray[:, w // 2 :]
    top = face_gray[: h // 2, :]
    bottom = face_gray[h // 2 :, :]

    yaw_score = float(abs(left.mean() - right.mean()) / max(1e-6, face_gray.mean()))
    pitch_score = float(abs(top.mean() - bottom.mean()) / max(1e-6, face_gray.mean()))

    roll_deg = 0.0
    if cv2 is not None:
        edges = cv2.Canny(face_gray.astype(np.uint8), 80, 160)
    else:
        gx = np.abs(np.diff(face_gray, axis=1, prepend=face_gray[:, :1]))
        gy = np.abs(np.diff(face_gray, axis=0, prepend=face_gray[:1, :]))
        edges = ((gx + gy) > 40).astype(np.uint8)
    coords = np.column_stack(np.where(edges > 0))
    if coords.shape[0] > 20:
        coords = coords - coords.mean(axis=0)
        cov = np.cov(coords, rowvar=False)
        eigvals, eigvecs = np.linalg.eigh(cov)
        principal = eigvecs[:, np.argmax(eigvals)]
        roll_rad = float(np.arctan2(principal[0], principal[1]))
        roll_deg = float(np.degrees(roll_rad))

    return yaw_score, pitch_score, roll_deg


def _background_uniformity(gray: np.ndarray, face_box: Optional[FaceBox]) -> float:
    if face_box is None:
        return float(np.std(gray) / 255.0)
    mask = np.ones(gray.shape, dtype=bool)
    x0 = max(face_box.x, 0)
    y0 = max(face_box.y, 0)
    x1 = min(face_box.x + face_box.w, gray.shape[1])
    y1 = min(face_box.y + face_box.h, gray.shape[0])
    mask[y0:y1, x0:x1] = False
    background = gray[mask]
    if background.size == 0:
        return 0.0
    return float(np.std(background) / 255.0)


def _draw_face_preview(image: Image.Image, face_box: Optional[FaceBox]) -> Image.Image:
    preview = image.copy()
    if face_box is None:
        return preview
    draw = ImageDraw.Draw(preview)
    draw.rectangle(
        (face_box.x, face_box.y, face_box.x + face_box.w, face_box.y + face_box.h),
        outline=(255, 80, 80),
        width=3,
    )
    return preview


def analyze_selfie_forensics(
    image: Image.Image,
    *,
    save_preview_path: Optional[str] = None,
) -> SelfieForensicsResult:
    """Run selfie forensics heuristics and return indicators."""

    gray = _to_gray_np(image)
    face_box = _detect_face(image)
    face_detected = face_box is not None
    face_ratio = 0.0
    yaw_score = pitch_score = roll_deg = 0.0
    illum_std = float(np.std(gray) / 255.0)

    if face_box is not None:
        face_ratio = face_box.area() / float(image.width * image.height)
        face_crop = gray[
            face_box.y : face_box.y + face_box.h,
            face_box.x : face_box.x + face_box.w,
        ]
        yaw_score, pitch_score, roll_deg = _pose_scores(face_crop)
        illum_std = float(np.std(face_crop) / 255.0)

    bg_uniform = _background_uniformity(gray, face_box)
    recapture = compute_recapture_signals(image)

    preview_path = None
    if save_preview_path is not None:
        preview = _draw_face_preview(image, face_box)
        preview_path = save_image(preview, save_preview_path)

    issues: List[str] = []
    if not face_detected:
        issues.append("face_not_detected")
    if face_ratio < 0.08 and face_detected:
        issues.append("face_too_small")
    if yaw_score > 0.22 or pitch_score > 0.22:
        issues.append("pose_off_center")
    if illum_std < 0.05:
        issues.append("flat_lighting")
    if bg_uniform < 0.03:
        issues.append("background_uniform")
    if recapture.issues:
        issues.append("recapture_hint")

    return SelfieForensicsResult(
        face_detected=face_detected,
        face_box=face_box,
        face_ratio=face_ratio,
        yaw_score=yaw_score,
        pitch_score=pitch_score,
        roll_deg=roll_deg,
        illumination_std=illum_std,
        background_uniformity=bg_uniform,
        recapture=recapture,
        issues=issues,
        preview_path=preview_path,
    )


def liveness_harness_placeholder() -> str:
    """Placeholder for academic liveness research harness (no bypass logic)."""

    return "Liveness harness is a placeholder for research-only integration."
