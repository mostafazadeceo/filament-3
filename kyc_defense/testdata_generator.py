from __future__ import annotations

"""Generate synthetic test images with a watermark for research-only use."""

from dataclasses import dataclass
from pathlib import Path
from typing import List, Tuple

from PIL import Image, ImageDraw, ImageFont

from .io import ensure_dir


@dataclass
class TestDataSpec:
    """Specification for synthetic test image generation."""

    size: Tuple[int, int] = (800, 500)
    background: Tuple[int, int, int] = (235, 235, 235)
    watermark: str = "TEST ONLY"
    count: int = 5


def _draw_watermark(image: Image.Image, text: str) -> Image.Image:
    draw = ImageDraw.Draw(image)
    try:
        font = ImageFont.load_default()
    except Exception:
        font = None
    w, h = image.size
    for y in range(0, h, 80):
        for x in range(0, w, 200):
            draw.text((x, y), text, fill=(200, 30, 30), font=font)
    return image


def generate_test_images(output_dir: str | Path, spec: TestDataSpec) -> List[str]:
    """Generate synthetic images with a clear TEST ONLY watermark."""

    target = ensure_dir(output_dir)
    paths: List[str] = []
    for idx in range(spec.count):
        image = Image.new("RGB", spec.size, spec.background)
        image = _draw_watermark(image, spec.watermark)
        path = target / f"test_only_{idx + 1}.png"
        image.save(path)
        paths.append(str(path))
    return paths
