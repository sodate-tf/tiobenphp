{{-- resources/views/partials/header.blade.php --}}
@php
    /**
     * Header público IA Tio Ben
     * - Desktop: topo fixo com dropdown do Terço
     * - Mobile: navegação fixa inferior com painel do Terço
     * - Sem dependência de clique externo, Alpine ou jQuery
     * - CSS/JS embutidos com @once para não duplicar caso o partial seja incluído mais de uma vez
     */

    $pathRaw = '/' . ltrim(request()->path(), '/');
    $pathRaw = $pathRaw === '//' ? '/' : $pathRaw;

    $isEnPath = $pathRaw === '/en' || str_starts_with($pathRaw, '/en/');

    $rawLang = $lang ?? ($isEnPath ? 'en' : (app()->getLocale() ?? 'pt'));
    $rawLang = str_replace('_', '-', strtolower((string) $rawLang));
    $lang = str_starts_with($rawLang, 'en') ? 'en' : 'pt';

    $prefix = $lang === 'en' ? '/en' : '';
    $todaySlug = now('America/Sao_Paulo')->format('d-m-Y');

    $liturgyToday = $lang === 'en'
        ? "/en/daily-mass-readings/{$todaySlug}"
        : "/liturgia-diaria/{$todaySlug}";

    /**
     * Switch PT/EN preservando a URL atual quando possível.
     */
    $mapEnToPt = [
        '/daily-liturgy'       => '/liturgia-diaria',
        '/daily-mass-readings' => '/liturgia-diaria',
        '/rosary'              => '/santo-terco',
        '/year/'               => '/ano/',
        '/month/'              => '/mes/',
    ];

    $mapPtToEn = [
        '/liturgia-diaria' => '/daily-mass-readings',
        '/santo-terco'     => '/rosary',
        '/ano/'            => '/year/',
        '/mes/'            => '/month/',
    ];

    $baseNoLang = $pathRaw;
    if ($baseNoLang === '/en') {
        $baseNoLang = '/';
    } elseif (str_starts_with($baseNoLang, '/en/')) {
        $baseNoLang = substr($baseNoLang, 3); // remove apenas o prefixo /en
        $baseNoLang = $baseNoLang === '' ? '/' : $baseNoLang;
    }

    $ptSwitch = str_replace(array_keys($mapEnToPt), array_values($mapEnToPt), $baseNoLang);
    $enSwitch = $baseNoLang === '/' ? '/en' : '/en' . str_replace(array_keys($mapPtToEn), array_values($mapPtToEn), $baseNoLang);

    $qs = request()->getQueryString();
    if ($qs) {
        $ptSwitch .= (str_contains($ptSwitch, '?') ? '&' : '?') . $qs;
        $enSwitch .= (str_contains($enSwitch, '?') ? '&' : '?') . $qs;
    }

    $routes = [
        'home'    => $prefix ?: '/',
        'liturgy' => $liturgyToday,
        'rosary'  => $prefix . '/' . ($lang === 'en' ? 'rosary' : 'santo-terco'),
        'blog'    => $prefix . '/blog',
        'pt'      => $ptSwitch ?: '/',
        'en'      => $enSwitch ?: '/en',
    ];

    $rosarySubCanon = [
        [
            'pt' => 'Rezar o Terço',
            'en' => 'Pray the Rosary',
            'pt_href' => '/santo-terco',
            'en_href' => '/rosary',
        ],
        [
            'pt' => 'Mistérios Gozosos',
            'en' => 'Joyful Mysteries',
            'pt_href' => '/santo-terco/misterios-gozosos',
            'en_href' => '/rosary/joyful-mysteries',
        ],
        [
            'pt' => 'Mistérios Luminosos',
            'en' => 'Luminous Mysteries',
            'pt_href' => '/santo-terco/misterios-luminosos',
            'en_href' => '/rosary/luminous-mysteries',
        ],
        [
            'pt' => 'Mistérios Dolorosos',
            'en' => 'Sorrowful Mysteries',
            'pt_href' => '/santo-terco/misterios-dolorosos',
            'en_href' => '/rosary/sorrowful-mysteries',
        ],
        [
            'pt' => 'Mistérios Gloriosos',
            'en' => 'Glorious Mysteries',
            'pt_href' => '/santo-terco/misterios-gloriosos',
            'en_href' => '/rosary/glorious-mysteries',
        ],
    ];

    $rosarySub = array_map(static function (array $item) use ($lang): array {
        return [
            'label' => $item[$lang] ?? $item['pt'],
            'href'  => $lang === 'en' ? '/en' . $item['en_href'] : $item['pt_href'],
        ];
    }, $rosarySubCanon);

    $tAll = [
        'pt' => [
            'subtitle' => 'Liturgia diária e IA católica',
            'home' => 'Início',
            'liturgy' => 'Liturgia',
            'rosary' => 'Terço',
            'blog' => 'Blog',
            'lang' => 'Idioma',
            'open_menu' => 'Abrir menu do Terço',
            'close_menu' => 'Fechar menu',
        ],
        'en' => [
            'subtitle' => 'Daily Liturgy & Catholic AI',
            'home' => 'Home',
            'liturgy' => 'Daily Liturgy',
            'rosary' => 'Rosary',
            'blog' => 'Blog',
            'lang' => 'Language',
            'open_menu' => 'Open Rosary menu',
            'close_menu' => 'Close menu',
        ],
    ];

    $t = $tAll[$lang] ?? $tAll['pt'];
