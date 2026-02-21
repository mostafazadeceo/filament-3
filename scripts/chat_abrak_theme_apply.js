db = db.getSiblingDB('rocketchat');

const now = new Date();

const customCss = `
:root {
  color-scheme: dark;
  --abrak-font: Vazirmatn, ui-sans-serif, system-ui, sans-serif;
  --abrak-bg: #07080d;
  --abrak-fg: #f4f6fb;
  --abrak-soft: #9aa6c0;
  --abrak-aurora: #6ee7ff;
  --abrak-pulse: #8b5cf6;
  --abrak-line: rgba(110, 231, 255, 0.12);
  --abrak-line-strong: rgba(110, 231, 255, 0.28);
  --abrak-panel: rgba(8, 16, 38, 0.68);
  --abrak-panel-strong: rgba(8, 22, 56, 0.84);
  --abrak-chip: rgba(11, 30, 72, 0.54);
  --abrak-chip-active: rgba(23, 72, 164, 0.58);
  --abrak-accent: #6ee7ff;

  --rcx-color-font-default: var(--abrak-fg) !important;
  --rcx-color-font-titles-labels: #ffffff !important;
  --rcx-color-font-hint: var(--abrak-soft) !important;
  --rcx-color-surface-room: transparent !important;
  --rcx-color-surface-light: var(--abrak-panel) !important;
  --rcx-color-surface-tint: var(--abrak-panel-strong) !important;
  --rcx-color-stroke-light: var(--abrak-line) !important;
  --rcx-color-stroke-extra-light: rgba(114, 171, 235, 0.08) !important;
  --rcx-color-button-primary-default: #12327a !important;
  --rcx-color-button-primary-hover: #1b459d !important;
  --rcx-color-button-primary-active: #193f8e !important;
}

html,
body,
#app,
#react-root,
#react-root > div {
  background: transparent !important;
  color: var(--abrak-fg) !important;
}

body {
  margin: 0;
  min-height: 100vh;
  font-family: var(--abrak-font);
  background:
    radial-gradient(980px 520px at 18% 10%, rgba(30, 106, 194, 0.32), transparent 61%),
    radial-gradient(1020px 540px at 84% 6%, rgba(82, 66, 199, 0.22), transparent 58%),
    linear-gradient(158deg, #07080d 0%, #080d1d 45%, #101128 100%) !important;
}

#react-root {
  position: relative;
  min-height: 100vh;
}

#react-root::before {
  content: "";
  position: fixed;
  inset: 0;
  pointer-events: none;
  opacity: 0.32;
  background-image:
    radial-gradient(circle at 8% 16%, rgba(191, 219, 254, 0.85) 0 1px, transparent 1.5px),
    radial-gradient(circle at 22% 62%, rgba(125, 211, 252, 0.88) 0 1.2px, transparent 1.8px),
    radial-gradient(circle at 43% 24%, rgba(224, 242, 254, 0.7) 0 1px, transparent 1.4px),
    radial-gradient(circle at 66% 48%, rgba(110, 231, 255, 0.82) 0 1px, transparent 1.6px),
    radial-gradient(circle at 84% 14%, rgba(125, 211, 252, 0.78) 0 1.2px, transparent 1.8px),
    radial-gradient(circle at 92% 72%, rgba(191, 219, 254, 0.7) 0 1px, transparent 1.6px);
}

#react-root::after {
  content: "";
  position: fixed;
  inset: 0;
  pointer-events: none;
  opacity: 0.58;
  background:
    linear-gradient(104deg, rgba(19, 68, 150, 0.21) 18%, transparent 42%),
    linear-gradient(312deg, rgba(55, 33, 120, 0.17) 12%, transparent 35%);
}

[data-qa-id='sidebar-wrapper'],
[data-qa-id='sidebar'],
[data-qa-id='sidebar-content'],
.rcx-sidebar {
  background: linear-gradient(180deg, rgba(8, 22, 62, 0.88), rgba(5, 14, 36, 0.86)) !important;
  backdrop-filter: blur(10px);
}

[data-qa-id='sidebar'],
.rcx-sidebar {
  border-inline-end: 1px solid var(--abrak-line) !important;
}

[data-qa-id='main-content'],
.messages-box,
.messages-container,
.rcx-thread,
.rcx-contextualbar {
  background: transparent !important;
}

[data-qa-id='sidebar-header'],
[data-qa-id='room-header'],
.rcx-sidebar__header,
.rcx-room-header,
.rcx-room-header__content,
.rcx-box--header {
  background: rgba(10, 30, 78, 0.83) !important;
  border-bottom: 1px solid var(--abrak-line) !important;
  backdrop-filter: blur(8px);
}

[data-qa-id='room-list-item'],
[class*='rooms-list__item'],
[class*='rcx-sidebar-item'] {
  background: var(--abrak-chip) !important;
  border: 1px solid rgba(110, 231, 255, 0.17) !important;
  border-radius: 14px !important;
  margin: 3px 8px !important;
  color: var(--abrak-fg) !important;
  box-shadow: none !important;
}

[data-qa-id='room-list-item'][aria-current='true'],
[data-qa-id='room-list-item'][aria-selected='true'],
[class*='rooms-list__item'][aria-current='true'],
[class*='rooms-list__item'][aria-selected='true'],
[class*='rooms-list__item--active'] {
  background: linear-gradient(95deg, rgba(17, 63, 147, 0.68), var(--abrak-chip-active)) !important;
  border-color: var(--abrak-line-strong) !important;
  box-shadow: 0 0 0 1px rgba(102, 196, 255, 0.16) inset;
}

.rcx-box,
.rcx-modal,
.rcx-tile,
.rcx-table,
.rcx-contextualbar {
  background: var(--abrak-panel) !important;
  border: 1px solid rgba(110, 231, 255, 0.12) !important;
  border-radius: 18px !important;
  backdrop-filter: blur(8px);
}

[data-qa-id='message-box'],
[class*='message-box'],
[class*='composer'],
[class*='message-form'],
.message-form {
  background: var(--abrak-panel-strong) !important;
  border: 1px solid rgba(110, 231, 255, 0.16) !important;
  border-radius: 18px !important;
}

[data-qa-id='messages-box'],
.messages-box,
.messages-container {
  border: 0 !important;
}

input,
textarea,
.rcx-input,
.rcx-input-box {
  color: var(--abrak-fg) !important;
  background: transparent !important;
  border-color: rgba(110, 231, 255, 0.14) !important;
}

input::placeholder,
textarea::placeholder,
.rcx-input::placeholder {
  color: var(--abrak-soft) !important;
}

[data-qa-id='main-content'],
[data-qa-id='sidebar'],
.messages-container,
.messages-box,
.rcx-box,
.rcx-room-header,
.rcx-sidebar-item,
.rcx-input-box,
.rcx-input,
.rcx-button,
.rcx-message {
  color: var(--abrak-fg) !important;
}

[data-qa-id='connection-status'],
[class*='connection-status'],
[class*='reconnecting'] {
  background: rgba(8, 24, 62, 0.84) !important;
  border: 1px solid rgba(110, 231, 255, 0.22) !important;
  color: var(--abrak-soft) !important;
}

a {
  color: var(--abrak-accent) !important;
}

a:hover {
  color: #d9f1ff !important;
}

[data-qa-id='sidebar-footer'],
[data-qa-id*='powered'],
[class*='powered'],
[class*='sidebar-footer'],
[id*='powered-by'],
[class*='about-app'],
a[href*='rocket.chat'],
a[href*='rocketchat'],
a[href*='open.rocket.chat'],
a[href*='forums.rocket.chat'],
a[href*='docs.rocket.chat'] {
  display: none !important;
}

.abrak-lang-switcher {
  position: fixed;
  top: 18px;
  inset-inline-start: 16px;
  z-index: 2147483000;
  display: inline-flex;
  gap: 6px;
  padding: 5px;
  border-radius: 999px;
  background: rgba(8, 20, 52, 0.9);
  border: 1px solid rgba(110, 231, 255, 0.22);
  box-shadow: 0 10px 28px rgba(2, 8, 23, 0.36);
}

html[dir='ltr'] .abrak-lang-switcher {
  inset-inline-end: 16px;
  inset-inline-start: auto;
}

.abrak-lang-switcher button {
  border: 1px solid transparent;
  background: transparent;
  color: #dbeafe;
  min-width: 48px;
  height: 30px;
  border-radius: 999px;
  font-size: 12px;
  line-height: 1;
  cursor: pointer;
}

.abrak-lang-switcher button.active {
  border-color: rgba(110, 231, 255, 0.52);
  background: rgba(96, 165, 250, 0.2);
  color: #ffffff;
}

html[dir='rtl'] body,
html[dir='rtl'] .messages-box,
html[dir='rtl'] .messages-container,
html[dir='rtl'] .message,
html[dir='rtl'] .rcx-sidebar,
html[dir='rtl'] .rcx-box,
html[dir='rtl'] .rcx-table,
html[dir='rtl'] [class*='message-content'],
html[dir='rtl'] [class*='sidebar'] {
  direction: rtl !important;
  text-align: right !important;
}
`;

