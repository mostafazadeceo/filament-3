from kyc_defense.quality import QualityResult
from kyc_defense.doc_forensics import DocForensicsResult
from kyc_defense.recapture import RecaptureResult
from kyc_defense.scoring import compute_risk_score


def test_risk_score_bounds():
    quality = QualityResult(
        score=45.0,
        blur_variance=30.0,
        exposure_mean=120.0,
        exposure_p05=10.0,
        exposure_p95=240.0,
        saturation_ratio=0.1,
        noise_estimate=15.0,
        contrast_std=12.0,
        resolution_ok=True,
        aspect_ok=True,
        blockiness=0.4,
        border_suspect=False,
        issues=["low_sharpness"],
    )
    doc_forensics = DocForensicsResult(
        exif_present=True,
        exif_suspicious=["Software:Demo"],
        exif_summary={},
        exif_warnings=[],
        exif_has_gps=False,
        ela_mean=10.0,
        ela_p95=30.0,
        resampling_score=0.6,
        blockiness=0.4,
        copy_move_score=0.5,
        edge_inconsistency=0.7,
        lighting_inconsistency=0.4,
        issues=["ela_high"],
        ela_path=None,
    )
    recapture = RecaptureResult(
        moire_score=0.6,
        aliasing_score=0.5,
        fft_peak_ratio=8.0,
        edge_repeat_score=0.4,
        issues=["moire_pattern"],
    )

    risk = compute_risk_score(quality, doc_forensics, recapture)
    assert 0.0 <= risk.score <= 100.0
    assert isinstance(risk.reasons, list)
