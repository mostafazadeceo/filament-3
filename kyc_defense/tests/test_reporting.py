from kyc_defense.reporting import build_report, REPORT_SCHEMA


def test_report_keys():
    report = build_report(
        inputs={"document": {"path": "x"}, "selfie": None},
        quality={"document": {}},
        doc_forensics={},
        selfie_forensics=None,
        recapture={"document": {}},
        risk={"score": 0},
        artifacts={},
        errors=[],
    )
    for key in REPORT_SCHEMA["required"]:
        assert key in report