const customScript = `(() => {
  const MARK = 'ABRAK_CHAT_CUSTOM_V13';
  if (window.__abrakChatBootedV13) return;
  window.__abrakChatBootedV13 = true;

  const landingUrl = 'https://abrak.org';
  const contactUrl = 'https://abrak.org/contact';
  const storeUrl = 'https://abrak.org/store';
  const hubLoginUrl = 'https://hub.abrak.org/tenant/login';
  const logoSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none"><defs><linearGradient id="g" x1="12" y1="12" x2="52" y2="52"><stop stop-color="#6ee7ff"/><stop offset="1" stop-color="#8b5cf6"/></linearGradient></defs><circle cx="32" cy="32" r="30" stroke="url(#g)" stroke-width="2"/><path d="M20 42L32 18L44 42" stroke="url(#g)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><circle cx="32" cy="46" r="3.5" fill="#f4b26b"/></svg>';
  const logoData = 'data:image/svg+xml;utf8,' + encodeURIComponent(logoSvg);

  const countryFa = new Set(['IR', 'AF', 'TJ']);
  const countryAr = new Set(['AE', 'BH', 'DZ', 'EG', 'IQ', 'JO', 'KW', 'LB', 'LY', 'MA', 'MR', 'OM', 'PS', 'QA', 'SA', 'SD', 'SO', 'SY', 'TN', 'YE']);

  const phraseMap = {
    fa: {
      'Home': 'خانه',
      'Teams': 'تیم‌ها',
      'Channels': 'کانال‌ها',
      'Search rooms': 'جستجوی اتاق‌ها',
      'Welcome to Abrak Chat': 'به چت ابرک خوش آمدید',
      'Some ideas to get you started': 'چند پیشنهاد برای شروع',
      'Mobile apps': 'اپلیکیشن موبایل',
      'Take Abrak Chat with you with mobile applications': 'چت ابرک را در موبایل همراه خود داشته باشید',
      'Take Rocket.Chat with you with mobile applications': 'چت ابرک را در موبایل همراه خود داشته باشید',
      'Join rooms': 'پیوستن به اتاق ها',
      'Discover public channels and teams in the workspace directory': 'کانال ها و تیم های عمومی را در فهرست فضای کاری پیدا کنید',
      'Open directory': 'باز کردن فهرست',
      'Documentation': 'مستندات',
      'Learn how to unlock the myriad possibilities of Abrak Chat': 'راهنمای کامل استفاده از قابلیت های چت ابرک',
      'Learn how to unlock the myriad possibilities of Rocket.Chat': 'راهنمای کامل استفاده از قابلیت های چت ابرک',
      'Desktop apps': 'اپلیکیشن دسکتاپ',
      'Install Abrak Chat on your preferred desktop platform': 'نسخه دسکتاپ چت ابرک را روی سیستم خود نصب کنید',
      'Install Rocket.Chat on your preferred desktop platform': 'نسخه دسکتاپ چت ابرک را روی سیستم خود نصب کنید',
      'See documentation': 'مشاهده مستندات',
      'Powered by': 'قدرت گرفته از',
      'Powered by Abrak Chat': 'قدرت گرفته از چت ابرک',
      'Community': 'جامعه',
      'App Store': 'اپ استور',
      'Google Play': 'گوگل پلی',
    },
    ar: {
      'Home': 'الرئيسية',
      'Teams': 'الفرق',
      'Channels': 'القنوات',
      'Search rooms': 'ابحث في الغرف',
      'Welcome to Abrak Chat': 'مرحبا بك في دردشة Abrak',
      'Some ideas to get you started': 'بعض الأفكار للبدء',
      'Mobile apps': 'تطبيقات الجوال',
      'Join rooms': 'الانضمام إلى الغرف',
      'Open directory': 'فتح الدليل',
      'Documentation': 'التوثيق',
      'Desktop apps': 'تطبيقات سطح المكتب',
      'See documentation': 'عرض التوثيق',
      'Powered by': 'مدعوم بواسطة',
      'Powered by Abrak Chat': 'مدعوم بواسطة دردشة Abrak',
      'Community': 'المجتمع',
      'App Store': 'متجر التطبيقات',
      'Google Play': 'جوجل بلاي',
    },
  };

  const normalizeLang = (lang) => {
    const value = String(lang || '').trim().toLowerCase();
    if (value.startsWith('fa')) return 'fa';
    if (value.startsWith('ar')) return 'ar';
    if (value.startsWith('en')) return 'en';
    return '';
  };

  const getCookie = (key) => {
    const chunks = String(document.cookie || '').split(';');
    for (const chunk of chunks) {
      const idx = chunk.indexOf('=');
      if (idx === -1) continue;
      const name = chunk.slice(0, idx).trim();
      if (name !== key) continue;
      try {
        return decodeURIComponent(chunk.slice(idx + 1).trim());
      } catch (_) {
        return chunk.slice(idx + 1).trim();
      }
    }
    return '';
  };

  const setCookie = (key, value) => {
    document.cookie = key + '=' + encodeURIComponent(value) + '; Path=/; Max-Age=31536000; SameSite=Lax';
  };

  const getStoredLang = () => {
    try {
      return localStorage.getItem('userLanguage') || '';
    } catch (_) {
      return '';
    }
  };

  const hasManualLangChoice = () => {
    try {
      return localStorage.getItem('abrak.lang.manual') === '1';
    } catch (_) {
      return false;
    }
  };

  const setManualLangChoice = () => {
    try {
      localStorage.setItem('abrak.lang.manual', '1');
    } catch (_) {}
  };

  const inferFromCountry = (countryCode) => {
    const code = String(countryCode || '').trim().toUpperCase();
    if (!code) return '';
    if (countryFa.has(code)) return 'fa';
    if (countryAr.has(code)) return 'ar';
    return 'en';
  };

  const inferFromTimezone = () => {
    try {
      const tz = String(Intl.DateTimeFormat().resolvedOptions().timeZone || '').toLowerCase();
      if (!tz) return '';
      if (/tehran|kabul|dushanbe/.test(tz)) return 'fa';
      if (/riyadh|dubai|doha|kuwait|baghdad|muscat|manama|amman|beirut|damascus|cairo|tripoli|tunis|algiers|casablanca|rabat|khartoum|aden|gaza|jerusalem/.test(tz)) return 'ar';
    } catch (_) {}
    return '';
  };

  const inferFromNavigator = () => {
    const langs = [];
    try {
      if (Array.isArray(navigator.languages)) langs.push(...navigator.languages);
      if (navigator.language) langs.push(navigator.language);
    } catch (_) {}

    for (const item of langs) {
      const normalized = normalizeLang(item);
      if (normalized) return normalized;
    }

    return 'en';
  };

  const readCountryHeader = async () => {
    try {
      const response = await fetch('/api/info', {
        method: 'GET',
        credentials: 'include',
        cache: 'no-store',
      });

      const headerNames = [
        'cf-ipcountry',
        'x-country-code',
        'x-country',
        'x-geo-country',
        'x-geoip-country-code',
        'x-vercel-ip-country',
        'x-appengine-country',
      ];

      for (const headerName of headerNames) {
        const value = response.headers.get(headerName);
        if (value) return value;
      }
    } catch (_) {}
    return '';
  };

  const getCurrentLang = () => {
    const docLang = normalizeLang(document.documentElement.getAttribute('lang'));
    if (docLang) return docLang;
    const cookieLang = normalizeLang(getCookie('rc_language'));
    if (cookieLang) return cookieLang;
    const storedLang = normalizeLang(getStoredLang());
    if (storedLang) return storedLang;
    return 'fa';
  };

  const syncUserLanguagePreference = async (lang) => {
    const userId = getCookie('rc_uid');
    const token = getCookie('rc_token');
    if (!userId || !token) return;

    try {
      await fetch('/api/v1/users.setPreferences', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Auth-Token': token,
          'X-User-Id': userId,
        },
        body: JSON.stringify({ data: { language: lang } }),
      });
    } catch (_) {}
  };

  const applyLang = (lang, persist = true, manual = false) => {
    const normalized = normalizeLang(lang) || 'fa';
    if (persist) {
      setCookie('rc_language', normalized);
      try {
        localStorage.setItem('userLanguage', normalized);
      } catch (_) {}
      syncUserLanguagePreference(normalized);
    }
    if (manual) setManualLangChoice();

    document.documentElement.setAttribute('lang', normalized);
    document.documentElement.setAttribute('dir', (normalized === 'fa' || normalized === 'ar') ? 'rtl' : 'ltr');
    return normalized;
  };

  const applyThemeClass = () => {
    document.documentElement.classList.add('abrak-chat-theme');
    document.documentElement.style.colorScheme = 'dark';
    if (document.body) document.body.classList.add('abrak-chat-theme');
  };

  const hasSession = () => {
    const cookieState = /(?:^|;\\s*)(rc_uid|rc_token)=/.test(document.cookie || '');
    const storageState = (() => {
      try {
        return Boolean(localStorage.getItem('Meteor.loginToken') || localStorage.getItem('Meteor.userId'));
      } catch (_) {
        return false;
      }
    })();
    return cookieState || storageState;
  };

  const shouldBypassRedirect = (path) => {
    const prefixes = [
      '/oauth/authorize',
      '/oauth/complete',
      '/_oauth/',
      '/api/',
      '/sockjs/',
      '/websocket',
      '/livechat',
      '/assets/',
      '/fonts/',
    ];
    return prefixes.some((prefix) => path.startsWith(prefix));
  };

  const mountLangSwitcher = () => {
    if (!document.body) return;
    if (document.querySelector('.abrak-lang-switcher')) return;

    const labels = {
      fa: 'فارسی',
      ar: 'العربية',
      en: 'EN',
    };

    const wrapper = document.createElement('div');
    wrapper.className = 'abrak-lang-switcher';
    wrapper.setAttribute('data-mark', MARK);

    const activeLang = getCurrentLang();
    ['fa', 'ar', 'en'].forEach((lang) => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.textContent = labels[lang];
      if (lang === activeLang) btn.classList.add('active');
      btn.addEventListener('click', () => {
        applyLang(lang, true, true);
        window.location.reload();
      });
      wrapper.appendChild(btn);
    });

    document.body.appendChild(wrapper);
  };

  const replaceBrandingAndTexts = () => {
    document.title = String(document.title || '')
      .replace(/Rocket\\.Chat/gi, 'Abrak Chat')
      .replace(/راکت\\s*چت/gi, 'چت ابرک');

    const lang = getCurrentLang();
    const langMap = phraseMap[lang] || {};

    const shouldSkipNode = (node) => {
      const parent = node && node.parentElement;
      if (!parent) return true;
      const tag = String(parent.tagName || '').toLowerCase();
      if (tag === 'script' || tag === 'style' || tag === 'svg' || tag === 'path' || tag === 'i') return true;
      const cls = String(parent.className || '').toLowerCase();
      if (cls.includes('icon') || cls.includes('emoji') || cls.includes('avatar')) return true;
      if (parent.closest('svg, i, [class*=\"icon\"], [class*=\"emoji\"], [class*=\"avatar\"]')) return true;
      const raw = String(node.nodeValue || '').trim();
      if (!raw) return true;
      if (parent.closest('button, [role=\"button\"]') && /^[a-z_]{2,24}$/i.test(raw)) return true;
      return false;
    };

    const walker = document.createTreeWalker(document.body || document.documentElement, NodeFilter.SHOW_TEXT);
    while (walker.nextNode()) {
      const node = walker.currentNode;
      if (!node || !node.nodeValue) continue;
      if (shouldSkipNode(node)) continue;

      let value = node.nodeValue
        .replace(/Rocket\\.Chat/gi, 'Abrak Chat')
        .replace(/rocket\\.chat/gi, 'Abrak Chat')
        .replace(/راکت\\s*چت/gi, 'چت ابرک');

      for (const [source, target] of Object.entries(langMap)) {
        if (value.includes(source)) {
          value = value.split(source).join(target);
        }
      }

      if (value !== node.nodeValue) {
        node.nodeValue = value;
      }
    }

    document.querySelectorAll('img').forEach((img) => {
      const src = String(img.getAttribute('src') || '').toLowerCase();
      const alt = String(img.getAttribute('alt') || '').toLowerCase();
      if (src.includes('logo') || src.includes('rocket') || src.includes('rocketchat') || alt.includes('rocket')) {
        img.setAttribute('src', logoData);
        img.setAttribute('alt', 'Abrak');
      }
    });

    document.querySelectorAll('a[href]').forEach((anchor) => {
      const href = String(anchor.getAttribute('href') || '').trim();
      if (!href) return;
      if (/rocket\\.chat|rocketchat|open\\.rocket\\.chat|forums\\.rocket\\.chat|docs\\.rocket\\.chat/i.test(href)) {
        anchor.setAttribute('href', landingUrl);
        if (!anchor.textContent || /rocket\\.chat/i.test(anchor.textContent)) {
          anchor.textContent = 'Abrak';
        }

        const footer = anchor.closest('footer, [data-qa-id*="footer"], [class*="footer"], [class*="powered"]');
        if (footer) {
          footer.style.display = 'none';
        }

        const block = anchor.closest('div, section, aside');
        if (block && /rocket\\.chat|powered by|community/i.test(String(block.textContent || ''))) {
          block.style.display = 'none';
        }
      }
    });

    document.querySelectorAll('footer, [data-qa-id*="footer"], [class*="footer"], [class*="powered"], [class*="about-app"]').forEach((el) => {
      const txt = String(el.textContent || '').toLowerCase();
      if (txt.includes('rocket.chat') || txt.includes('powered by') || txt.includes('community')) {
        el.style.display = 'none';
      }
    });

    document.querySelectorAll('[title], [aria-label]').forEach((el) => {
      const title = String(el.getAttribute('title') || '');
      const aria = String(el.getAttribute('aria-label') || '');
      if (/rocket\\.chat|rocketchat/i.test(title)) {
        el.setAttribute('title', title.replace(/rocket\\.chat|rocketchat/gi, 'Abrak Chat'));
      }
      if (/rocket\\.chat|rocketchat/i.test(aria)) {
        el.setAttribute('aria-label', aria.replace(/rocket\\.chat|rocketchat/gi, 'Abrak Chat'));
      }
    });
  };

  const autoSelectLanguage = async () => {
    const manualExplicit = normalizeLang(getStoredLang() || getCookie('rc_language'));
    if (hasManualLangChoice() && manualExplicit) {
      applyLang(manualExplicit, true);
      return manualExplicit;
    }

    const sessionKey = 'abrak.auto-lang.v13';
    let alreadyChecked = false;
    try {
      alreadyChecked = sessionStorage.getItem(sessionKey) === '1';
    } catch (_) {}

    if (alreadyChecked) {
      const cachedLang = normalizeLang(getCookie('rc_language') || getStoredLang() || inferFromTimezone() || 'fa') || 'fa';
      applyLang(cachedLang, true);
      return cachedLang;
    }

    const country = await readCountryHeader();
    const countryLang = normalizeLang(inferFromCountry(country));

    try {
      sessionStorage.setItem(sessionKey, '1');
    } catch (_) {}

    if (countryLang) {
      applyLang(countryLang, true);
      return countryLang;
    }

    const timezoneLang = normalizeLang(inferFromTimezone());
    if (timezoneLang) {
      applyLang(timezoneLang, true);
      return timezoneLang;
    }

    const fallback = normalizeLang(inferFromNavigator() || 'fa') || 'fa';
    applyLang(fallback, true);
    return fallback;
  };

  const boot = async () => {
    applyThemeClass();
    await autoSelectLanguage();
    mountLangSwitcher();
    replaceBrandingAndTexts();

    const path = String(window.location.pathname || '/');
    if (path.startsWith('/register') || path.startsWith('/signup')) {
      window.location.replace(storeUrl);
      return;
    }

    if (path.startsWith('/oauth/error')) {
      window.location.replace(contactUrl);
      return;
    }

    if (!hasSession() && !shouldBypassRedirect(path)) {
      window.location.replace(hubLoginUrl);
      return;
    }

    setInterval(() => {
      applyThemeClass();
      mountLangSwitcher();
      replaceBrandingAndTexts();
    }, 12000);
  };

  const run = () => {
    boot().catch(() => {});
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', run, { once: true });
  } else {
    run();
  }
})();`;

