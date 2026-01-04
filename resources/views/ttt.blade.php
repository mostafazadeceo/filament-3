<!doctype html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>داشبورد پژوهشی KYC Defense</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Lalezar&family=Vazirmatn:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --bg: #f7f2ea;
      --bg-2: #fff3de;
      --ink: #1a1a1a;
      --muted: #5c5c5c;
      --brand: #0e8f87;
      --brand-2: #f09f1a;
      --accent: #1c3c3a;
      --card: #ffffff;
      --danger: #c9382b;
      --ok: #2a8c4f;
      --shadow: 0 12px 30px rgba(20, 27, 26, 0.12);
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: "Vazirmatn", "Tahoma", sans-serif;
      color: var(--ink);
      background: radial-gradient(1200px 800px at 90% -10%, #ffe3ba, transparent 60%),
        radial-gradient(900px 700px at -10% 10%, #dff5f2, transparent 55%),
        linear-gradient(160deg, var(--bg), var(--bg-2));
      min-height: 100vh;
    }

    .noise {
      position: fixed;
      inset: 0;
      pointer-events: none;
      background-image: repeating-linear-gradient(
          0deg,
          rgba(0, 0, 0, 0.02),
          rgba(0, 0, 0, 0.02) 1px,
          transparent 1px,
          transparent 3px
        ),
        repeating-linear-gradient(
          90deg,
          rgba(0, 0, 0, 0.02),
          rgba(0, 0, 0, 0.02) 1px,
          transparent 1px,
          transparent 4px
        );
      mix-blend-mode: multiply;
      opacity: 0.6;
      z-index: 0;
    }

    .wrap {
      position: relative;
      z-index: 1;
      max-width: 1240px;
      margin: 0 auto;
      padding: 32px 24px 60px;
    }

    header {
      display: grid;
      grid-template-columns: 1.2fr 0.8fr;
      gap: 24px;
      align-items: center;
      margin-bottom: 24px;
      animation: rise 700ms ease-out both;
    }

    .badge {
      display: inline-flex;
      gap: 8px;
      align-items: center;
      padding: 8px 14px;
      border-radius: 999px;
      background: rgba(14, 143, 135, 0.12);
      color: var(--brand);
      font-weight: 600;
      font-size: 0.9rem;
    }

    h1 {
      font-family: "Lalezar", "Vazirmatn", sans-serif;
      font-size: 2.5rem;
      margin: 16px 0 10px;
      letter-spacing: 0.5px;
    }

    .lead {
      color: var(--muted);
      font-size: 1.05rem;
      line-height: 1.8;
    }

    .header-card {
      background: var(--card);
      border-radius: 22px;
      padding: 22px;
      box-shadow: var(--shadow);
      border: 1px solid rgba(14, 143, 135, 0.15);
      display: grid;
      gap: 12px;
    }

    .header-card strong {
      color: var(--accent);
    }

    .grid {
      display: grid;
      gap: 18px;
      grid-template-columns: repeat(12, 1fr);
    }

    .card {
      background: var(--card);
      border-radius: 18px;
      padding: 18px;
      box-shadow: var(--shadow);
      border: 1px solid rgba(26, 26, 26, 0.06);
      animation: rise 700ms ease-out both;
    }

    .card h3 {
      margin: 0 0 8px;
      font-size: 1.1rem;
    }

    .kpi {
      grid-column: span 3;
    }

    .kpi-value {
      font-size: 2rem;
      font-weight: 700;
      color: var(--accent);
    }

    .kpi small {
      color: var(--muted);
    }

    .upload {
      grid-column: span 12;
      display: grid;
      gap: 16px;
    }

    .dropzone {
      border: 2px dashed rgba(14, 143, 135, 0.35);
      border-radius: 16px;
      padding: 18px;
      background: rgba(14, 143, 135, 0.05);
      display: grid;
      gap: 8px;
      cursor: pointer;
      transition: all 200ms ease;
    }

    .dropzone.dragover {
      background: rgba(14, 143, 135, 0.15);
      border-color: var(--brand);
    }

    .dropzone input {
      display: none;
    }

    .file-meta {
      color: var(--muted);
      font-size: 0.9rem;
    }

    .btn {
      border: none;
      border-radius: 999px;
      padding: 10px 18px;
      font-weight: 700;
      cursor: pointer;
      background: linear-gradient(120deg, var(--brand), #36b6ac);
      color: #fff;
      box-shadow: 0 10px 22px rgba(14, 143, 135, 0.25);
      transition: transform 150ms ease;
      width: fit-content;
    }

    .btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      box-shadow: none;
    }

    .btn:active {
      transform: translateY(1px);
    }

    .status-line {
      color: var(--muted);
      font-size: 0.95rem;
    }

    .chart {
      grid-column: span 7;
    }

    .signals {
      grid-column: span 5;
    }

    .list {
      display: grid;
      gap: 12px;
    }

    .signal-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      font-size: 0.95rem;
    }

    .bar {
      height: 10px;
      border-radius: 999px;
      background: #f0f0f0;
      overflow: hidden;
      flex: 1;
    }

    .bar span {
      display: block;
      height: 100%;
      background: linear-gradient(90deg, var(--brand), #46c2b5);
    }

    .reports {
      grid-column: span 7;
    }

    .policy {
      grid-column: span 5;
    }

    .artifacts {
      grid-column: span 6;
    }

    .forensics {
      grid-column: span 6;
    }

    .metadata {
      grid-column: span 6;
    }

    .json {
      grid-column: span 6;
    }

    .artifact-grid {
      display: grid;
      gap: 12px;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }

    .artifact-card {
      border-radius: 12px;
      background: #f8f1e3;
      padding: 10px;
      text-align: center;
      font-size: 0.9rem;
      color: var(--muted);
    }

    .artifact-card img {
      width: 100%;
      border-radius: 8px;
      margin-top: 8px;
      object-fit: cover;
      max-height: 180px;
    }

    .meta-list {
      display: grid;
      gap: 8px;
      font-size: 0.95rem;
    }

    .meta-item {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      align-items: center;
    }

    .meta-key {
      color: var(--muted);
    }

    .meta-value {
      color: var(--accent);
      font-weight: 600;
      text-align: left;
      direction: ltr;
    }

    pre {
      background: #1d1f1f;
      color: #f6f1e7;
      border-radius: 12px;
      padding: 14px;
      font-size: 0.85rem;
      max-height: 320px;
      overflow: auto;
      direction: ltr;
      text-align: left;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.95rem;
    }

    th,
    td {
      padding: 10px 8px;
      text-align: right;
      border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    }

    th {
      color: var(--muted);
      font-weight: 600;
    }

    .status {
      padding: 4px 10px;
      border-radius: 999px;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.85rem;
    }

    .status.ok {
      background: rgba(42, 140, 79, 0.14);
      color: var(--ok);
    }

    .status.warn {
      background: rgba(240, 159, 26, 0.14);
      color: #b26b00;
    }

    .status.danger {
      background: rgba(201, 56, 43, 0.14);
      color: var(--danger);
    }

    .cta {
      margin-top: 14px;
      background: linear-gradient(120deg, rgba(14, 143, 135, 0.12), rgba(240, 159, 26, 0.14));
      border-radius: 14px;
      padding: 14px;
      font-size: 0.95rem;
    }

    .pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 12px;
      border-radius: 12px;
      background: #f8f1e3;
      color: var(--accent);
      font-weight: 600;
      font-size: 0.85rem;
    }

    .footer {
      margin-top: 28px;
      color: var(--muted);
      font-size: 0.9rem;
      line-height: 1.7;
    }

    .float {
      position: absolute;
      width: 260px;
      height: 260px;
      border-radius: 40% 60% 55% 45% / 55% 35% 65% 45%;
      background: radial-gradient(circle at 30% 30%, rgba(14, 143, 135, 0.35), transparent 60%),
        radial-gradient(circle at 70% 60%, rgba(240, 159, 26, 0.35), transparent 60%);
      filter: blur(0.5px);
      top: -70px;
      left: -40px;
      animation: float 9s ease-in-out infinite;
      z-index: 0;
    }

    @keyframes rise {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes float {
      0%,
      100% {
        transform: translateY(0) rotate(0deg);
      }
      50% {
        transform: translateY(18px) rotate(3deg);
      }
    }

    @media (max-width: 980px) {
      header {
        grid-template-columns: 1fr;
      }

      .kpi {
        grid-column: span 6;
      }

      .chart,
      .signals,
      .reports,
      .policy,
      .artifacts,
      .forensics,
      .metadata,
      .json {
        grid-column: span 12;
      }
    }

    @media (max-width: 600px) {
      h1 {
        font-size: 2.1rem;
      }

      .kpi {
        grid-column: span 12;
      }
    }
  </style>
</head>
<body>
  <div class="noise"></div>
  <div class="wrap">
    <div class="float"></div>
    <header>
      <div>
        <span class="badge">پژوهش دفاعی KYC • نسخه آزمایشگاهی</span>
        <h1>داشبورد رصد کیفیت و فارنزیک سند/سلفی</h1>
        <p class="lead">
          این نما برای تحلیل علمی کیفیت تصویر، نشانه‌های دستکاری، و سیگنال‌های ریسک طراحی شده است.
          خروجی‌ها صرفا پژوهشی هستند و هیچ قابلیت تولید یا بهینه‌سازی سند ندارند.
        </p>
      </div>
      <div class="header-card">
        <div class="pill">حالت امن: تحلیل دفاعی</div>
        <div><strong>ورودی:</strong> تصویر سند + سلفی اختیاری</div>
        <div><strong>خروجی:</strong> گزارش JSON + آرتیفکت‌های ELA/Preview</div>
        <div><strong>وضعیت سرویس:</strong> آماده تحلیل</div>
      </div>
    </header>

    <section class="grid">
      <div class="card upload" style="animation-delay: 60ms;">
        <h3>بارگذاری تصاویر</h3>
        <div class="dropzone" id="docDrop">
          <input type="file" id="docInput" accept="image/*" />
          <strong>سند را بکشید و رها کنید</strong>
          <span class="file-meta" id="docMeta">یا برای انتخاب کلیک کنید (الزامی)</span>
        </div>
        <div class="dropzone" id="selfieDrop">
          <input type="file" id="selfieInput" accept="image/*" />
          <strong>سلفی (اختیاری)</strong>
          <span class="file-meta" id="selfieMeta">یا برای انتخاب کلیک کنید</span>
        </div>
        <button class="btn" id="analyzeBtn" disabled>شروع تحلیل</button>
        <div class="status-line" id="statusLine">ابتدا یک تصویر سند انتخاب کنید.</div>
      </div>

      <div class="card kpi" style="animation-delay: 120ms;">
        <h3>کیفیت سند</h3>
        <div class="kpi-value" id="docQuality">—</div>
        <small id="docQualityNote">منتظر تحلیل</small>
      </div>
      <div class="card kpi" style="animation-delay: 180ms;">
        <h3>کیفیت سلفی</h3>
        <div class="kpi-value" id="selfieQuality">—</div>
        <small id="selfieQualityNote">منتظر تحلیل</small>
      </div>
      <div class="card kpi" style="animation-delay: 240ms;">
        <h3>ریسک کلی</h3>
        <div class="kpi-value" id="riskScore">—</div>
        <small id="riskNote">منتظر تحلیل</small>
      </div>
      <div class="card kpi" style="animation-delay: 300ms;">
        <h3>تحلیل ID</h3>
        <div class="kpi-value" id="analysisId">—</div>
        <small>برای ردیابی داخلی</small>
      </div>

      <div class="card chart" style="animation-delay: 360ms;">
        <h3>نمودار روند ریسک (نمونه نمایشی)</h3>
        <svg viewBox="0 0 680 220" width="100%" height="220" role="img" aria-label="Risk trend">
          <defs>
            <linearGradient id="riskGradient" x1="0" y1="0" x2="1" y2="0">
              <stop offset="0%" stop-color="#0e8f87" />
              <stop offset="100%" stop-color="#f09f1a" />
            </linearGradient>
          </defs>
          <rect x="0" y="0" width="680" height="220" fill="#fffaf2" rx="16"></rect>
          <path d="M20 160 L70 120 L120 140 L170 110 L220 150 L270 100 L320 90 L370 130 L420 80 L470 110 L520 70 L570 95 L640 60" fill="none" stroke="url(#riskGradient)" stroke-width="5" stroke-linecap="round" />
          <circle cx="640" cy="60" r="6" fill="#f09f1a" />
        </svg>
        <div class="cta">برای نتایج زنده، ابتدا تحلیل را اجرا کنید.</div>
      </div>

      <div class="card signals" style="animation-delay: 420ms;">
        <h3>سیگنال‌های اثرگذار</h3>
        <div class="list" id="signalsList">
          <div class="signal-row">
            <span>در انتظار تحلیل</span>
            <div class="bar"><span style="width: 10%;"></span></div>
            <strong>—</strong>
          </div>
        </div>
      </div>

      <div class="card artifacts" style="animation-delay: 480ms;">
        <h3>آرتیفکت‌ها</h3>
        <div class="artifact-grid">
          <div class="artifact-card">
            ELA Heatmap
            <img id="elaPreview" alt="ELA preview" style="display:none;" />
          </div>
          <div class="artifact-card">
            علامت‌گذاری مشکلات
            <img id="docIssueOverlay" alt="Issue overlay" style="display:none;" />
          </div>
          <div class="artifact-card">
            Selfie Preview
            <img id="selfiePreview" alt="Selfie preview" style="display:none;" />
          </div>
        </div>
      </div>

      <div class="card forensics" style="animation-delay: 520ms;">
        <h3>فارنزیک سند</h3>
        <div class="list" id="docForensicsList">
          <div>منتظر تحلیل</div>
        </div>
      </div>

      <div class="card metadata" style="animation-delay: 560ms;">
        <h3>متادیتای سند (EXIF)</h3>
        <div class="meta-list" id="exifSummary">
          <div>منتظر تحلیل</div>
        </div>
        <div class="cta" id="exifWarnings">—</div>
      </div>

      <div class="card json" style="animation-delay: 600ms;">
        <h3>خروجی خام JSON</h3>
        <pre id="reportJson">{}</pre>
      </div>

      <div class="card reports" style="animation-delay: 660ms;">
        <h3>نمونه وضعیت‌ها</h3>
        <table>
          <thead>
            <tr>
              <th>شناسه</th>
              <th>نتیجه</th>
              <th>ریسک</th>
              <th>یادداشت</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#A-1042</td>
              <td><span class="status ok">پایدار</span></td>
              <td>19</td>
              <td>کیفیت بالا، بدون نشانه دستکاری</td>
            </tr>
            <tr>
              <td>#A-1043</td>
              <td><span class="status warn">نیاز به بازبینی</span></td>
              <td>44</td>
              <td>ریکپچر خفیف، نور یکنواخت</td>
            </tr>
            <tr>
              <td>#A-1044</td>
              <td><span class="status danger">پرریسک</span></td>
              <td>71</td>
              <td>ELA بالا و بلاک‌نس شدید</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card policy" style="animation-delay: 720ms;">
        <h3>قواعد پژوهشی</h3>
        <p class="lead" style="margin: 0 0 12px;">
          سیاست‌ها برای جلوگیری از سوءاستفاده تنظیم شده‌اند و خروجی‌ها صرفاً برای ارزیابی دفاعی مجاز هستند.
        </p>
        <div class="list">
          <div>• عدم تولید یا بهینه‌سازی سند واقعی</div>
          <div>• ذخیره‌سازی امن گزارش‌های JSON</div>
          <div>• گزارش شفاف دلایل افزایش ریسک</div>
          <div>• پشتیبانی از FAR/FRR و ROC-AUC در صورت وجود برچسب</div>
        </div>
        <div class="cta">
          اجرای نمونه: <code>python -m kyc_defense.cli analyze --doc doc.jpg --selfie selfie.jpg</code>
        </div>
      </div>
    </section>

    <div class="footer">
      این صفحه یک داشبورد نمایشی است و داده‌های واقعی را افشا نمی‌کند.
      برای اتصال به داده‌های زنده، API پژوهشی داخلی باید امن‌سازی شود.
    </div>
  </div>

  <script>
    const docDrop = document.getElementById('docDrop');
    const docInput = document.getElementById('docInput');
    const docMeta = document.getElementById('docMeta');
    const selfieDrop = document.getElementById('selfieDrop');
    const selfieInput = document.getElementById('selfieInput');
    const selfieMeta = document.getElementById('selfieMeta');
    const analyzeBtn = document.getElementById('analyzeBtn');
    const statusLine = document.getElementById('statusLine');
    const reportJson = document.getElementById('reportJson');

    const docQuality = document.getElementById('docQuality');
    const selfieQuality = document.getElementById('selfieQuality');
    const riskScore = document.getElementById('riskScore');
    const analysisId = document.getElementById('analysisId');
    const docQualityNote = document.getElementById('docQualityNote');
    const selfieQualityNote = document.getElementById('selfieQualityNote');
    const riskNote = document.getElementById('riskNote');
    const signalsList = document.getElementById('signalsList');
    const elaPreview = document.getElementById('elaPreview');
    const docIssueOverlay = document.getElementById('docIssueOverlay');
    const selfiePreview = document.getElementById('selfiePreview');
    const docForensicsList = document.getElementById('docForensicsList');
    const exifSummary = document.getElementById('exifSummary');
    const exifWarnings = document.getElementById('exifWarnings');

    let docFile = null;
    let selfieFile = null;

    const formatBytes = (bytes) => {
      if (!bytes) return '0 B';
      const sizes = ['B', 'KB', 'MB', 'GB'];
      const i = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), sizes.length - 1);
      return `${(bytes / Math.pow(1024, i)).toFixed(1)} ${sizes[i]}`;
    };

    const ISSUE_LABELS = {
      low_sharpness: 'وضوح کم',
      saturation_clipping: 'کلیپینگ نور/سچوریشن',
      high_noise: 'نویز بالا',
      low_resolution: 'رزولوشن پایین',
      aspect_mismatch: 'نسبت ابعاد نامعمول',
      compression_artifacts: 'آرتیفکت فشرده‌سازی',
      border_crop_suspicion: 'شک به کراپ/حاشیه',
      suspicious_exif: 'EXIF مشکوک',
      ela_high: 'ELA بالا',
      resampling_artifacts: 'نشانه بازنمونه‌برداری',
      copy_move_pattern: 'الگوی کپی-جابجایی',
      edge_inconsistency: 'ناسازگاری لبه‌ها',
      lighting_inconsistency: 'ناسازگاری نور',
      face_not_detected: 'چهره شناسایی نشد',
      face_too_small: 'اندازه چهره کوچک',
      pose_off_center: 'زاویه نامناسب چهره',
      flat_lighting: 'نور یکنواخت/کم‌کنتراست',
      background_uniform: 'پس‌زمینه بیش از حد یکنواخت',
      recapture_hint: 'نشانه ریکپچر',
      moire_pattern: 'الگوی موآره',
      aliasing: 'علیاسینگ',
      edge_repeat: 'تکرار لبه‌ها',
      missing_exif: 'فاقد EXIF',
      missing_datetime: 'فاقد زمان ثبت',
      software_tag_present: 'نرم‌افزار ویرایشگر ثبت شده',
      gps_present: 'وجود GPS در متادیتا',
      datetime_mismatch: 'ناهمخوانی زمان‌های EXIF',
    };

    const SIGNAL_LABELS = {
      doc_blur: 'تاری سند',
      doc_noise: 'نویز سند',
      doc_saturation: 'اشباع سند',
      doc_blockiness: 'بلاک‌نس سند',
      doc_ela: 'ELA سند',
      doc_resampling: 'بازنمونه‌برداری سند',
      doc_copy_move: 'کپی-جابجایی سند',
      doc_edge_inconsistency: 'ناسازگاری لبه سند',
      doc_lighting_inconsistency: 'ناسازگاری نور سند',
      doc_recapture: 'ریکپچر سند',
      selfie_face_missing: 'چهره سلفی',
      selfie_face_small: 'اندازه چهره سلفی',
      selfie_pose: 'پوز سلفی',
      selfie_recapture: 'ریکپچر سلفی',
      selfie_blur: 'تاری سلفی',
      selfie_noise: 'نویز سلفی',
      selfie_saturation: 'اشباع سلفی',
      selfie_blockiness: 'بلاک‌نس سلفی',
    };

    const EXIF_LABELS = {
      Make: 'برند دوربین',
      Model: 'مدل دوربین',
      Software: 'نرم‌افزار',
      ProcessingSoftware: 'پردازشگر',
      DateTimeOriginal: 'زمان ثبت',
      CreateDate: 'زمان ایجاد',
      ModifyDate: 'زمان ویرایش',
      DateTime: 'زمان عمومی',
      Orientation: 'جهت/چرخش',
      Artist: 'سازنده',
      GPSInfo: 'GPS',
    };

    const translateIssues = (issues = []) => issues.map((issue) => ISSUE_LABELS[issue] || issue);

    const formatReason = (reason) => {
      if (!reason) return '';
      const [key, raw] = reason.split(':');
      const label = SIGNAL_LABELS[key] || key;
      const value = Number(raw);
      if (Number.isFinite(value)) {
        return `${label} ${Math.round(value * 100)}%`;
      }
      return raw ? `${label} ${raw}` : label;
    };

    const updateButtonState = () => {
      analyzeBtn.disabled = !docFile;
      statusLine.textContent = docFile ? 'آماده شروع تحلیل.' : 'ابتدا یک تصویر سند انتخاب کنید.';
    };

    const setFile = (type, file) => {
      if (type === 'doc') {
        docFile = file;
        docMeta.textContent = `${file.name} • ${formatBytes(file.size)}`;
      } else {
        selfieFile = file;
        selfieMeta.textContent = `${file.name} • ${formatBytes(file.size)}`;
      }
      updateButtonState();
    };

    const setupDrop = (dropEl, inputEl, metaEl, type) => {
      dropEl.addEventListener('click', () => inputEl.click());
      dropEl.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropEl.classList.add('dragover');
      });
      dropEl.addEventListener('dragleave', () => dropEl.classList.remove('dragover'));
      dropEl.addEventListener('drop', (event) => {
        event.preventDefault();
        dropEl.classList.remove('dragover');
        const file = event.dataTransfer.files[0];
        if (file) {
          inputEl.files = event.dataTransfer.files;
          setFile(type, file);
        }
      });
      inputEl.addEventListener('change', () => {
        const file = inputEl.files[0];
        if (file) {
          setFile(type, file);
        }
      });
    };

    setupDrop(docDrop, docInput, docMeta, 'doc');
    setupDrop(selfieDrop, selfieInput, selfieMeta, 'selfie');

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const renderSignals = (signals = {}) => {
      const entries = Object.entries(signals)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 4);
      signalsList.innerHTML = '';
      if (!entries.length) {
        signalsList.innerHTML = '<div class="signal-row"><span>سیگنال قابل نمایش نیست</span><div class="bar"><span style="width:10%"></span></div><strong>—</strong></div>';
        return;
      }
      entries.forEach(([key, value]) => {
        const pct = Math.round(value * 100);
        const label = SIGNAL_LABELS[key] || key;
        const row = document.createElement('div');
        row.className = 'signal-row';
        row.innerHTML = `<span>${label}</span><div class="bar"><span style="width:${pct}%"></span></div><strong>${pct}%</strong>`;
        signalsList.appendChild(row);
      });
    };

    const showArtifact = (imgEl, url) => {
      if (!url) {
        imgEl.style.display = 'none';
        return;
      }
      imgEl.src = url + `?t=${Date.now()}`;
      imgEl.style.display = 'block';
    };

    const renderExifSummary = (summary = {}) => {
      exifSummary.innerHTML = '';
      const entries = Object.entries(summary || {});
      if (!entries.length) {
        exifSummary.innerHTML = '<div>EXIF یافت نشد.</div>';
        return;
      }
      entries.forEach(([key, value]) => {
        const row = document.createElement('div');
        row.className = 'meta-item';
        const label = EXIF_LABELS[key] || key;
        row.innerHTML = `<span class="meta-key">${label}</span><span class="meta-value">${value ?? '—'}</span>`;
        exifSummary.appendChild(row);
      });
    };

    const renderDocForensics = (docForensics, recapture) => {
      docForensicsList.innerHTML = '';
      if (!docForensics) {
        docForensicsList.innerHTML = '<div>داده‌ای در دسترس نیست.</div>';
        return;
      }
      const items = [];
      if (Array.isArray(docForensics.issues)) {
        items.push(...translateIssues(docForensics.issues));
      }
      if (Array.isArray(docForensics.exif_warnings)) {
        items.push(...translateIssues(docForensics.exif_warnings));
      }
      if (Array.isArray(docForensics.exif_suspicious) && docForensics.exif_suspicious.length) {
        items.push(`EXIF مشکوک: ${docForensics.exif_suspicious.join('، ')}`);
      }
      if (recapture && Array.isArray(recapture.issues)) {
        const recaptureItems = translateIssues(recapture.issues).map((item) => `ریکپچر: ${item}`);
        items.push(...recaptureItems);
      }
      if (!items.length) {
        docForensicsList.innerHTML = '<div>بدون نشانه پرریسک</div>';
        return;
      }
      items.forEach((item) => {
        const row = document.createElement('div');
        row.textContent = `• ${item}`;
        docForensicsList.appendChild(row);
      });
    };

    const renderExifWarnings = (warnings = []) => {
      const items = translateIssues(Array.isArray(warnings) ? warnings : []);
      exifWarnings.textContent = items.length ? `هشدارها: ${items.join('، ')}` : 'هشدار متادیتا یافت نشد.';
    };

    analyzeBtn.addEventListener('click', async () => {
      if (!docFile) return;
      analyzeBtn.disabled = true;
      statusLine.textContent = 'در حال تحلیل...';

      const formData = new FormData();
      formData.append('document', docFile);
      if (selfieFile) {
        formData.append('selfie', selfieFile);
      }

      try {
        const response = await fetch('/ttt/analyze', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
          },
          body: formData,
        });
        const data = await response.json();
        if (!response.ok) {
          throw new Error(data.detail || data.message || 'analysis_failed');
        }

        reportJson.textContent = JSON.stringify(data, null, 2);
        analysisId.textContent = data.analysis_id ? data.analysis_id.slice(0, 8) : '—';

        const docScore = data.quality?.document?.score;
        docQuality.textContent = docScore !== undefined ? `${Math.round(docScore)}/100` : '—';
        const docIssues = translateIssues(data.quality?.document?.issues || []);
        docQualityNote.textContent = docIssues.length ? docIssues.join('، ') : 'بدون هشدار جدی';

        const selfieScore = data.quality?.selfie?.score;
        selfieQuality.textContent = selfieScore !== undefined ? `${Math.round(selfieScore)}/100` : '—';
        const selfieIssues = translateIssues(data.selfie_forensics?.issues || []);
        selfieQualityNote.textContent = data.selfie_forensics
          ? (selfieIssues.length ? selfieIssues.join('، ') : 'بدون هشدار جدی')
          : 'سلفی ثبت نشده';

        const risk = data.risk?.score;
        riskScore.textContent = risk !== undefined ? `${Math.round(risk)}/100` : '—';
        const reasons = Array.isArray(data.risk?.reasons) ? data.risk.reasons : [];
        const reasonText = reasons.length ? reasons.map(formatReason).filter(Boolean).join('، ') : '';
        riskNote.textContent = reasonText || 'ریسک پایین';

        renderSignals(data.risk?.signals || {});
        showArtifact(elaPreview, data.artifacts?.ela_heatmap_url);
        showArtifact(docIssueOverlay, data.artifacts?.doc_issue_overlay_url);
        showArtifact(selfiePreview, data.artifacts?.selfie_preview_url);
        renderDocForensics(data.doc_forensics, data.recapture?.document);
        renderExifSummary(data.doc_forensics?.exif_summary || {});
        renderExifWarnings(data.doc_forensics?.exif_warnings || []);

        statusLine.textContent = 'تحلیل با موفقیت انجام شد.';
      } catch (error) {
        statusLine.textContent = `خطا در تحلیل: ${error.message}`;
      } finally {
        analyzeBtn.disabled = false;
      }
    });
  </script>
</body>
</html>
