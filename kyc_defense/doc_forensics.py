from __future__ import annotations

"""Document forensics heuristics for defensive KYC research."""

from dataclasses import dataclass, field
from datetime import datetime
from typing import Dict, List, Optional, Tuple

import numpy as np
from PIL import Image, ImageChops, ExifTags

from .io import save_image

try:
    import cv2  # type: ignore
except Exception:
    cv2 = None


SUSPICIOUS_EXIF_TAGS = {
    "Software",
    "ProcessingSoftware",
    "DocumentName",
    "ImageDescription",
    "Artist",
}


@dataclass
class DocForensicsResult:
    """Forensics indicators derived from a document image."""

    exif_present: bool
    exif_suspicious: List[str]
    exif_summary: Dict[str, Optional[str]]
    exif_warnings: List[str]
    exif_has_gps: bool
    ela_mean: float
    ela_p95: float
    resampling_score: float
    blockiness: float
    copy_move_score: float
    edge_inconsistency: float
    lighting_inconsistency: float
    issues: List[str] = field(default_factory=list)
    ela_path: Optional[str] = None


def _to_gray_np(image: Image.Image) -> np.ndarray:
    if image.mode != "L":
        image = image.convert("L")
    return np.asarray(image, dtype=np.float32)


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


def extract_exif(image: Image.Image) -> Dict[str, str]:
    """Extract EXIF tags from a PIL image."""

    try:
        exif = image.getexif()
    except Exception:
        return {}

    tags: Dict[str, str] = {}
    for key, value in exif.items():
        name = ExifTags.TAGS.get(key, str(key))
        try:
            tags[name] = str(value)
        except Exception:
            tags[name] = "<unreadable>"
    return tags


def detect_suspicious_exif(exif: Dict[str, str]) -> List[str]:
    """Return a list of suspicious EXIF tag names/values."""

    hits: List[str] = []
    for tag, value in exif.items():
        if tag in SUSPICIOUS_EXIF_TAGS:
            hits.append(f"{tag}:{value}")
        if "photoshop" in value.lower() or "gimp" in value.lower() or "affinity" in value.lower():
            hits.append(f"software:{value}")
    return hits


def summarize_exif(exif: Dict[str, str]) -> Dict[str, Optional[str]]:
    """Return a normalized subset of EXIF tags."""

    keys = [
        "Make",
        "Model",
        "Software",
        "ProcessingSoftware",
        "DateTimeOriginal",
        "CreateDate",
        "ModifyDate",
        "DateTime",
        "Orientation",
        "Artist",
        "GPSInfo",
    ]
    summary: Dict[str, Optional[str]] = {}
    for key in keys:
        if key in exif:
            summary[key] = exif.get(key)
    return summary


def _parse_exif_datetime(value: Optional[str]) -> Optional[datetime]:
    if not value:
        return None
    for fmt in ("%Y:%m:%d %H:%M:%S", "%Y-%m-%d %H:%M:%S"):
        try:
            return datetime.strptime(value, fmt)
        except Exception:
            continue
    return None


def analyze_exif(exif: Dict[str, str]) -> Tuple[List[str], bool]:
    """Analyze EXIF for warnings and GPS presence."""

    warnings: List[str] = []
    if not exif:
        warnings.append("missing_exif")
        return warnings, False

    summary = summarize_exif(exif)
    if not any(key in summary for key in ("DateTimeOriginal", "CreateDate", "ModifyDate", "DateTime")):
        warnings.append("missing_datetime")
    if any(key in summary for key in ("Software", "ProcessingSoftware")):
        warnings.append("software_tag_present")

    has_gps = "GPSInfo" in summary or any(key.startswith("GPS") for key in exif)
    if has_gps:
        warnings.append("gps_present")

    original_dt = _parse_exif_datetime(summary.get("DateTimeOriginal") or summary.get("DateTime"))
    modify_dt = _parse_exif_datetime(summary.get("ModifyDate") or summary.get("CreateDate"))
    if original_dt and modify_dt:
        delta = abs((modify_dt - original_dt).total_seconds())
        if delta > 86400:
            warnings.append("datetime_mismatch")

    return warnings, has_gps


def ela_heatmap(
    image: Image.Image,
    *,
    quality: int = 90,
    save_path: Optional[str] = None,
) -> Tuple[Image.Image, float, float, Optional[str]]:
    """Generate an ELA heatmap and return stats + optional saved path."""

    if image.mode != "RGB":
        image = image.convert("RGB")

    from io import BytesIO

    buffer = BytesIO()
    image.save(buffer, format="JPEG", quality=quality)
    buffer.seek(0)
    compressed = Image.open(buffer)
    compressed.load()

    diff = ImageChops.difference(image, compressed)
    diff_gray = diff.convert("L")
    diff_np = np.asarray(diff_gray, dtype=np.float32)

    max_val = float(np.max(diff_np)) if diff_np.size else 1.0
    scale = 255.0 / max(1.0, max_val)
    heatmap = diff.point(lambda p: min(255, int(round(p * scale))))

    mean = float(np.mean(diff_np))
    p95 = float(np.percentile(diff_np, 95))

    saved = None
    if save_path is not None:
        saved = save_image(heatmap, save_path)

    return heatmap, mean, p95, saved


