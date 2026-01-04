from __future__ import annotations

"""KYC defense research toolkit (defensive analysis only)."""

from dataclasses import dataclass
from pathlib import Path
from typing import Any, Dict, Optional

from PIL import Image

from .io import ImageInfo, load_image, ensure_dir
from .quality import QualityResult, assess_quality
from .doc_forensics import DocForensicsResult, analyze_document_forensics
from .selfie_forensics import SelfieForensicsResult, analyze_selfie_forensics
from .recapture import RecaptureResult, compute_recapture_signals
from .scoring import RiskAssessment, compute_risk_score
from .reporting import build_report, save_html_report, send_preview_to_virtualcam
from .annotations import generate_document_issue_overlay

__all__ = [
    "AnalysisOptions",
    "analyze_pair",
    "analyze_document",
    "analyze_selfie",
]


@dataclass
class AnalysisOptions:
    """Options for analysis and report generation."""

    weights_path: Optional[str] = None
    artifacts_dir: Optional[str] = None
    html_report_path: Optional[str] = None
    virtualcam_preview: bool = False


def _safe_path(base: Optional[str], filename: str) -> Optional[str]:
    if base is None:
        return None
    target = ensure_dir(base)
    return str(Path(target) / filename)


def _empty_doc_forensics() -> DocForensicsResult:
    return DocForensicsResult(
        exif_present=False,
        exif_suspicious=[],
        exif_summary={},
        exif_warnings=[],
        exif_has_gps=False,
        ela_mean=0.0,
        ela_p95=0.0,
        resampling_score=0.0,
        blockiness=0.0,
        copy_move_score=0.0,
        edge_inconsistency=0.0,
        lighting_inconsistency=0.0,
        issues=[],
        ela_path=None,
    )


def _empty_recapture() -> RecaptureResult:
    return RecaptureResult(
        moire_score=0.0,
        aliasing_score=0.0,
        fft_peak_ratio=0.0,
        edge_repeat_score=0.0,
        issues=[],
    )


def analyze_document(
    doc_path: str,
    *,
    options: Optional[AnalysisOptions] = None,
) -> Dict[str, Any]:
    """Analyze a single document image."""

    return analyze_pair(doc_path=doc_path, selfie_path=None, options=options)


def analyze_selfie(
    selfie_path: str,
    *,
    options: Optional[AnalysisOptions] = None,
) -> Dict[str, Any]:
    """Analyze a single selfie image (document fields are left empty)."""

    return analyze_pair(doc_path=selfie_path, selfie_path=selfie_path, options=options)


def analyze_pair(
    *,
    doc_path: str,
    selfie_path: Optional[str] = None,
    options: Optional[AnalysisOptions] = None,
) -> Dict[str, Any]:
    """Analyze a document + optional selfie pair and return a report dict."""

    opts = options or AnalysisOptions()
    errors: list[str] = []
    artifacts: Dict[str, Any] = {}

    doc_image: Optional[Image.Image] = None
    doc_info: Optional[ImageInfo] = None
    selfie_image: Optional[Image.Image] = None
    selfie_info: Optional[ImageInfo] = None

    try:
        doc_image, doc_info = load_image(doc_path)
    except Exception as exc:
        raise RuntimeError(f"Failed to load document image: {doc_path}") from exc

    if selfie_path:
        try:
            selfie_image, selfie_info = load_image(selfie_path)
        except Exception as exc:
            errors.append(f"selfie_load_failed:{exc}")

    inputs = {
        "document": {
            "path": doc_info.path,
            "width": doc_info.width,
            "height": doc_info.height,
            "mode": doc_info.mode,
        },
        "selfie": (
            {
                "path": selfie_info.path,
                "width": selfie_info.width,
                "height": selfie_info.height,
                "mode": selfie_info.mode,
            }
            if selfie_info
            else None
        ),
    }

    quality_doc = assess_quality(doc_image)
    doc_ela_path = _safe_path(opts.artifacts_dir, "ela_heatmap.png")
    try:
        doc_forensics = analyze_document_forensics(doc_image, save_ela_path=doc_ela_path)
    except Exception as exc:
        errors.append(f"doc_forensics_failed:{exc}")
        doc_forensics = _empty_doc_forensics()
    try:
        recapture_doc = compute_recapture_signals(doc_image)
    except Exception as exc:
        errors.append(f"doc_recapture_failed:{exc}")
        recapture_doc = _empty_recapture()

    doc_issue_overlay_saved = None
    doc_issue_overlay_path = _safe_path(opts.artifacts_dir, "doc_issue_overlay.png")
    if doc_issue_overlay_path:
        try:
            doc_issue_overlay_saved = generate_document_issue_overlay(
                doc_image,
                ela_path=doc_forensics.ela_path,
                output_path=doc_issue_overlay_path,
            )
        except Exception as exc:
            errors.append(f"doc_issue_overlay_failed:{exc}")
            doc_issue_overlay_saved = None

    quality_selfie: Optional[QualityResult] = None
    selfie_forensics: Optional[SelfieForensicsResult] = None
    recapture_selfie: Optional[RecaptureResult] = None
    if selfie_image is not None:
        quality_selfie = assess_quality(selfie_image)
        selfie_preview_path = _safe_path(opts.artifacts_dir, "selfie_preview.png")
        try:
            selfie_forensics = analyze_selfie_forensics(selfie_image, save_preview_path=selfie_preview_path)
        except Exception as exc:
            errors.append(f"selfie_forensics_failed:{exc}")
            selfie_forensics = None
        try:
            recapture_selfie = compute_recapture_signals(selfie_image)
        except Exception as exc:
            errors.append(f"selfie_recapture_failed:{exc}")
            recapture_selfie = None
    else:
        selfie_preview_path = None

    artifacts["ela_heatmap_path"] = doc_forensics.ela_path
    artifacts["doc_issue_overlay_path"] = doc_issue_overlay_saved
    artifacts["selfie_preview_path"] = selfie_preview_path

    risk = compute_risk_score(
        quality_doc,
        doc_forensics,
        recapture_doc,
        quality_selfie=quality_selfie,
        selfie_forensics=selfie_forensics,
        recapture_selfie=recapture_selfie,
        weights_path=opts.weights_path,
    )

    quality: Dict[str, Any] = {"document": quality_doc}
    if quality_selfie is not None:
        quality["selfie"] = quality_selfie

    recapture = {"document": recapture_doc}
    if recapture_selfie is not None:
        recapture["selfie"] = recapture_selfie

    report = build_report(
        inputs=inputs,
        quality=quality,
        doc_forensics=doc_forensics,
        selfie_forensics=selfie_forensics,
        recapture=recapture,
        risk=risk,
        artifacts=artifacts,
        errors=errors,
    )

    if opts.html_report_path:
        try:
            save_html_report(report, opts.html_report_path)
        except Exception as exc:
            errors.append(f"html_report_failed:{exc}")

    if opts.virtualcam_preview and selfie_image is not None:
        try:
            send_preview_to_virtualcam(selfie_image)
        except Exception as exc:
            errors.append(f"virtualcam_failed:{exc}")

    return report
