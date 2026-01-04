from __future__ import annotations

"""I/O utilities for loading images and videos with normalization."""

from dataclasses import dataclass
from pathlib import Path
from typing import Iterable, List, Optional, Sequence, Tuple

import numpy as np
from PIL import Image, ImageOps

try:
    import cv2  # type: ignore
except Exception:
    cv2 = None


IMAGE_EXTS = {".jpg", ".jpeg", ".png", ".bmp", ".gif", ".webp", ".tif", ".tiff"}
VIDEO_EXTS = {".mp4", ".mov", ".avi", ".mkv", ".webm", ".m4v"}


@dataclass(frozen=True)
class ImageInfo:
    """Basic image metadata returned by loaders."""

    path: str
    width: int
    height: int
    mode: str


class MediaLoadError(RuntimeError):
    """Raised when a media file cannot be loaded."""


class VideoDependencyError(RuntimeError):
    """Raised when video decoding is requested without OpenCV."""


def resolve_path(path: str | Path) -> Path:
    """Resolve a filesystem path to an absolute, expanded path."""

    return Path(path).expanduser().resolve()


def ensure_dir(path: str | Path) -> Path:
    """Ensure a directory exists and return the resolved path."""

    target = resolve_path(path)
    target.mkdir(parents=True, exist_ok=True)
    return target


def is_image_path(path: str | Path) -> bool:
    """Return True if the path appears to be an image file."""

    return Path(path).suffix.lower() in IMAGE_EXTS


def is_video_path(path: str | Path) -> bool:
    """Return True if the path appears to be a video file."""

    return Path(path).suffix.lower() in VIDEO_EXTS


def normalize_image(
    image: Image.Image,
    *,
    max_side: Optional[int] = None,
    target_size: Optional[Tuple[int, int]] = None,
) -> Image.Image:
    """Normalize an image (EXIF orientation + optional resize)."""

    normalized = ImageOps.exif_transpose(image)
    if target_size is not None:
        return normalized.resize(target_size, resample=Image.Resampling.LANCZOS)

    if max_side is None:
        return normalized

    w, h = normalized.size
    scale = max(w, h) / float(max_side)
    if scale <= 1.0:
        return normalized

    new_w = max(1, int(round(w / scale)))
    new_h = max(1, int(round(h / scale)))
    return normalized.resize((new_w, new_h), resample=Image.Resampling.LANCZOS)


def load_image(
    path: str | Path,
    *,
    max_side: Optional[int] = None,
    require_rgb: bool = True,
) -> Tuple[Image.Image, ImageInfo]:
    """Load an image from disk and return the image + metadata."""

    resolved = resolve_path(path)
    if not resolved.exists():
        raise MediaLoadError(f"Image not found: {resolved}")

    try:
        image = Image.open(resolved)
        image.load()
    except Exception as exc:
        raise MediaLoadError(f"Failed to open image: {resolved}") from exc

    image = normalize_image(image, max_side=max_side)
    if require_rgb and image.mode != "RGB":
        image = image.convert("RGB")

    info = ImageInfo(path=str(resolved), width=image.width, height=image.height, mode=image.mode)
    return image, info


def pil_to_np(image: Image.Image) -> np.ndarray:
    """Convert a PIL image to an RGB numpy array."""

    if image.mode != "RGB":
        image = image.convert("RGB")
    return np.asarray(image, dtype=np.uint8)


def np_to_pil(array: np.ndarray) -> Image.Image:
    """Convert a numpy array to a PIL image."""

    if array.dtype != np.uint8:
        array = np.clip(array, 0, 255).astype(np.uint8)
    if array.ndim == 2:
        return Image.fromarray(array, mode="L")
    return Image.fromarray(array, mode="RGB")


def load_video_frames(
    path: str | Path,
    *,
    max_frames: int = 1,
    stride: int = 1,
    max_side: Optional[int] = None,
) -> List[Tuple[np.ndarray, ImageInfo]]:
    """Load frames from a video using OpenCV (RGB arrays)."""

    if cv2 is None:
        raise VideoDependencyError("OpenCV is required for video loading.")

    resolved = resolve_path(path)
    if not resolved.exists():
        raise MediaLoadError(f"Video not found: {resolved}")

    cap = cv2.VideoCapture(str(resolved))
    if not cap.isOpened():
        raise MediaLoadError(f"Failed to open video: {resolved}")

    frames: List[Tuple[np.ndarray, ImageInfo]] = []
    idx = 0
    collected = 0
    try:
        while collected < max_frames:
            ret, frame_bgr = cap.read()
            if not ret:
                break
            if idx % stride != 0:
                idx += 1
                continue
            frame_rgb = cv2.cvtColor(frame_bgr, cv2.COLOR_BGR2RGB)
            pil_img = Image.fromarray(frame_rgb)
            pil_img = normalize_image(pil_img, max_side=max_side)
            frame_rgb = np.asarray(pil_img, dtype=np.uint8)
            info = ImageInfo(path=str(resolved), width=pil_img.width, height=pil_img.height, mode="RGB")
            frames.append((frame_rgb, info))
            collected += 1
            idx += 1
    finally:
        cap.release()

    if not frames:
        raise MediaLoadError(f"No frames decoded from video: {resolved}")

    return frames


def load_media(
    path: str | Path,
    *,
    max_frames: int = 1,
    stride: int = 1,
    max_side: Optional[int] = None,
) -> List[Tuple[np.ndarray, ImageInfo]]:
    """Load an image or video and return a list of RGB frames."""

    if is_video_path(path):
        return load_video_frames(path, max_frames=max_frames, stride=stride, max_side=max_side)

    image, info = load_image(path, max_side=max_side)
    return [(pil_to_np(image), info)]


def save_image(image: Image.Image, path: str | Path) -> str:
    """Save a PIL image to disk and return the path string."""

    target = resolve_path(path)
    ensure_dir(target.parent)
    image.save(target)
    return str(target)


def save_array(array: np.ndarray, path: str | Path) -> str:
    """Save a numpy array as an image file and return the path string."""

    image = np_to_pil(array)
    return save_image(image, path)


def batch_paths(paths: Sequence[str | Path]) -> List[Path]:
    """Resolve a list of paths, ignoring empty strings."""

    resolved: List[Path] = []
    for item in paths:
        if str(item).strip():
            resolved.append(resolve_path(item))
    return resolved