const customTranslations = {
  en: {
    Rocket_Chat: 'Abrak Chat',
    Sign_in_with: 'Sign in with Abrak Hub',
    Home: 'Home',
    Teams: 'Teams',
    Channels: 'Channels',
    Odoo: 'Abrak',
  },
  fa: {
    Rocket_Chat: 'چت ابرک',
    Sign_in_with: 'ورود با هاب ابرک',
    Login_with: 'ورود با',
    Logout: 'خروج',
    Search: 'جستجو',
    Save_changes: 'ذخیره تغییرات',
    Cancel: 'انصراف',
    Loading: 'در حال بارگذاری',
    Welcome_to_Abrak_Chat: 'به چت ابرک خوش آمدید',
    Some_ideas_to_get_you_started: 'چند پیشنهاد برای شروع',
    Mobile_apps: 'اپلیکیشن موبایل',
    Join_rooms: 'پیوستن به اتاق ها',
    Open_directory: 'باز کردن فهرست',
    Desktop_apps: 'اپلیکیشن دسکتاپ',
    Home: 'خانه',
    Teams: 'تیم‌ها',
    Channels: 'کانال‌ها',
    Search_rooms: 'جستجوی اتاق‌ها',
    Presence: 'وضعیت حضور',
    Away: 'غایب',
    Online: 'آنلاین',
    Odoo: 'ابرک',
  },
  ar: {
    Rocket_Chat: 'دردشة أبرك',
    Sign_in_with: 'الدخول عبر Abrak Hub',
    Welcome_to_Abrak_Chat: 'مرحبا بك في دردشة Abrak',
    Some_ideas_to_get_you_started: 'بعض الأفكار للبدء',
    Mobile_apps: 'تطبيقات الجوال',
    Join_rooms: 'الانضمام إلى الغرف',
    Open_directory: 'فتح الدليل',
    Desktop_apps: 'تطبيقات سطح المكتب',
    Home: 'الرئيسية',
    Teams: 'الفرق',
    Channels: 'القنوات',
    Search_rooms: 'ابحث في الغرف',
    Odoo: 'أبرك',
  },
};

