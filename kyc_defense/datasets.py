from __future__ import annotations

"""Dataset helpers for KYC defense research."""

from dataclasses import dataclass, field
from pathlib import Path
from typing import Dict, List, Optional

import csv


@dataclass
class DatasetItem:
    """Represents a document + selfie pair (label optional)."""

    doc_path: Path
    selfie_path: Optional[Path] = None
    label: Optional[int] = None
    meta: Dict[str, str] = field(default_factory=dict)


@dataclass
class DatasetSplit:
    """Split a dataset into train/val/test subsets."""

    train: List[DatasetItem]
    val: List[DatasetItem]
    test: List[DatasetItem]


def _read_labels_csv(path: Path) -> List[DatasetItem]:
    items: List[DatasetItem] = []
    with path.open("r", newline="", encoding="utf-8") as handle:
        reader = csv.DictReader(handle)
        for row in reader:
            doc = Path(row.get("doc_path", "")).expanduser()
            selfie = Path(row.get("selfie_path", "")).expanduser() if row.get("selfie_path") else None
            label_raw = row.get("label")
            label = int(label_raw) if label_raw is not None and label_raw != "" else None
            meta = {k: v for k, v in row.items() if k not in {"doc_path", "selfie_path", "label"}}
            items.append(DatasetItem(doc_path=doc, selfie_path=selfie, label=label, meta=meta))
    return items


def load_dataset(
    root: str | Path,
    *,
    labels_csv: Optional[str | Path] = None,
) -> List[DatasetItem]:
    """Load a dataset of document/selfie pairs."""

    root_path = Path(root).expanduser().resolve()
    if labels_csv:
        items = _read_labels_csv(Path(labels_csv).expanduser().resolve())
        resolved: List[DatasetItem] = []
        for item in items:
            doc = (root_path / item.doc_path).resolve() if not item.doc_path.is_absolute() else item.doc_path
            selfie = None
            if item.selfie_path:
                selfie = (
                    (root_path / item.selfie_path).resolve()
                    if not item.selfie_path.is_absolute()
                    else item.selfie_path
                )
            resolved.append(DatasetItem(doc_path=doc, selfie_path=selfie, label=item.label, meta=item.meta))
        return resolved

    documents_dir = root_path / "documents"
    selfies_dir = root_path / "selfies"
    if not documents_dir.exists():
        raise FileNotFoundError(f"Dataset documents directory not found: {documents_dir}")

    items: List[DatasetItem] = []
    for doc_path in sorted(documents_dir.glob("*")):
        if not doc_path.is_file():
            continue
        selfie_path = selfies_dir / doc_path.name
        if not selfie_path.exists():
            selfie_path = None
        items.append(DatasetItem(doc_path=doc_path.resolve(), selfie_path=selfie_path.resolve() if selfie_path else None))
    return items


def split_dataset(
    items: List[DatasetItem],
    *,
    train_ratio: float = 0.7,
    val_ratio: float = 0.15,
) -> DatasetSplit:
    """Split items into train/val/test subsets deterministically."""

    total = len(items)
    train_end = int(total * train_ratio)
    val_end = train_end + int(total * val_ratio)
    return DatasetSplit(train=items[:train_end], val=items[train_end:val_end], test=items[val_end:])
