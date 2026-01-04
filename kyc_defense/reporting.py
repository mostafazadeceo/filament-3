from __future__ import annotations

"""Report building and serialization utilities."""

from dataclasses import asdict, is_dataclass
from pathlib import Path
from typing import Any, Dict, Optional

import json
import numpy as np
from PIL import Image

try:
    import jsonschema  # type: ignore
except Exception:
    jsonschema = None

try:
    import pyvirtualcam  # type: ignore
except Exception:
    pyvirtualcam = None


REPORT_VERSION = "0.1.0"

REPORT_SCHEMA: Dict[str, Any] = {
    "$schema": "http://json-schema.org/draft-07/schema#",
    "title": "KYCDefenseReport",
    "type": "object",
    "required": [
        "version",
        "inputs",
        "quality",
        "doc_forensics",
        "selfie_forensics",
        "recapture",
        "risk",
        "artifacts",
        "errors",
    ],
    "properties": {
        "version": {"type": "string"},
        "inputs": {"type": "object"},
        "quality": {"type": "object"},
        "doc_forensics": {"type": "object"},
        "selfie_forensics": {"type": ["object", "null"]},
        "recapture": {"type": "object"},
        "risk": {"type": "object"},
        "artifacts": {"type": "object"},
        "errors": {"type": "array"},
    },
}


def _serialize(obj: Any) -> Any:
    if obj is None:
        return None
    if is_dataclass(obj):
        return asdict(obj)
    if isinstance(obj, Path):
        return str(obj)
    if isinstance(obj, (list, tuple)):
        return [ _serialize(item) for item in obj ]
    if isinstance(obj, dict):
        return {str(k): _serialize(v) for k, v in obj.items()}
    return obj


def build_report(
    *,
    inputs: Dict[str, Any],
    quality: Dict[str, Any],
    doc_forensics: Any,
    selfie_forensics: Any,
    recapture: Dict[str, Any],
    risk: Any,
    artifacts: Dict[str, Any],
    errors: Optional[list] = None,
) -> Dict[str, Any]:
    """Build a JSON-serializable report dict."""

    return {
        "version": REPORT_VERSION,
        "inputs": _serialize(inputs),
        "quality": _serialize(quality),
        "doc_forensics": _serialize(doc_forensics),
        "selfie_forensics": _serialize(selfie_forensics),
        "recapture": _serialize(recapture),
        "risk": _serialize(risk),
        "artifacts": _serialize(artifacts),
        "errors": errors or [],
    }


def save_report(report: Dict[str, Any], path: str | Path) -> str:
    """Save a JSON report to disk."""

    target = Path(path).expanduser().resolve()
    target.parent.mkdir(parents=True, exist_ok=True)
    target.write_text(json.dumps(report, indent=2, sort_keys=False), encoding="utf-8")
    return str(target)


def validate_report(report: Dict[str, Any]) -> None:
    """Validate a report against the JSON schema if jsonschema is available."""

    if jsonschema is None:
        return
    jsonschema.validate(instance=report, schema=REPORT_SCHEMA)


def save_html_report(report: Dict[str, Any], path: str | Path) -> str:
    """Save a minimal HTML report for quick inspection."""

    target = Path(path).expanduser().resolve()
    target.parent.mkdir(parents=True, exist_ok=True)
    payload = json.dumps(report, indent=2)
    html = (
        "<!doctype html><html><head><meta charset='utf-8'>"
        "<title>KYC Defense Report</title>"
        "<style>body{font-family:monospace;white-space:pre;}</style>"
        "</head><body>" + payload + "</body></html>"
    )
    target.write_text(html, encoding="utf-8")
    return str(target)


def send_preview_to_virtualcam(
    image: Image.Image,
    *,
    width: int = 640,
    height: int = 360,
    fps: int = 5,
    frames: int = 10,
) -> None:
    """Send a diagnostic preview to a virtual camera if available."""

    if pyvirtualcam is None:
        raise RuntimeError("pyvirtualcam is not available.")

    preview = image.convert("RGB").resize((width, height))
    frame = np.asarray(preview, dtype=np.uint8)
    with pyvirtualcam.Camera(width=width, height=height, fps=fps, fmt=pyvirtualcam.PixelFormat.RGB) as cam:
        for _ in range(frames):
            cam.send(frame)
            cam.sleep_until_next_frame()
