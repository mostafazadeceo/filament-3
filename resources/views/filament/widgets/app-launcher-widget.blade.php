@php
    $apps = $apps ?? [];
    $activeKey = $activeKey ?? null;
@endphp

@once
    <script>
        (function () {
            const updateLayoutFlag = () => {
                const hasLauncher = !!document.querySelector('.fi-abrak-app-launcher');
                document.documentElement.classList.toggle('abrak-has-launcher', hasLauncher);
            };

            const toSlug = (value) => String(value || '')
                .trim()
                .toLowerCase()
                .replace(/[^\p{L}\p{N}]+/gu, '-')
                .replace(/^-+|-+$/g, '');

            const uniq = (arr) => Array.from(new Set((arr || []).filter(Boolean)));

            window.abrakLauncher = function (appsInput, activeKey) {
                const apps = (appsInput || []).map((app) => {
                    const key = String(app.key || '').trim();
                    return {
                        key,
                        label: String(app.label || key || 'App'),
                        description: String(app.description || ''),
                        url: String(app.url || '/'),
                        external: !!app.external,
                        search: (String(app.label || '') + ' ' + String(app.description || '') + ' ' + key).toLowerCase(),
                    };
                }).filter((app) => app.key !== '');

                const appMap = Object.fromEntries(apps.map((app) => [app.key, app]));
                const storageKey = 'abrak.launcher.layout.v1';

                const defaultSectionMeta = [
                    { id: 'general', title: 'ماژول‌های عمومی' },
                    { id: 'sales', title: 'ماژول‌های فروش و فروشگاه' },
                    { id: 'website', title: 'ماژول‌های سایت و محتوا' },
                    { id: 'operations', title: 'ماژول‌های عملیات و پیگیری' },
                    { id: 'finance', title: 'ماژول‌های مالی و حسابداری' },
                    { id: 'iam', title: 'ماژول‌های دسترسی و امنیت' },
                    { id: 'communication', title: 'ماژول‌های ارتباطات و اعلان' },
                    { id: 'hr', title: 'ماژول‌های منابع انسانی' },
                    { id: 'reports', title: 'ماژول‌های گزارش و تحلیل' },
                    { id: 'other', title: 'سایر ابزارها' },
                ];

                const classify = (app) => {
                    const text = (app.label + ' ' + app.description + ' ' + app.key).toLowerCase();

                    if (/chat|اعلان|mail|notification|sms|پیام|telegram|whatsapp|3cx/.test(text)) return 'communication';
                    if (/hr|payroll|attendance|employee|leave|کارمند|حقوق|مرخصی/.test(text)) return 'hr';
                    if (/permission|role|user|auth|access|iam|امنیت|مجوز|نقش/.test(text)) return 'iam';
                    if (/blog|cms|site|page|seo|landing|content|سایت|محتوا|وبلاگ/.test(text)) return 'website';
                    if (/store|catalog|product|order|checkout|pos|marketplace|commerce|فروش|سفارش|محصول|فروشگاه/.test(text)) return 'sales';
                    if (/wallet|invoice|account|treasury|tax|petty|finance|crypto|حساب|مالی|کیف پول|فاکتور/.test(text)) return 'finance';
                    if (/report|dashboard|analysis|audit|گزارش|تحلیل|داشبورد/.test(text)) return 'reports';
                    if (/task|project|workflow|ticket|workhub|operation|عملیات|پروژه|گردش/.test(text)) return 'operations';

                    return 'general';
                };

                const buildDefaultState = () => {
                    const sections = defaultSectionMeta.map((meta) => ({ ...meta, keys: [] }));
                    const byId = Object.fromEntries(sections.map((section) => [section.id, section]));

                    apps.forEach((app) => {
                        const sectionId = classify(app);
                        (byId[sectionId] || byId.other).keys.push(app.key);
                    });

                    const favorites = apps.slice(0, 8).map((app) => app.key);

                    return {
                        favorites,
                        sections,
                    };
                };

                const sanitizeState = (state) => {
                    const defaults = buildDefaultState();

                    let sections = Array.isArray(state?.sections) ? state.sections : defaults.sections;
                    let favorites = Array.isArray(state?.favorites) ? state.favorites : defaults.favorites;

                    sections = sections
                        .map((section, index) => {
                            const id = toSlug(section?.id || '') || `section-${index + 1}`;
                            const title = String(section?.title || '').trim() || `بخش ${index + 1}`;
                            const keys = uniq((section?.keys || []).filter((key) => !!appMap[key]));
                            return { id, title, keys };
                        })
                        .filter((section, index, arr) => arr.findIndex((s) => s.id === section.id) === index);

                    if (!sections.length) {
                        sections = defaults.sections;
                    }

                    const assigned = new Set();
                    sections.forEach((section) => {
                        section.keys = section.keys.filter((key) => {
                            if (assigned.has(key)) return false;
                            assigned.add(key);
                            return true;
                        });
                    });

                    const missing = apps.map((app) => app.key).filter((key) => !assigned.has(key));
                    if (missing.length) {
                        const fallback = sections.find((section) => section.id === 'other') || sections[sections.length - 1];
                        fallback.keys.push(...missing);
                    }

                    favorites = uniq(favorites.filter((key) => !!appMap[key]));

                    return { sections, favorites };
                };

                const loadState = () => {
                    try {
                        const raw = localStorage.getItem(storageKey);
                        if (!raw) return sanitizeState(null);
                        return sanitizeState(JSON.parse(raw));
                    } catch (_error) {
                        return sanitizeState(null);
                    }
                };

                const persist = (state) => {
                    try {
                        localStorage.setItem(storageKey, JSON.stringify({
                            favorites: state.favorites,
                            sections: state.sections,
                        }));
                    } catch (_error) {
                        // Intentionally ignore storage errors.
                    }
                };

                return {
                    q: '',
                    activeKey: activeKey || null,
                    apps,
                    appMap,
                    sections: [],
                    favorites: [],
                    dragKey: null,
                    dragSectionId: null,
                    editingSectionId: null,
                    editTitle: '',

                    init() {
                        updateLayoutFlag();

                        const state = loadState();
                        this.sections = state.sections;
                        this.favorites = state.favorites;
                        this.persist();
                    },

                    persist() {
                        persist({ sections: this.sections, favorites: this.favorites });
                    },

                    matches(app) {
                        if (!this.q) return true;
                        return app.search.includes(this.q.toLowerCase());
                    },

                    visibleKeys(keys) {
                        return (keys || []).filter((key) => {
                            const app = this.appMap[key];
                            return app ? this.matches(app) : false;
                        });
                    },

                    initials(label) {
                        const clean = String(label || '').trim();
                        if (!clean) return 'A';
                        const parts = clean.split(/\s+/).filter(Boolean);
                        if (parts.length === 1) {
                            return parts[0].slice(0, 2).toUpperCase();
                        }
                        return (parts[0][0] + parts[1][0]).toUpperCase();
                    },

                    findSection(sectionId) {
                        return this.sections.find((section) => section.id === sectionId) || null;
                    },

                    removeKeyEverywhere(key) {
                        this.favorites = this.favorites.filter((item) => item !== key);
                        this.sections.forEach((section) => {
                            section.keys = section.keys.filter((item) => item !== key);
                        });
                    },

                    startDragKey(event, key) {
                        this.dragKey = key;
                        this.dragSectionId = null;
                        event.dataTransfer.effectAllowed = 'move';
                        event.dataTransfer.setData('text/plain', key);
                    },

                    dropToFavorites(event, beforeKey = null) {
                        const key = event.dataTransfer.getData('text/plain') || this.dragKey;
                        if (!key || !this.appMap[key]) return;

                        this.removeKeyEverywhere(key);

                        if (!beforeKey || !this.favorites.includes(beforeKey)) {
                            this.favorites.push(key);
                        } else {
                            const index = this.favorites.indexOf(beforeKey);
                            this.favorites.splice(index, 0, key);
                        }

                        this.dragKey = null;
                        this.persist();
                    },

                    dropToSection(event, sectionId, beforeKey = null) {
                        const key = event.dataTransfer.getData('text/plain') || this.dragKey;
                        if (!key || !this.appMap[key]) return;

                        const section = this.findSection(sectionId);
                        if (!section) return;

                        this.removeKeyEverywhere(key);

                        if (!beforeKey || !section.keys.includes(beforeKey)) {
                            section.keys.push(key);
                        } else {
                            const index = section.keys.indexOf(beforeKey);
                            section.keys.splice(index, 0, key);
                        }

                        this.dragKey = null;
                        this.persist();
                    },

                    startDragSection(event, sectionId) {
                        this.dragSectionId = sectionId;
                        this.dragKey = null;
                        event.dataTransfer.effectAllowed = 'move';
                        event.dataTransfer.setData('text/section', sectionId);
                    },

                    dropSectionBefore(event, targetSectionId) {
                        const sourceId = event.dataTransfer.getData('text/section') || this.dragSectionId;
                        if (!sourceId || sourceId === targetSectionId) return;

                        const sourceIndex = this.sections.findIndex((section) => section.id === sourceId);
                        const targetIndex = this.sections.findIndex((section) => section.id === targetSectionId);
                        if (sourceIndex < 0 || targetIndex < 0) return;

                        const [moved] = this.sections.splice(sourceIndex, 1);
                        const nextTargetIndex = this.sections.findIndex((section) => section.id === targetSectionId);
                        this.sections.splice(nextTargetIndex, 0, moved);

                        this.dragSectionId = null;
                        this.persist();
                    },

                    startEditTitle(section) {
                        this.editingSectionId = section.id;
                        this.editTitle = section.title;
                    },

                    saveTitle(section) {
                        if (this.editingSectionId !== section.id) return;
                        const title = String(this.editTitle || '').trim();
                        if (title !== '') {
                            section.title = title.slice(0, 64);
                            this.persist();
                        }
                        this.editingSectionId = null;
                        this.editTitle = '';
                    },

                    cancelEditTitle() {
                        this.editingSectionId = null;
                        this.editTitle = '';
                    },
                };
            };

            updateLayoutFlag();
            window.addEventListener('livewire:navigated', updateLayoutFlag);
            window.addEventListener('filament:navigate', updateLayoutFlag);
        })();
    </script>