const settingsUpdates = {
  Site_Name: 'Abrak Chat',
  Site_Url: 'https://chat.abrak.org',
  Language: '',
  Meta_language: 'fa',
  Accounts_ShowFormLogin: false,
  Accounts_AllowUserRegistration: false,
  Accounts_RegistrationForm: 'Disabled',
  Accounts_Default_User_Preferences_language: 'fa',
  Layout_Login_Hide_Powered_By: true,
  Layout_Login_Hide_Title: false,
  Layout_Login_Hide_Logo: false,
  Layout_Custom_CSS: customCss,
  'theme-custom-css': customCss,
  Custom_Script_Logged_Out: customScript,
  Custom_Script_Logged_In: customScript,
  Custom_Script_On_Logout: customScript,
  Custom_Translations: JSON.stringify(customTranslations),
  'Accounts_OAuth_Custom-Abrak-button_label_text': 'ورود با هاب ابرک',
  'Accounts_OAuth_Custom-Abrak-button_color': '#0b1b42',
  'Accounts_OAuth_Custom-Abrak-button_label_color': '#e8f2ff',
  'Accounts_OAuth_Custom-Abrak-show_button': true,
  'Accounts_OAuth_Custom-Abrak-login_style': 'redirect',
};

for (const [key, value] of Object.entries(settingsUpdates)) {
  db.rocketchat_settings.updateOne(
    { _id: key },
    { $set: { value, _updatedAt: now } },
    { upsert: true },
  );
}

