from __future__ import annotations

"""Evaluation metrics for defensive KYC research."""

from typing import Dict, Iterable, List, Optional, Tuple

import numpy as np

try:
    from sklearn.metrics import roc_auc_score  # type: ignore
except Exception:
    roc_auc_score = None


def _to_array(values: Iterable[float]) -> np.ndarray:
    return np.asarray(list(values), dtype=float)


def confusion_counts(y_true: Iterable[int], y_pred: Iterable[int]) -> Dict[str, int]:
    """Compute confusion matrix counts."""

    y_true_arr = _to_array(y_true).astype(int)
    y_pred_arr = _to_array(y_pred).astype(int)
    tp = int(np.sum((y_true_arr == 1) & (y_pred_arr == 1)))
    tn = int(np.sum((y_true_arr == 0) & (y_pred_arr == 0)))
    fp = int(np.sum((y_true_arr == 0) & (y_pred_arr == 1)))
    fn = int(np.sum((y_true_arr == 1) & (y_pred_arr == 0)))
    return {"tp": tp, "tn": tn, "fp": fp, "fn": fn}


def far_frr(counts: Dict[str, int]) -> Dict[str, float]:
    """Compute FAR and FRR from confusion counts."""

    fp = counts["fp"]
    tn = counts["tn"]
    fn = counts["fn"]
    tp = counts["tp"]
    far = fp / float(fp + tn) if (fp + tn) > 0 else 0.0
    frr = fn / float(fn + tp) if (fn + tp) > 0 else 0.0
    return {"far": far, "frr": frr}


def precision_recall(counts: Dict[str, int]) -> Dict[str, float]:
    """Compute precision and recall from confusion counts."""

    tp = counts["tp"]
    fp = counts["fp"]
    fn = counts["fn"]
    precision = tp / float(tp + fp) if (tp + fp) > 0 else 0.0
    recall = tp / float(tp + fn) if (tp + fn) > 0 else 0.0
    return {"precision": precision, "recall": recall}


def roc_auc(y_true: Iterable[int], y_score: Iterable[float]) -> Optional[float]:
    """Compute ROC-AUC if scikit-learn is available."""

    if roc_auc_score is None:
        return None
    return float(roc_auc_score(list(y_true), list(y_score)))


def compute_metrics(
    y_true: Iterable[int],
    y_score: Iterable[float],
    *,
    threshold: float = 0.5,
) -> Dict[str, float | int | None]:
    """Compute FAR/FRR, precision/recall, and ROC-AUC if possible."""

    y_true_arr = _to_array(y_true).astype(int)
    y_score_arr = _to_array(y_score)
    y_pred = (y_score_arr >= threshold).astype(int)
    counts = confusion_counts(y_true_arr, y_pred)
    rates = far_frr(counts)
    pr = precision_recall(counts)
    auc = roc_auc(y_true_arr, y_score_arr)
    result: Dict[str, float | int | None] = {
        **counts,
        **rates,
        **pr,
        "roc_auc": auc,
    }
    return result