@endphp

@once
    <style id="tb-public-header-css">
        :root {
            --tb-header-h: 64px;
            --tb-mobile-nav-h: 68px;
            --tb-amber-50: #fffbeb;
            --tb-amber-100: #fef3c7;
            --tb-amber-700: #b45309;
            --tb-amber-800: #92400e;
            --tb-amber-900: #78350f;
            --tb-slate-50: #f8fafc;
            --tb-slate-100: #f1f5f9;
            --tb-slate-200: #e2e8f0;
            --tb-slate-700: #334155;
            --tb-slate-900: #0f172a;
        }

        .tb-public-header,
        .tb-public-header * {
            box-sizing: border-box;
        }

        .tb-public-header a {
            color: inherit;
            text-decoration: none;
        }

        .tb-public-header button {
            font: inherit;
        }

        .tb-header-wrap {
            width: min(100% - 32px, 1152px);
            margin-inline: auto;
        }

        .tb-desktop-topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            display: none;
            height: var(--tb-header-h);
            background: rgba(255, 255, 255, .96);
            border-bottom: 1px solid rgba(226, 232, 240, .95);
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
        }

        .tb-desktop-inner {
            height: var(--tb-header-h);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            min-width: 0;
            overflow: visible;
        }

        .tb-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            flex: 0 1 auto;
        }

        .tb-brand-logo {
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            border-radius: 10px;
            object-fit: contain;
            display: block;
        }

        .tb-brand-copy {
            min-width: 0;
            line-height: 1.1;
        }

        .tb-brand-title {
            color: var(--tb-amber-900);
            font-size: 16px;
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .tb-brand-subtitle {
            margin-top: 2px;
            color: rgba(120, 53, 15, .72);
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .tb-desktop-nav {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            min-width: 0;
            flex: 0 0 auto;
            color: rgba(69, 26, 3, .84);
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
        }

        .tb-nav-link,
        .tb-dd-btn {
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 0 10px;
            border: 0;
            border-radius: 10px;
            background: transparent;
            color: inherit;
            cursor: pointer;
            text-decoration: none;
            transition: background-color .16s ease, color .16s ease;
        }

        .tb-nav-link:hover,
        .tb-dd-btn:hover,
        .tb-dd-btn[aria-expanded="true"] {
            background: var(--tb-amber-50);
            color: var(--tb-amber-900);
        }

        .tb-dd {
            position: relative;
            display: inline-flex;
        }

        .tb-dd-caret {
            font-size: 10px;
            line-height: 1;
            opacity: .72;
            transform: translateY(1px);
        }

        .tb-dd-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            z-index: 10000;
            width: 320px;
            max-width: min(92vw, 360px);
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 1);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .16);
        }

        .tb-dd-menu[hidden] {
            display: none !important;
        }

        .tb-dd-head {
            padding: 12px 14px;
            border-bottom: 1px solid var(--tb-slate-200);
            background: var(--tb-amber-50);
            color: rgba(120, 53, 15, .84);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .01em;
        }

        .tb-dd-list {
            padding: 8px;
        }

        .tb-dd-item {
            display: block;
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            color: rgba(69, 26, 3, .88);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.35;
            text-decoration: none;
            transition: background-color .16s ease, color .16s ease;
        }

        .tb-dd-item:hover,
        .tb-dd-item:focus-visible {
            outline: none;
            background: var(--tb-amber-50);
            color: var(--tb-amber-900);
        }

        .tb-lang-group {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding-left: 4px;
        }

        .tb-lang {
            min-width: 38px;
            min-height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 9px;
            border: 1px solid rgba(120, 53, 15, .18);
            border-radius: 999px;
            background: #fff;
            color: rgba(69, 26, 3, .78);
            font-size: 12px;
            font-weight: 900;
            text-decoration: none;
        }

        .tb-lang:hover,
        .tb-lang.is-active {
            background: var(--tb-amber-100);
            color: var(--tb-amber-900);
        }

        .tb-desktop-spacer {
            display: none;
            height: var(--tb-header-h);
        }

        .tb-mobile-bottombar {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9999;
            display: block;
            background: rgba(255, 255, 255, .98);
            border-top: 1px solid rgba(226, 232, 240, .96);
            padding-bottom: env(safe-area-inset-bottom);
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
        }

        .tb-mobile-inner {
            height: var(--tb-mobile-nav-h);
            display: flex;
            align-items: center;
            justify-content: space-around;
            gap: 2px;
            padding-inline: 8px;
        }

        .tb-mobile-item {
            flex: 1 1 0;
            min-width: 0;
            min-height: 56px;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 4px 2px;
            border: 0;
            border-radius: 12px;
            background: transparent;
            color: rgba(69, 26, 3, .82);
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            -webkit-tap-highlight-color: transparent;
        }

        .tb-mobile-item:active,
        .tb-mobile-item[aria-expanded="true"] {
            background: var(--tb-amber-50);
        }

        .tb-mobile-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 32px;
            border-radius: 12px;
            background: var(--tb-amber-100);
            color: var(--tb-amber-900);
            font-size: 17px;
            font-weight: 900;
            line-height: 1;
        }

        .tb-mobile-label {
            max-width: 100%;
            overflow: hidden;
            color: inherit;
            font-size: 10.5px;
            font-weight: 800;
            line-height: 1.1;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .tb-mobile-panel {
            position: fixed;
            left: 12px;
            right: 12px;
            bottom: calc(var(--tb-mobile-nav-h) + env(safe-area-inset-bottom) + 10px);
            z-index: 10000;
            max-height: min(70vh, 430px);
            overflow: auto;
            border: 1px solid rgba(226, 232, 240, .96);
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .2);
            -webkit-overflow-scrolling: touch;
        }

        .tb-mobile-panel[hidden] {
            display: none !important;
        }

        .tb-mobile-panel-head {
            position: sticky;
            top: 0;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border-bottom: 1px solid var(--tb-slate-200);
            background: var(--tb-amber-50);
            color: rgba(120, 53, 15, .86);
            font-size: 13px;
            font-weight: 900;
        }

        .tb-mobile-close {
            min-width: 34px;
            min-height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(120, 53, 15, .18);
            border-radius: 10px;
            background: #fff;
            color: var(--tb-slate-900);
            cursor: pointer;
        }

        .tb-mobile-panel-list {
            padding: 8px;
        }

        @media (min-width: 768px) {
            .tb-desktop-topbar {
                display: block;
            }

            .tb-desktop-spacer {
                display: block;
            }

            .tb-mobile-bottombar {
                display: none !important;
            }
        }

        @media (max-width: 767.98px) {
            body {
                padding-bottom: calc(var(--tb-mobile-nav-h) + env(safe-area-inset-bottom));
            }
        }

        @media (max-width: 960px) and (min-width: 768px) {
            .tb-header-wrap {
                width: min(100% - 24px, 1152px);
            }

            .tb-desktop-inner {
                gap: 12px;
            }

            .tb-brand-subtitle {
                display: none;
            }

            .tb-desktop-nav {
                gap: 4px;
                font-size: 13px;
            }

            .tb-nav-link,
            .tb-dd-btn {
                padding-inline: 8px;
            }
        }
    </style>
