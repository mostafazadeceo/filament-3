from __future__ import annotations

"""Risk scoring and explainability logic."""

from dataclasses import dataclass, field
from typing import Any, Dict, List, Optional

import json
from pathlib import Path

from .quality import QualityResult
from .doc_forensics import DocForensicsResult
from .selfie_forensics import SelfieForensicsResult
from .recapture import RecaptureResult

try:
    import yaml  # type: ignore
except Exception:
    yaml = None


DEFAULT_THRESHOLDS: Dict[str, float] = {
    "blur_variance": 60.0,
    "noise_estimate": 12.0,
    "saturation_ratio": 0.08,
    "blockiness": 0.35,
    "ela_p95": 24.0,
    "resampling_score": 0.5,
    "copy_move_score": 0.4,
    "edge_inconsistency": 0.6,
    "lighting_inconsistency": 0.35,
    "face_ratio": 0.08,
    "pose_score": 0.22,
    "recapture_score": 0.5,
}

DEFAULT_WEIGHTS: Dict[str, float] = {
    "doc_blur": 0.10,
    "doc_noise": 0.06,
    "doc_saturation": 0.05,
    "doc_blockiness": 0.06,
    "doc_ela": 0.12,
    "doc_resampling": 0.10,
    "doc_copy_move": 0.10,
    "doc_edge_inconsistency": 0.06,
    "doc_lighting_inconsistency": 0.05,
    "selfie_face_missing": 0.12,
    "selfie_face_small": 0.06,
    "selfie_pose": 0.06,
    "selfie_recapture": 0.06,
    "selfie_blur": 0.05,
    "selfie_noise": 0.04,
    "selfie_saturation": 0.03,
    "selfie_blockiness": 0.04,
    "doc_recapture": 0.05,
}


@dataclass
class RiskAssessment:
    """Risk score and explainability data."""

    score: float
    reasons: List[str] = field(default_factory=list)
    signals: Dict[str, float] = field(default_factory=dict)
    weights: Dict[str, float] = field(default_factory=dict)
    thresholds: Dict[str, float] = field(default_factory=dict)


def _load_config(path: Optional[str]) -> Dict[str, Dict[str, float]]:
    if path is None:
        return {"weights": DEFAULT_WEIGHTS, "thresholds": DEFAULT_THRESHOLDS}
    resolved = Path(path).expanduser().resolve()
    if not resolved.exists():
        raise FileNotFoundError(f"Weights config not found: {resolved}")
    content = resolved.read_text(encoding="utf-8")
    if resolved.suffix.lower() in {".yaml", ".yml"}:
        if yaml is None:
            raise RuntimeError("PyYAML is required to load YAML configs.")
        data = yaml.safe_load(content)
    else:
        data = json.loads(content)
    weights = data.get("weights", {})
    thresholds = data.get("thresholds", {})
    merged_weights = dict(DEFAULT_WEIGHTS)
    merged_weights.update({k: float(v) for k, v in weights.items()})
    merged_thresholds = dict(DEFAULT_THRESHOLDS)
    merged_thresholds.update({k: float(v) for k, v in thresholds.items()})
    return {"weights": merged_weights, "thresholds": merged_thresholds}


def _normalize_score(score: float) -> float:
    return float(max(0.0, min(100.0, score)))


def _risk_from_values(weights: Dict[str, float], signals: Dict[str, float]) -> float:
    total_weight = sum(weights.values()) or 1.0
    weighted = 0.0
    for key, weight in weights.items():
        weighted += weight * max(0.0, min(1.0, signals.get(key, 0.0)))
    return (weighted / total_weight) * 100.0


def compute_risk_score(
    quality_doc: QualityResult,
    doc_forensics: DocForensicsResult,
    recapture_doc: RecaptureResult,
    *,
    quality_selfie: Optional[QualityResult] = None,
    selfie_forensics: Optional[SelfieForensicsResult] = None,
    recapture_selfie: Optional[RecaptureResult] = None,
    weights_path: Optional[str] = None,
) -> RiskAssessment:
    """Compute a risk score from signals with configurable weights."""

    cfg = _load_config(weights_path)
    weights = cfg["weights"]
    thresholds = cfg["thresholds"]

    signals: Dict[str, float] = {}
    signals["doc_blur"] = 1.0 if quality_doc.blur_variance < thresholds["blur_variance"] else 0.0
    signals["doc_noise"] = min(1.0, quality_doc.noise_estimate / thresholds["noise_estimate"])
    signals["doc_saturation"] = min(1.0, quality_doc.saturation_ratio / thresholds["saturation_ratio"])
    signals["doc_blockiness"] = min(1.0, quality_doc.blockiness / thresholds["blockiness"])
    signals["doc_ela"] = 1.0 if doc_forensics.ela_p95 > thresholds["ela_p95"] else 0.0
    signals["doc_resampling"] = min(1.0, doc_forensics.resampling_score / thresholds["resampling_score"])
    signals["doc_copy_move"] = min(1.0, doc_forensics.copy_move_score / thresholds["copy_move_score"])
    signals["doc_edge_inconsistency"] = min(
        1.0, doc_forensics.edge_inconsistency / thresholds["edge_inconsistency"]
    )
    signals["doc_lighting_inconsistency"] = min(
        1.0, doc_forensics.lighting_inconsistency / thresholds["lighting_inconsistency"]
    )
    signals["doc_recapture"] = min(1.0, recapture_doc.moire_score / thresholds["recapture_score"])

    if selfie_forensics is not None:
        signals["selfie_face_missing"] = 1.0 if not selfie_forensics.face_detected else 0.0
        signals["selfie_face_small"] = (
            1.0 if selfie_forensics.face_ratio < thresholds["face_ratio"] else 0.0
        )
        pose_signal = max(selfie_forensics.yaw_score, selfie_forensics.pitch_score)
        signals["selfie_pose"] = min(1.0, pose_signal / thresholds["pose_score"])
    if quality_selfie is not None:
        signals["selfie_blur"] = 1.0 if quality_selfie.blur_variance < thresholds["blur_variance"] else 0.0
        signals["selfie_noise"] = min(1.0, quality_selfie.noise_estimate / thresholds["noise_estimate"])
        signals["selfie_saturation"] = min(1.0, quality_selfie.saturation_ratio / thresholds["saturation_ratio"])
        signals["selfie_blockiness"] = min(1.0, quality_selfie.blockiness / thresholds["blockiness"])

    if recapture_selfie is not None:
        signals["selfie_recapture"] = min(1.0, recapture_selfie.moire_score / thresholds["recapture_score"])

    score = _normalize_score(_risk_from_values(weights, signals))

    reasons: List[str] = []
    for key, value in signals.items():
        if value >= 0.5:
            reasons.append(f"{key}:{value:.2f}")

    return RiskAssessment(
        score=score,
        reasons=reasons,
        signals=signals,
        weights=weights,
        thresholds=thresholds,
    )