def resampling_score(image: Image.Image) -> float:
    """Heuristic resampling score based on FFT peak ratios."""

    gray = _to_gray_np(image)
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
    ratio = (peak - median) / max(1e-6, median)
    return float(min(1.0, ratio / 12.0))


def copy_move_score(image: Image.Image) -> float:
    """Naive copy-move score using AKAZE/ORB keypoints if available."""

    if cv2 is None:
        return 0.0
    gray = _to_gray_np(image).astype(np.uint8)
    detector = None
    for creator in (getattr(cv2, "AKAZE_create", None), getattr(cv2, "ORB_create", None)):
        if creator is None:
            continue
        detector = creator()
        break
    if detector is None:
        return 0.0

    kps, des = detector.detectAndCompute(gray, None)
    if des is None or len(kps) < 8:
        return 0.0

    matcher = cv2.BFMatcher(cv2.NORM_HAMMING, crossCheck=False)
    matches = matcher.knnMatch(des, des, k=2)
    suspicious = 0
    total = 0
    for pair in matches:
        if len(pair) < 2:
            continue
        m, n = pair
        if m.queryIdx == m.trainIdx:
            continue
        total += 1
        if m.distance < 0.75 * n.distance:
            p1 = np.array(kps[m.queryIdx].pt)
            p2 = np.array(kps[m.trainIdx].pt)
            if np.linalg.norm(p1 - p2) > 12.0:
                suspicious += 1
    if total == 0:
        return 0.0
    ratio = suspicious / float(total)
    return float(min(1.0, ratio * 3.0))


def edge_inconsistency(image: Image.Image) -> float:
    """Edge inconsistency across quadrants."""

    gray = _to_gray_np(image).astype(np.uint8)
    if cv2 is not None:
        edges = cv2.Canny(gray, 80, 160)
    else:
        gx = np.abs(np.diff(gray.astype(np.float32), axis=1, prepend=gray[:, :1]))
        gy = np.abs(np.diff(gray.astype(np.float32), axis=0, prepend=gray[:1, :]))
        edges = ((gx + gy) > 40).astype(np.uint8) * 255

    h, w = edges.shape
    h2, w2 = h // 2, w // 2
    quads = [
        edges[:h2, :w2],
        edges[:h2, w2:],
        edges[h2:, :w2],
        edges[h2:, w2:],
    ]
    densities = [float(np.mean(q > 0)) for q in quads if q.size]
    if not densities:
        return 0.0
    mean = float(np.mean(densities))
    if mean <= 1e-6:
        return 0.0
    return float(min(1.0, np.std(densities) / mean))


def lighting_inconsistency(image: Image.Image) -> float:
    """Estimate lighting mismatch across quadrants based on luminance means."""

    gray = _to_gray_np(image)
    h, w = gray.shape
    h2, w2 = h // 2, w // 2
    quads = [
        gray[:h2, :w2],
        gray[:h2, w2:],
        gray[h2:, :w2],
        gray[h2:, w2:],
    ]
    means = [float(np.mean(q)) for q in quads if q.size]
    if not means:
        return 0.0
    mean = float(np.mean(means))
    if mean <= 1e-6:
        return 0.0
    return float(min(1.0, np.std(means) / mean))


def analyze_document_forensics(
    image: Image.Image,
    *,
    save_ela_path: Optional[str] = None,
) -> DocForensicsResult:
    """Run document forensics heuristics and return indicators."""

    exif = extract_exif(image)
    suspicious = detect_suspicious_exif(exif)
    exif_summary = summarize_exif(exif)
    exif_warnings, exif_has_gps = analyze_exif(exif)
    _, ela_mean, ela_p95, saved = ela_heatmap(image, save_path=save_ela_path)
    resample = resampling_score(image)
    blockiness = _blockiness(_to_gray_np(image))
    copy_move = copy_move_score(image)
    edge_inc = edge_inconsistency(image)
    light_inc = lighting_inconsistency(image)

    issues: List[str] = []
    if suspicious:
        issues.append("suspicious_exif")
    if ela_p95 > 24.0:
        issues.append("ela_high")
    if resample > 0.5:
        issues.append("resampling_artifacts")
    if copy_move > 0.4:
        issues.append("copy_move_pattern")
    if edge_inc > 0.6:
        issues.append("edge_inconsistency")
    if light_inc > 0.35:
        issues.append("lighting_inconsistency")

    return DocForensicsResult(
        exif_present=bool(exif),
        exif_suspicious=suspicious,
        exif_summary=exif_summary,
        exif_warnings=exif_warnings,
        exif_has_gps=exif_has_gps,
        ela_mean=ela_mean,
        ela_p95=ela_p95,
        resampling_score=resample,
        blockiness=blockiness,
        copy_move_score=copy_move,
        edge_inconsistency=edge_inc,
        lighting_inconsistency=light_inc,
        issues=issues,
        ela_path=saved,
    )