@endonce

<header class="tb-public-header" data-tb-header>
    {{-- DESKTOP --}}
    <div class="tb-desktop-topbar" role="banner">
        <div class="tb-header-wrap tb-desktop-inner">
            <a href="{{ $routes['home'] }}" class="tb-brand" aria-label="IA Tio Ben">
                <img
                    src="{{ asset('images/logo-amp.png') }}"
                    alt="IA Tio Ben"
                    class="tb-brand-logo"
                    width="36"
                    height="36"
                    loading="eager"
                    decoding="async"
                >
                <span class="tb-brand-copy">
                    <span class="tb-brand-title">IA Tio Ben</span>
                    <span class="tb-brand-subtitle">{{ $t['subtitle'] }}</span>
                </span>
            </a>

            <nav class="tb-desktop-nav" aria-label="Menu principal">
                <a href="{{ $routes['home'] }}" class="tb-nav-link">{{ $t['home'] }}</a>
                <a href="{{ $routes['liturgy'] }}" class="tb-nav-link">{{ $t['liturgy'] }}</a>

                <div class="tb-dd" data-tb-desktop-dd>
                    <button
                        type="button"
                        class="tb-dd-btn"
                        data-tb-desktop-btn
                        aria-haspopup="menu"
                        aria-expanded="false"
                        aria-controls="tbRosaryDesktopMenu"
                    >
                        <span>{{ $t['rosary'] }}</span>
                        <span class="tb-dd-caret" aria-hidden="true">▼</span>
                    </button>

                    <div
                        id="tbRosaryDesktopMenu"
                        class="tb-dd-menu"
                        data-tb-desktop-menu
                        role="menu"
                        hidden
                    >
                        <div class="tb-dd-head">{{ $t['rosary'] }}</div>
                        <div class="tb-dd-list">
                            @foreach($rosarySub as $item)
                                <a href="{{ $item['href'] }}" class="tb-dd-item" role="menuitem">
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <a href="{{ $routes['blog'] }}" class="tb-nav-link">{{ $t['blog'] }}</a>

                <div class="tb-lang-group" aria-label="{{ $t['lang'] }}">
                    <a href="{{ $routes['pt'] }}" class="tb-lang {{ $lang === 'pt' ? 'is-active' : '' }}" hreflang="pt-BR">PT</a>
                    <a href="{{ $routes['en'] }}" class="tb-lang {{ $lang === 'en' ? 'is-active' : '' }}" hreflang="en">EN</a>
                </div>
            </nav>
        </div>
    </div>

    <div class="tb-desktop-spacer" aria-hidden="true"></div>

    {{-- MOBILE --}}
    <nav class="tb-mobile-bottombar" aria-label="Menu principal mobile">
        <div class="tb-header-wrap tb-mobile-inner">
            <a href="{{ $routes['home'] }}" class="tb-mobile-item">
                <span class="tb-mobile-icon" aria-hidden="true">🏠</span>
                <span class="tb-mobile-label">{{ $t['home'] }}</span>
            </a>

            <a href="{{ $routes['liturgy'] }}" class="tb-mobile-item">
                <span class="tb-mobile-icon" aria-hidden="true">📖</span>
                <span class="tb-mobile-label">{{ $t['liturgy'] }}</span>
            </a>

            <a href="{{ $routes['blog'] }}" class="tb-mobile-item">
                <span class="tb-mobile-icon" aria-hidden="true">📰</span>
                <span class="tb-mobile-label">{{ $t['blog'] }}</span>
            </a>

            <button
                type="button"
                class="tb-mobile-item"
                data-tb-mobile-btn
                aria-haspopup="dialog"
                aria-expanded="false"
                aria-controls="tbRosaryMobilePanel"
                aria-label="{{ $t['open_menu'] }}"
            >
                <span class="tb-mobile-icon" aria-hidden="true">📿</span>
                <span class="tb-mobile-label">{{ $t['rosary'] }}</span>
            </button>

            <a href="{{ $lang === 'en' ? $routes['pt'] : $routes['en'] }}" class="tb-mobile-item" hreflang="{{ $lang === 'en' ? 'pt-BR' : 'en' }}">
                <span class="tb-mobile-icon" aria-hidden="true">{{ $lang === 'en' ? 'PT' : 'EN' }}</span>
                <span class="tb-mobile-label">{{ $t['lang'] }}</span>
            </a>
        </div>

        <div
            id="tbRosaryMobilePanel"
            class="tb-mobile-panel"
            data-tb-mobile-panel
            role="dialog"
            aria-modal="false"
            aria-label="{{ $t['rosary'] }}"
            hidden
        >
            <div class="tb-mobile-panel-head">
                <span>{{ $t['rosary'] }}</span>
                <button type="button" class="tb-mobile-close" data-tb-mobile-close aria-label="{{ $t['close_menu'] }}">✕</button>
            </div>

            <div class="tb-mobile-panel-list">
                @foreach($rosarySub as $item)
                    <a href="{{ $item['href'] }}" class="tb-dd-item">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </nav>
