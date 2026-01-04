from __future__ import annotations

"""Command-line interface for KYC defense research."""

import argparse
import json
from pathlib import Path
from typing import List, Optional

from . import AnalysisOptions, analyze_pair
from .datasets import load_dataset
from .metrics import compute_metrics
from .reporting import save_report


def _parse_args(argv: Optional[List[str]] = None) -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="KYC defense research toolkit (defensive analysis only).")
    sub = parser.add_subparsers(dest="command", required=True)

    analyze = sub.add_parser("analyze", help="Analyze a document/selfie pair")
    analyze.add_argument("--doc", required=True, help="Path to document image")
    analyze.add_argument("--selfie", help="Path to selfie image")
    analyze.add_argument("--out", help="Output JSON report path")
    analyze.add_argument("--save-artifacts", help="Directory to save ELA/preview images")
    analyze.add_argument("--weights", help="Path to weights config (JSON or YAML)")
    analyze.add_argument("--html", help="Optional HTML report path")
    analyze.add_argument("--virtualcam", action="store_true", help="Send selfie preview to virtualcam")

    evaluate = sub.add_parser("evaluate", help="Evaluate a labeled dataset")
    evaluate.add_argument("--dataset", required=True, help="Dataset root path")
    evaluate.add_argument("--labels", help="CSV with doc_path,selfie_path,label columns")
    evaluate.add_argument("--out", required=True, help="Metrics JSON output path")
    evaluate.add_argument("--weights", help="Path to weights config (JSON or YAML)")
    evaluate.add_argument("--threshold", type=float, default=50.0, help="Risk threshold (0-100 or 0-1)")

    return parser.parse_args(argv)


def _normalize_threshold(value: float) -> float:
    return value if value <= 1.0 else value / 100.0


def run_analyze(args: argparse.Namespace) -> int:
    options = AnalysisOptions(
        weights_path=args.weights,
        artifacts_dir=args.save_artifacts,
        html_report_path=args.html,
        virtualcam_preview=args.virtualcam,
    )
    report = analyze_pair(doc_path=args.doc, selfie_path=args.selfie, options=options)
    payload = json.dumps(report, indent=2)
    if args.out:
        save_report(report, args.out)
    else:
        print(payload)
    return 0


def run_evaluate(args: argparse.Namespace) -> int:
    items = load_dataset(args.dataset, labels_csv=args.labels)
    y_true: List[int] = []
    y_score: List[float] = []
    errors: List[str] = []

    for item in items:
        if item.label is None:
            continue
        try:
            report = analyze_pair(
                doc_path=str(item.doc_path),
                selfie_path=str(item.selfie_path) if item.selfie_path else None,
                options=AnalysisOptions(weights_path=args.weights),
            )
        except Exception as exc:
            errors.append(f"{item.doc_path}:{exc}")
            continue
        score = float(report.get("risk", {}).get("score", 0.0)) / 100.0
        y_true.append(int(item.label))
        y_score.append(score)

    if not y_true:
        raise RuntimeError("No labeled items were found for evaluation.")

    threshold = _normalize_threshold(args.threshold)
    metrics = compute_metrics(y_true, y_score, threshold=threshold)
    payload = {
        "threshold": threshold,
        "count": len(y_true),
        "errors": errors,
        "skipped": len(errors),
        "metrics": metrics,
    }
    Path(args.out).expanduser().resolve().write_text(json.dumps(payload, indent=2), encoding="utf-8")
    return 0


def main(argv: Optional[List[str]] = None) -> int:
    args = _parse_args(argv)
    if args.command == "analyze":
        return run_analyze(args)
    if args.command == "evaluate":
        return run_evaluate(args)
    raise RuntimeError("Unknown command")


if __name__ == "__main__":
    raise SystemExit(main())