@endonce

<x-filament-widgets::widget class="fi-abrak-app-launcher">
    <x-filament::section class="abrak-app-shell">
        <div
            x-data="abrakLauncher(@js($apps), @js($activeKey))"
            class="abrak-app-layout"
        >
            <div class="abrak-app-header">
                <div class="abrak-app-title">
                    <div class="abrak-app-mark" aria-hidden="true">
                        <svg viewBox="0 0 64 64" fill="none" class="h-full w-full" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="32" cy="32" r="30" stroke="url(#grad)" stroke-width="2" />
                            <path d="M20 42L32 18L44 42" stroke="url(#grad)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                            <circle cx="32" cy="46" r="3.5" fill="var(--abrak-ember)" />
                            <defs>
                                <linearGradient id="grad" x1="12" y1="12" x2="52" y2="52">
                                    <stop stop-color="var(--abrak-aurora)" />
                                    <stop offset="1" stop-color="var(--abrak-pulse)" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                    <div>
                        <div class="abrak-app-heading">مرکز اپلیکیشن‌ها</div>
                        <div class="abrak-app-subtitle">مدیریت سریع ماژول‌ها با نمایش هوشمند، درگ‌دراپ و چیدمان قابل شخصی‌سازی.</div>
                    </div>
                </div>

                <div class="abrak-app-search">
                    <label class="sr-only" for="abrak-app-search">جستجو</label>
                    <input
                        id="abrak-app-search"
                        x-model.trim="q"
                        type="text"
                        placeholder="جستجو در اپلیکیشن‌ها…"
                        class="abrak-app-search-input"
                    />
                </div>
            </div>

            <section class="abrak-favorites" @dragover.prevent @drop.prevent="dropToFavorites($event)">
                <div class="abrak-section-header">
                    <h3>اپلیکیشن‌های پرکاربرد</h3>
                    <p>با Drag/Drop ترتیب را تغییر دهید.</p>
                </div>

                <div class="abrak-app-grid">
                    <template x-for="key in visibleKeys(favorites)" :key="`fav-${key}`">
                        <a
                            :href="appMap[key].url"
                            draggable="true"
                            @dragstart="startDragKey($event, key)"
                            @dragover.prevent
                            @drop.prevent="dropToFavorites($event, key)"
                            class="abrak-app-card"
                            :class="{ 'is-active': activeKey === key }"
                            x-bind:target="appMap[key].external ? '_blank' : null"
                            x-bind:rel="appMap[key].external ? 'noopener noreferrer' : null"
                        >
                            <div class="abrak-app-card__icon" x-text="initials(appMap[key].label)"></div>
                            <div class="abrak-app-card__body">
                                <div class="abrak-app-card__title" x-text="appMap[key].label"></div>
                                <div class="abrak-app-card__desc" x-text="appMap[key].description"></div>
                            </div>
                        </a>
                    </template>
                </div>
            </section>

            <template x-for="section in sections" :key="section.id">
                <section
                    class="abrak-module-section"
                    draggable="true"
                    @dragstart="startDragSection($event, section.id)"
                    @dragover.prevent
                    @drop.prevent="dropSectionBefore($event, section.id)"
                >
                    <div class="abrak-section-header">
                        <div class="abrak-section-title-wrap">
                            <button type="button" class="abrak-drag-handle" title="جابجایی بخش">≡</button>
                            <h3
                                x-show="editingSectionId !== section.id"
                                @dblclick="startEditTitle(section)"
                                x-text="section.title"
                            ></h3>
                            <input
                                x-show="editingSectionId === section.id"
                                x-model="editTitle"
                                @keydown.enter.prevent="saveTitle(section)"
                                @keydown.escape.prevent="cancelEditTitle()"
                                @blur="saveTitle(section)"
                                class="abrak-section-title-input"
                            />
                        </div>
                        <p>برای تغییر عنوان، دوبار کلیک کنید.</p>
                    </div>

                    <div class="abrak-section-divider"></div>

                    <div class="abrak-app-grid" @dragover.prevent @drop.prevent="dropToSection($event, section.id)">
                        <template x-for="key in visibleKeys(section.keys)" :key="`${section.id}-${key}`">
                            <a
                                :href="appMap[key].url"
                                draggable="true"
                                @dragstart="startDragKey($event, key)"
                                @dragover.prevent
                                @drop.prevent="dropToSection($event, section.id, key)"
                                class="abrak-app-card"
                                :class="{ 'is-active': activeKey === key }"
                                x-bind:target="appMap[key].external ? '_blank' : null"
                                x-bind:rel="appMap[key].external ? 'noopener noreferrer' : null"
                            >
                                <div class="abrak-app-card__icon" x-text="initials(appMap[key].label)"></div>
                                <div class="abrak-app-card__body">
                                    <div class="abrak-app-card__title" x-text="appMap[key].label"></div>
                                    <div class="abrak-app-card__desc" x-text="appMap[key].description"></div>
                                </div>
                            </a>
                        </template>
                    </div>
                </section>
            </template>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