</header>

@once
    <script id="tb-public-header-js">
        (function () {
            'use strict';

            const header = document.querySelector('[data-tb-header]');
            if (!header) return;

            const desktopWrap = header.querySelector('[data-tb-desktop-dd]');
            const desktopBtn = header.querySelector('[data-tb-desktop-btn]');
            const desktopMenu = header.querySelector('[data-tb-desktop-menu]');
            let closeTimer = null;

            function openDesktopMenu() {
                if (!desktopBtn || !desktopMenu) return;
                window.clearTimeout(closeTimer);
                desktopMenu.hidden = false;
                desktopBtn.setAttribute('aria-expanded', 'true');
            }

            function closeDesktopMenu() {
                if (!desktopBtn || !desktopMenu) return;
                desktopMenu.hidden = true;
                desktopBtn.setAttribute('aria-expanded', 'false');
            }

            function scheduleDesktopClose() {
                window.clearTimeout(closeTimer);
                closeTimer = window.setTimeout(closeDesktopMenu, 120);
            }

            if (desktopWrap && desktopBtn && desktopMenu) {
                desktopBtn.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    desktopMenu.hidden ? openDesktopMenu() : closeDesktopMenu();
                });

                desktopWrap.addEventListener('mouseenter', openDesktopMenu);
                desktopWrap.addEventListener('mouseleave', scheduleDesktopClose);
                desktopMenu.addEventListener('mouseenter', openDesktopMenu);
                desktopMenu.addEventListener('mouseleave', scheduleDesktopClose);
            }

            const mobileBtn = header.querySelector('[data-tb-mobile-btn]');
            const mobilePanel = header.querySelector('[data-tb-mobile-panel]');
            const mobileClose = header.querySelector('[data-tb-mobile-close]');

            function openMobilePanel() {
                if (!mobileBtn || !mobilePanel) return;
                mobilePanel.hidden = false;
                mobileBtn.setAttribute('aria-expanded', 'true');
            }

            function closeMobilePanel() {
                if (!mobileBtn || !mobilePanel) return;
                mobilePanel.hidden = true;
                mobileBtn.setAttribute('aria-expanded', 'false');
            }

            if (mobileBtn && mobilePanel) {
                mobileBtn.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    mobilePanel.hidden ? openMobilePanel() : closeMobilePanel();
                });

                if (mobileClose) {
                    mobileClose.addEventListener('click', function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                        closeMobilePanel();
                    });
                }
            }

            document.addEventListener('click', function (event) {
                const target = event.target;

                if (desktopWrap && desktopMenu && !desktopMenu.hidden && !desktopWrap.contains(target)) {
                    closeDesktopMenu();
                }

                if (mobileBtn && mobilePanel && !mobilePanel.hidden && !mobilePanel.contains(target) && !mobileBtn.contains(target)) {
                    closeMobilePanel();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key !== 'Escape') return;
                closeDesktopMenu();
                closeMobilePanel();
            });

            window.addEventListener('resize', function () {
                closeDesktopMenu();
                closeMobilePanel();
            }, { passive: true });
        })();
    </script>
@endonce
