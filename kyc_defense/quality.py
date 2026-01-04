from __future__ import annotations

"""Image quality assessment utilities."""

from dataclasses import dataclass, field
from typing import List, Optional, Tuple

import numpy as np
from PIL import Image

try:
    import cv2  # type: ignore
except Exception:
    cv2 = None


@dataclass
class QualityResult:
    """Quality metrics extracted from an image."""

    score: float
    blur_variance: float
    exposure_mean: float
    exposure_p05: float
    exposure_p95: float
    saturation_ratio: float
    noise_estimate: float
    contrast_std: float
    resolution_ok: bool
    aspect_ok: bool
    blockiness: float
    border_suspect: bool
    issues: List[str] = field(default_factory=list)


def _to_gray_np(image: Image.Image) -> np.ndarray:
    if image.mode != "L":
        image = image.convert("L")
    return np.asarray(image, dtype=np.float32)


def _laplacian_variance(gray: np.ndarray) -> float:
    if cv2 is not None:
        lap = cv2.Laplacian(gray, cv2.CV_32F)
        return float(lap.var())
    lap = (
        -4.0 * gray
        + np.roll(gray, 1, axis=0)
        + np.roll(gray, -1, axis=0)
        + np.roll(gray, 1, axis=1)
        + np.roll(gray, -1, axis=1)
    )
    return float(lap.var())


def _box_blur(gray: np.ndarray) -> np.ndarray:
    if cv2 is not None:
        return cv2.GaussianBlur(gray, (3, 3), 0)
    padded = np.pad(gray, 1, mode="reflect")
    acc = np.zeros_like(gray)
    for dy in range(3):
        for dx in range(3):
            acc += padded[dy : dy + gray.shape[0], dx : dx + gray.shape[1]]
    return acc / 9.0


def _noise_estimate(gray: np.ndarray) -> float:
    blurred = _box_blur(gray)
    residual = gray - blurred
    return float(np.std(residual))


def _exposure_metrics(gray: np.ndarray) -> Tuple[float, float, float, float]:
    mean = float(np.mean(gray))
    p05 = float(np.percentile(gray, 5))
    p95 = float(np.percentile(gray, 95))
    saturated = float(np.mean((gray <= 5) | (gray >= 250)))
    return mean, p05, p95, saturated


def _blockiness(gray: np.ndarray) -> float:
    h, w = gray.shape
    if h < 16 or w < 16:
        return 0.0
    vb = 0.0
    if w >= 16:
        v1 = gray[:, 8::8]
        v0 = gray[:, 7::8]
        cols = min(v1.shape[1], v0.shape[1])
        if cols > 0:
            vb = float(np.abs(v1[:, :cols] - v0[:, :cols]).mean())
    hb = 0.0
    if h >= 16:
        h1 = gray[8::8, :]
        h0 = gray[7::8, :]
        rows = min(h1.shape[0], h0.shape[0])
        if rows > 0:
            hb = float(np.abs(h1[:rows, :] - h0[:rows, :]).mean())
    diff = np.abs(gray[:, 1:] - gray[:, :-1]).mean() + np.abs(gray[1:, :] - gray[:-1, :]).mean()
    if diff <= 1e-6:
        return 0.0
    return float(min(1.0, ((vb + hb) / 2.0) / diff))


def _border_suspicion(gray: np.ndarray) -> bool:
    h, w = gray.shape
    band = max(1, int(round(min(h, w) * 0.05)))
    if band < 1:
        return False
    border = np.concatenate(
        [
            gray[:band, :].ravel(),
            gray[-band:, :].ravel(),
            gray[:, :band].ravel(),
            gray[:, -band:].ravel(),
        ]
    )
    center = gray[band:-band, band:-band]
    if center.size == 0:
        return False
    border_var = float(np.var(border))
    center_var = float(np.var(center))
    if center_var <= 1e-6:
        return False
    ratio = border_var / center_var
    extreme = float(np.mean((border <= 5) | (border >= 250)))
    return ratio < 0.15 and extreme > 0.4


def assess_quality(
    image: Image.Image,
    *,
    expected_aspect: Optional[float] = None,
    min_resolution: Tuple[int, int] = (600, 400),
) -> QualityResult:
    """Assess image quality with simple, explainable heuristics."""

    gray = _to_gray_np(image)
    blur_var = _laplacian_variance(gray)
    mean, p05, p95, sat = _exposure_metrics(gray)
    noise = _noise_estimate(gray)
    contrast = float(np.std(gray))
    blockiness = _blockiness(gray)
    border_suspect = _border_suspicion(gray)

    w, h = image.size
    resolution_ok = w >= min_resolution[0] and h >= min_resolution[1]
    aspect_ok = True
    if expected_aspect is not None:
        aspect = w / float(h)
        aspect_ok = abs(aspect - expected_aspect) <= 0.12

    issues: List[str] = []
    score = 100.0
    if blur_var < 60.0:
        issues.append("low_sharpness")
        score -= 20.0
    if sat > 0.08:
        issues.append("saturation_clipping")
        score -= 15.0
    if noise > 12.0:
        issues.append("high_noise")
        score -= 15.0
    if not resolution_ok:
        issues.append("low_resolution")
        score -= 20.0
    if not aspect_ok:
        issues.append("aspect_mismatch")
        score -= 10.0
    if blockiness > 0.35:
        issues.append("compression_artifacts")
        score -= 10.0
    if border_suspect:
        issues.append("border_crop_suspicion")
        score -= 10.0

    score = float(max(0.0, min(100.0, score)))

    return QualityResult(
        score=score,
        blur_variance=blur_var,
        exposure_mean=mean,
        exposure_p05=p05,
        exposure_p95=p95,
        saturation_ratio=sat,
        noise_estimate=noise,
        contrast_std=contrast,
        resolution_ok=resolution_ok,
        aspect_ok=aspect_ok,
        blockiness=blockiness,
        border_suspect=border_suspect,
        issues=issues,
    )
