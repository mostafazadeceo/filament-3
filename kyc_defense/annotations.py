from __future__ import annotations

"""Issue overlay generation for document images."""

from typing import Dict, List, Optional, Tuple

import numpy as np
from PIL import Image, ImageDraw

try:
    import cv2  # type: ignore
except Exception:
    cv2 = None


def _to_gray_np(image: Image.Image) -> np.ndarray:
    if image.mode != "L":
        image = image.convert("L")
    return np.asarray(image, dtype=np.float32)


RESAMPLE = Image.Resampling.LANCZOS if hasattr(Image, "Resampling") else Image.LANCZOS


def _downscale(image: Image.Image, max_side: int) -> Image.Image:
    if max_side <= 0:
        return image
    w, h = image.size
    scale = max(w, h) / float(max_side)
    if scale <= 1.0:
        return image
    new_w = max(1, int(round(w / scale)))
    new_h = max(1, int(round(h / scale)))
    return image.resize((new_w, new_h), resample=RESAMPLE)


def _blur(gray: np.ndarray, ksize: int = 5) -> np.ndarray:
    if cv2 is not None:
        return cv2.GaussianBlur(gray, (ksize, ksize), 0)
    padded = np.pad(gray, ksize // 2, mode="reflect")
    acc = np.zeros_like(gray)
    for dy in range(ksize):
        for dx in range(ksize):
            acc += padded[dy : dy + gray.shape[0], dx : dx + gray.shape[1]]
    return acc / float(ksize * ksize)


def _saturation_mask(gray: np.ndarray) -> np.ndarray:
    high = gray >= 250
    low = gray <= 5
    return high | low


def _noise_mask(gray: np.ndarray) -> np.ndarray:
    blur = _blur(gray, 5)
    residual = gray - blur
    energy = residual * residual
    energy_blur = _blur(energy, 9)
    threshold = float(np.percentile(energy_blur, 95)) if energy_blur.size else 0.0
    if threshold < 8.0:
        return np.zeros_like(gray, dtype=bool)
    return energy_blur >= threshold


def _blockiness_mask(gray: np.ndarray, block: int = 8) -> np.ndarray:
    h, w = gray.shape
    if h < block * 2 or w < block * 2:
        return np.zeros_like(gray, dtype=bool)
    hb = h // block
    wb = w // block
    trimmed = gray[: hb * block, : wb * block]
    blocks = trimmed.reshape(hb, block, wb, block).mean(axis=(1, 3))
    diff_right = np.abs(blocks[:, 1:] - blocks[:, :-1])
    diff_down = np.abs(blocks[1:, :] - blocks[:-1, :])
    scores = np.zeros_like(blocks)
    if diff_right.size:
        scores[:, 1:] += diff_right
        scores[:, :-1] += diff_right
    if diff_down.size:
        scores[1:, :] += diff_down
        scores[:-1, :] += diff_down
    scores /= 2.0
    threshold = float(np.percentile(scores, 90)) if scores.size else 0.0
    if threshold < 6.0:
        return np.zeros_like(gray, dtype=bool)
    mask_blocks = scores >= threshold
    mask = np.repeat(np.repeat(mask_blocks, block, axis=0), block, axis=1)
    full = np.zeros_like(gray, dtype=bool)
    full[: mask.shape[0], : mask.shape[1]] = mask
    return full


def _ela_mask(ela_image: Optional[Image.Image]) -> np.ndarray:
    if ela_image is None:
        return np.zeros((1, 1), dtype=bool)
    gray = _to_gray_np(ela_image)
    threshold = float(np.percentile(gray, 95)) if gray.size else 0.0
    if threshold < 10.0:
        return np.zeros_like(gray, dtype=bool)
    return gray >= threshold


def _points_from_mask(mask: np.ndarray, step: int, coverage: float) -> List[Tuple[int, int]]:
    h, w = mask.shape
    points: List[Tuple[int, int]] = []
    for y in range(0, h, step):
        for x in range(0, w, step):
            patch = mask[y : y + step, x : x + step]
            if patch.size == 0:
                continue
            if float(np.mean(patch)) >= coverage:
                points.append((x + step // 2, y + step // 2))
    return points


def generate_document_issue_overlay(
    image: Image.Image,
    *,
    ela_path: Optional[str] = None,
    output_path: Optional[str] = None,
    max_side: int = 1200,
) -> Optional[str]:
    """Generate an overlay marking problematic regions with circles."""

    if image.mode != "RGB":
        image = image.convert("RGB")
    base = _downscale(image, max_side)
    gray = _to_gray_np(base)

    ela_image = None
    if ela_path:
        try:
            ela_image = Image.open(ela_path)
            ela_image = _downscale(ela_image, max_side)
        except Exception:
            ela_image = None

    masks: Dict[str, np.ndarray] = {
        "saturation": _saturation_mask(gray),
        "noise": _noise_mask(gray),
        "blockiness": _blockiness_mask(gray),
    }
    ela_mask = _ela_mask(ela_image)
    if ela_mask.shape == gray.shape:
        masks["ela"] = ela_mask

    colors = {
        "saturation": (210, 45, 45),
        "noise": (242, 145, 0),
        "blockiness": (110, 86, 210),
        "ela": (32, 120, 200),
    }

    step = max(24, min(base.size) // 12)
    overlay = base.copy()
    draw = ImageDraw.Draw(overlay)
    any_points = False

    for key, mask in masks.items():
        if mask.shape != gray.shape:
            continue
        points = _points_from_mask(mask, step=step, coverage=0.25)
        if not points:
            continue
        any_points = True
        radius = max(8, step // 3)
        color = colors.get(key, (255, 0, 0))
        for x, y in points:
            draw.ellipse((x - radius, y - radius, x + radius, y + radius), outline=color, width=3)

    if not any_points:
        return None

    if output_path is not None:
        overlay.save(output_path)
        return output_path

    return None
