from PIL import Image

from kyc_defense.quality import assess_quality


def test_quality_metrics_on_synthetic_image():
    img = Image.new("RGB", (800, 600), (128, 128, 128))
    result = assess_quality(img)
    assert result.resolution_ok
    assert result.blur_variance >= 0.0
    assert 0.0 <= result.saturation_ratio <= 1.0


def test_quality_detects_low_resolution():
    img = Image.new("RGB", (200, 150), (128, 128, 128))
    result = assess_quality(img)
    assert not result.resolution_ok
