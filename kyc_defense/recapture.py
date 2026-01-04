from __future__ import annotations

"""Recapture/screenshot heuristics for defensive KYC research."""

from dataclasses import dataclass, field
from typing import List

import numpy as np
from PIL import Image

try:
    import cv2  # type: ignore
except Exception:
    cv2 = None


@dataclass
class RecaptureResult:
    """Signals that may indicate recapture or screen replay."""

    moire_score: float
    aliasing_score: float
    fft_peak_ratio: float
    edge_repeat_score: float
    issues: List[str] = field(default_factory=list)


def _to_gray_np(image: Image.Image) -> np.ndarray:
    if image.mode != "L":
        image = image.convert("L")
    return np.asarray(image, dtype=np.float32)


def _fft_peak_ratio(gray: np.ndarray) -> float:
    if gray.size == 0:
        return 0.0
    if cv2 is not None:
        blur = cv2.GaussianBlur(gray, (5, 5), 0)
    else:
        blur = gray
    hp = gray - blur
    fft = np.fft.fftshift(np.fft.fft2(hp))
    mag = np.abs(fft)
    h, w = mag.shape
    cy, cx = h // 2, w // 2
    mask = np.ones_like(mag, dtype=bool)
    mask[max(cy - 10, 0) : min(cy + 10, h), max(cx - 10, 0) : min(cx + 10, w)] = False
    values = mag[mask]
    if values.size == 0:
        return 0.0
    median = float(np.median(values))
    peak = float(np.max(values))
    return (peak - median) / max(1e-6, median)


def _aliasing_score(gray: np.ndarray) -> float:
    h, w = gray.shape
    if h < 32 or w < 32:
        return 0.0
    if cv2 is not None:
        down = cv2.resize(gray, (w // 2, h // 2), interpolation=cv2.INTER_AREA)
        up = cv2.resize(down, (w, h), interpolation=cv2.INTER_NEAREST)
    else:
        down = gray[::2, ::2]
        up = np.repeat(np.repeat(down, 2, axis=0), 2, axis=1)[:h, :w]
    diff = np.abs(gray - up)
    score = float(np.mean(diff) / 255.0)
    return float(min(1.0, score * 3.0))


def _edge_repeat_score(gray: np.ndarray) -> float:
    if cv2 is not None:
        edges = cv2.Canny(gray.astype(np.uint8), 80, 160)
    else:
        gx = np.abs(np.diff(gray, axis=1, prepend=gray[:, :1]))
        gy = np.abs(np.diff(gray, axis=0, prepend=gray[:1, :]))
        edges = ((gx + gy) > 40).astype(np.uint8)
    if edges.size == 0:
        return 0.0
    edges = edges.astype(np.float32)
    max_corr = 0.0
    for shift in range(2, 9):
        shifted = np.roll(edges, shift, axis=1)
        corr = float(np.mean(edges * shifted))
        max_corr = max(max_corr, corr)
    return float(min(1.0, max_corr * 5.0))


def compute_recapture_signals(image: Image.Image) -> RecaptureResult:
    """Compute recapture-related signals from a single image."""

    gray = _to_gray_np(image)
    peak_ratio = _fft_peak_ratio(gray)
    moire = float(min(1.0, peak_ratio / 12.0))
    aliasing = _aliasing_score(gray)
    edge_repeat = _edge_repeat_score(gray)

    issues: List[str] = []
    if moire > 0.5:
        issues.append("moire_pattern")
    if aliasing > 0.5:
        issues.append("aliasing")
    if edge_repeat > 0.5:
        issues.append("edge_repeat")

    return RecaptureResult(
        moire_score=moire,
        aliasing_score=aliasing,
        fft_peak_ratio=peak_ratio,
        edge_repeat_score=edge_repeat,
        issues=issues,
    )