db.users.updateMany(
  { $or: [{ language: { $exists: false } }, { language: null }, { language: '' }] },
  { $set: { language: 'fa' } },
);

db.users.updateMany(
  { 'settings.preferences.language': { $exists: false } },
  { $set: { 'settings.preferences.language': 'fa' } },
);

db.meteor_accounts_loginServiceConfiguration.updateOne(
  { service: 'abrak' },
  {
    $set: {
      loginStyle: 'redirect',
      showButton: true,
      buttonLabelText: 'ورود با هاب ابرک',
      buttonColor: '#0b1b42',
      buttonLabelColor: '#e8f2ff',
    },
  },
  { upsert: false },
);

printjson({
  appliedAt: new Date().toISOString(),
  siteName: db.rocketchat_settings.findOne({ _id: 'Site_Name' })?.value,
  language: db.rocketchat_settings.findOne({ _id: 'Language' })?.value,
  metaLanguage: db.rocketchat_settings.findOne({ _id: 'Meta_language' })?.value,
  customCssLength: (db.rocketchat_settings.findOne({ _id: 'Layout_Custom_CSS' })?.value || '').length,
  themeCustomCssLength: (db.rocketchat_settings.findOne({ _id: 'theme-custom-css' })?.value || '').length,
  customLoggedOutLength: (db.rocketchat_settings.findOne({ _id: 'Custom_Script_Logged_Out' })?.value || '').length,
  customLoggedInLength: (db.rocketchat_settings.findOne({ _id: 'Custom_Script_Logged_In' })?.value || '').length,
  customTranslationsLength: String(db.rocketchat_settings.findOne({ _id: 'Custom_Translations' })?.value || '').length,
  oauthButtonLabel: db.rocketchat_settings.findOne({ _id: 'Accounts_OAuth_Custom-Abrak-button_label_text' })?.value,
  oauthButtonColor: db.rocketchat_settings.findOne({ _id: 'Accounts_OAuth_Custom-Abrak-button_color' })?.value,
  oauth: db.meteor_accounts_loginServiceConfiguration.findOne({ service: 'abrak' }),
});
