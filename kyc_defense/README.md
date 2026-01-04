# KYC Defense Toolkit (Research)

This package provides **defensive** analysis utilities for KYC document + selfie assessment.
It focuses on quality signals, forensic indicators, and explainable risk scoring.

## Scope & Safety
- This toolkit **does not** generate, alter, or optimize documents to pass KYC.
- It is designed for **research and evaluation** of defenses only.

## Install

```bash
python -m venv .venv
source .venv/bin/activate
pip install -e .
# Optional extras
pip install -e ".[opencv,metrics,yaml,virtualcam,jsonschema]"
```

## CLI

Analyze a single document (+ optional selfie):

```bash
python -m kyc_defense.cli analyze --doc /path/doc.jpg --selfie /path/selfie.jpg --out report.json --save-artifacts artifacts/
```

Custom weights (JSON/YAML):

```json
{
  "weights": {
    "doc_ela": 0.2,
    "selfie_face_missing": 0.2
  },
  "thresholds": {
    "blur_variance": 70.0
  }
}
```

Evaluate a labeled dataset:

```bash
python -m kyc_defense.cli evaluate --dataset /path/dataset --labels labels.csv --out metrics.json
```

`labels.csv` should contain: `doc_path,selfie_path,label`.
Use label `1` for risky/invalid samples and `0` for genuine samples.

## Output
Each analysis produces a JSON report containing:
- quality metrics
- document forensics
- selfie forensics (if provided)
- recapture signals
- risk score and reasons
- artifact paths (ELA heatmap, selfie preview)

## Python API

```python
from kyc_defense import analyze_pair, AnalysisOptions

report = analyze_pair(
    doc_path="/path/doc.jpg",
    selfie_path="/path/selfie.jpg",
    options=AnalysisOptions(artifacts_dir="artifacts")
)
```

## Notes
- OpenCV enables face detection, keypoint matching, and video loading.
- If optional dependencies are missing, the toolkit degrades gracefully.
