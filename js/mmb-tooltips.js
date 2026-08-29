/* ═══════════════════════════════════════════════════════════════
   MMB DRUGSTORE — CURATED HELP LAYER  v2.0
   Hover tooltips + "?" badges ONLY on controls a new staff member
   could genuinely find confusing. Everything self-explanatory
   (View / Edit / Delete, Save, Cancel, Close, login fields,
   pagination, exports, category pills) stays clean and silent.

   Curated coverage
   ────────────────
   • Sidebar menu — what each section is for
   • POS jargon — Close Register, Process Return (F9), Void,
     Override, Senior/PWD ID verification, Dispose, barcode Auto,
     transaction Lookup, Confirm Refund
   • Discount selector — Senior/PWD discounts require ID check
   • Pay button — state-aware (explains why it is disabled)
   • "Real Revenue Today" KPI — net after refunds
   • Anything a developer explicitly hints with title="" or
     data-bs-title="" (upgraded to the styled bubble)

   Resolution order:
     1. data-bs-title attribute (explicit override)
     2. native title="" attribute (upgraded to styled)
     3. element rules (pay button, discount select, KPI tile)
     4. prefix rule — "Pay Now ₱…" (state-aware)
     5. LABEL_TIPS dictionary keyed by visible label text
     6. ICON_TIPS map (jargon icon-only buttons)
     7. null → no tooltip, no badge. Silence is the default.

   Mechanics: tooltips resolve lazily at hover time, so elements
   rendered later (DataTables redraws, POS cart rows, modals) are
   covered automatically; "?" badges are injected at runtime and
   re-attached by MutationObserver + Bootstrap shown events.
   Powered by Bootstrap 5 Tooltip (already in the bundle) — silent
   no-op if Bootstrap is missing.
   Loaded once per page from conn/connection_links.php.
   ═══════════════════════════════════════════════════════════════ */

(function () {
    'use strict';
    if (window.mmbTooltips) return;
    if (!window.bootstrap || !bootstrap.Tooltip) return; // CDN failed → no-op

    /* ── Curated explanations, keyed by normalized visible text.
       Every entry must EARN its place: if the label already says
       what the control does, it does not belong here. */
    var LABEL_TIPS = {
        /* Sidebar navigation */
        'dashboard':          'Today\u2019s sales, chart and recent activity',
        'product management': 'Add, edit and remove products',
        'inventory':          'Manage stock batches and disposal',
        'sales (pos)':        'Ring up items and accept payment',
        'reports':            'Sales, inventory and product reports',
        'security':           'Passwords, PINs and account security',
        'user management':    'Create staff accounts and set roles',
        'system settings':    'Your profile and login credentials',
        'pending accounts':   'Approve new staff account requests',

        /* POS / workflow jargon — labels that do not explain themselves */
        'close register':     'End your shift — reconcile the cash drawer',
        'close cashier register': 'End your shift — reconcile the cash drawer',
        'confirm closing':    'Submit the counted cash and close the register',
        'process return f9':  'Open the returns window (keyboard shortcut: F9)',
        'void':               'Cancel this transaction (requires PIN)',
        'override':           'Request a manager-approved price change',
        'authorize override': 'Approve the price change with your PIN',
        'lookup':             'Find this transaction by ID',
        'confirm refund':     'Process the refund and restock the item',
        'dispose':            'Write this batch off — removes it from sellable stock',
        'auto':               'Generate a barcode for this product',
        'verify':             'Check the ID and apply the discount',
        'decline':            'Reject the discount request',
        'confirm id verified':'Confirm the ID was checked — unlocks the discount',
        'verify senior id':   'Open Senior Citizen ID verification',
        'verify pwd id':      'Open PWD ID verification'
    };

    /* ── Icon-only buttons worth explaining (matched by FA glyph).
       Obvious glyphs (eye, pen, trash, print…) are deliberately
       absent — they explain themselves. */
    var ICON_TIPS = {
        'fa-rotate-left':    'Process a return',
        'fa-cash-register':  'Close the register'
    };

    /* ── Interactive elements this layer scans ── */
    var SEL = [
        'button',
        '.btn',
        'a[href]',
        '[role="button"]',
        'a[data-bs-toggle="pill"]',
        'a[data-bs-toggle="tab"]',
        '.dropdown-item',
        '.wepos-cat-btn',
        '.wepos-btn-icon',
        '.wepos-select',
        '.dt-button',
        '.dt-paging-button',
        '.stat-card',
        '.wepos-add-btn-fake',
        'select',
        'textarea',
        'input:not([type="hidden"])',
        '#togglePassword'
    ].join(',');

    /* Most scanned elements resolve to null and are left alone —
       the curated lists above decide what actually speaks. */
    var SKIP = '.page-link';
    var FIELDS = 'input, select, textarea';
    var initialized = new WeakSet();

    /* Elements already hosting another Bootstrap component (nav pills,
       tab buttons, dropdown toggles) CANNOT register a Tooltip in
       Bootstrap 5.3 — Data.set refuses a second component per element
       ("Bootstrap doesn't allow more than one instance per element")
       which produces zombie instances, duplicate tip elements and
       console errors. Detect them up front and fall back to a native
       title tooltip so sidebar hints still work. */
    var COMPONENTS = ['Tab', 'Dropdown', 'Collapse', 'Modal', 'Offcanvas',
                      'Popover', 'ScrollSpy', 'Toast', 'Button', 'Carousel'];

    function hostsBootstrapComponent(el) {
        for (var i = 0; i < COMPONENTS.length; i++) {
            var C = bootstrap[COMPONENTS[i]];
            if (C && typeof C.getInstance === 'function' && C.getInstance(el)) {
                return true;
            }
        }
        return false;
    }

    function norm(s) {
        return (s || '').replace(/\s+/g, ' ').trim().toLowerCase();
    }
    function textOf(el) {
        return norm(el.textContent);
    }

    /* ── Smart placement: keep bubbles inside the viewport ── */
    function placementOf(el) {
        /* Fields: bubble above — never sideways into neighbouring inputs */
        if (el.matches(FIELDS)) return 'top';

        /* Vertical navs (the app sidebar): bubble floats to the RIGHT of
           the menu instead of rendering below as a bar across the column. */
        if (el.closest('.nav.flex-column')) return 'right';
        if (el.matches('a[data-bs-toggle="pill"], a[data-bs-toggle="tab"]')) {
            return el.closest('.d-flex, .card, .dash-card') ? 'bottom' : 'right';
        }
        if (el.closest('.wepos-right')) return 'left';
        var row = el.closest('tr');
        var cell = el.closest('td, th');
        if (row && cell && cell === row.lastElementChild) return 'left';
        if (el.closest('.dt-buttons, .inventory-report-toolbar, .report-toolbar, .pagehead-actions')) {
            return 'bottom';
        }
        return 'top';
    }

    /* ── Element-specific rules (checked before the dictionary) ── */
    function ruleFor(el) {
        /* POS pay button — state-aware */
        if (el.id === 'weposPayBtn' || el.classList.contains('wepos-pay-btn')) {
            return el.disabled
                ? 'Cart is empty — add products to enable payment'
                : 'Complete the sale and accept the customer\u2019s payment';
        }

        /* Discount selector — the one field whose use isn't obvious */
        if (el.classList.contains('wepos-select')) {
            return 'Choose a discount — Senior and PWD discounts require ID verification';
        }

        /* KPI tiles — only the one whose name is jargon */
        if (el.classList.contains('stat-card')) {
            var label = norm(el.querySelector('.stat-label') ? el.querySelector('.stat-label').textContent : '');
            if (label === 'real revenue today') {
                return 'Net revenue today — refunds already deducted';
            }
            return null;
        }

        return null;
    }

    /* ── Main resolver (re-evaluated on every show — state-aware) ── */
    function resolveTip(el) {
        /* 1. explicit override */
        var explicit = el.getAttribute('data-bs-title');
        if (explicit) return explicit.trim();

        /* 2. native title attribute → upgrade to styled tooltip
              (kept in data-native-title after first init) */
        var nativeTitle = el.getAttribute('title') || el.getAttribute('data-native-title');
        if (nativeTitle && nativeTitle.trim()) return nativeTitle.trim();

        /* 3. element rules */
        var rule = ruleFor(el);
        if (rule) return rule;

        var t = textOf(el);

        /* 4. live-label prefix — "Pay Now ₱…" (state-aware) */
        if (t.indexOf('pay now') === 0) {
            return el.disabled
                ? 'Cart is empty — add products to enable payment'
                : 'Complete the sale and accept the customer\u2019s payment';
        }

        /* 5. dictionary by visible label (curated) */
        if (LABEL_TIPS[t]) return LABEL_TIPS[t];

        /* 6. icon-only jargon buttons (never the "?" badge's own glyph) */
        var icon = null, glyphs = el.querySelectorAll('i[class*="fa-"]');
        for (var g = 0; g < glyphs.length; g++) {
            if (!glyphs[g].closest('.mmb-q')) { icon = glyphs[g]; break; }
        }
        if (icon && t === '') {
            var classes = icon.className || '';
            for (var key in ICON_TIPS) {
                if (classes.indexOf(key) !== -1) return ICON_TIPS[key];
            }
        }

        /* 7. Everything else is self-explanatory — stay quiet.
           No tooltip, no "?" badge. */
        return null;
    }

    /* ── Lazy initialization on hover / keyboard focus ── */
    function maybeInit(target, fromFocus) {
        var el = target.closest ? target.closest(SEL) : null;
        if (!el || initialized.has(el)) return;
        if (el.matches(SKIP)) return;

        var isField = el.matches(FIELDS);
        var tip = resolveTip(el);
        initialized.add(el);
        if (!tip) return; // self-explanatory — stay silent

        /* Elements with another Bootstrap component on them: styled
           tooltip is impossible — deliver the hint as a native title.
           This includes components that only materialize on FIRST
           CLICK (pill/tab/dropdown/collapse triggers): hovering one
           before clicking would bind a Tooltip to the element and
           Bootstrap's Data store would then refuse the real component
           ("doesn't allow more than one instance per element") — so
           those triggers are detected by attribute, not just by
           getInstance(). Modal/offcanvas triggers are safe: their
           instance lives on the TARGET element, not the trigger. */
        var willHostComponent = el.matches(
            '[data-bs-toggle="pill"], [data-bs-toggle="tab"], ' +
            '[data-bs-toggle="dropdown"], [data-bs-toggle="collapse"]'
        );
        if (willHostComponent || hostsBootstrapComponent(el)) {
            el.setAttribute('data-native-title', tip);
            el.setAttribute('title', tip);
            return;
        }

        /* remove native tooltip to avoid double bubbles */
        if (el.getAttribute('title')) el.setAttribute('data-native-title', el.getAttribute('title'));
        el.removeAttribute('title');

        var tt = new bootstrap.Tooltip(el, {
            /* function title → re-resolved on every show, so state-aware
               hints (e.g. Pay Now: empty cart vs active) never go stale */
            title: function () { return resolveTip(el); },
            /* fields: hover-only — a focus tooltip would pop up while the
               cashier is typing in the box */
            trigger: isField ? 'hover' : 'hover focus',
            delay: { show: 250, hide: 100 },
            placement: placementOf(el),
            fallbackPlacements: ['top', 'bottom', 'left', 'right'],
            customClass: 'mmb-tip'
        });
        /* pointer is already over the element — reveal immediately.
           (Keyboard focus on a field is the one exception: no popup
           mid-typing.) */
        if (!(isField && fromFocus)) tt.show();
    }

    document.addEventListener('mouseover', function (e) {
        maybeInit(e.target);
    }, true);

    document.addEventListener('focusin', function (e) {
        var el = e.target.closest ? e.target.closest(SEL) : null;
        if (el && !initialized.has(el)) maybeInit(e.target, true);
    }, true);

    /* ═══════════════════════════════════════════════════════════════
       "?" AFFORDANCE BADGES
       A small translucent question chip pinned to the corner of the
       FEW controls that carry a curated explanation (sidebar menu,
       POS jargon buttons) — so staff can SEE where hover help
       exists. Everything obvious stays badge-free by design:
       addQBadge only fires when resolveTip() returns text, and the
       resolver is silent for self-explanatory controls.

       The badge holds ONLY a Font Awesome <i> glyph — no text node —
       so visible labels and dictionary lookups are untouched.
       ".mmb-q" is aria-hidden and pointer-events:none: hovering it
       hovers the control itself.
       ═══════════════════════════════════════════════════════════════ */
    var BADGE_SKIP = '.mmb-q, .tooltip, .popover, .modal-backdrop, noscript';

    /* Fields cannot hold children — their badge is appended to a wrapper
       but PINNED TO THE FIELD ITSELF (see positionFieldBadge), so a
       field with an explicit hint keeps its "?" on its own corner. */
    function qHostFor(el) {
        if (!el.matches(FIELDS)) return el;
        var host = el.parentElement;
        if (host && host.classList.contains('input-group')) host = host.parentElement;
        return host;
    }

    /* Pin a field badge to the field's own top-right corner (wrapper-
       relative). Inline styles are recomputed on every scan + resize,
       so responsive reflows and newly opened modals stay aligned. */
    function positionFieldBadge(host, badge, field) {
        var fr = field.getBoundingClientRect(), hr = host.getBoundingClientRect();
        if (!fr.width && !fr.height) return;   // hidden: CSS corner fallback
        badge.style.top = Math.round(fr.top - hr.top - 7) + 'px';
        badge.style.left = Math.round(fr.right - hr.left - 8) + 'px';
        badge.style.right = 'auto';
    }

    var fieldBadges = [];   // {badge, field, host} — for repositioning
    function repositionFieldBadges() {
        for (var i = fieldBadges.length - 1; i >= 0; i--) {
            var fb = fieldBadges[i];
            if (!fb.badge.isConnected || !fb.field.isConnected) {
                fieldBadges.splice(i, 1);
                continue;
            }
            positionFieldBadge(fb.host, fb.badge, fb.field);
        }
    }

    function addQBadge(el) {
        if (el.closest(BADGE_SKIP)) return;
        if (el.hasAttribute('data-mmb-q')) return;   // already processed

        /* no curated explanation → no badge: the question mark must
           never appear on something self-explanatory */
        if (!resolveTip(el)) { el.setAttribute('data-mmb-q', '0'); return; }

        var host = qHostFor(el);
        if (!host) return;

        el.setAttribute('data-mmb-q', '1');

        var badge = document.createElement('span');
        badge.className = 'mmb-q';
        badge.setAttribute('aria-hidden', 'true');
        var glyph = document.createElement('i');
        glyph.className = 'fa-solid fa-question';
        badge.appendChild(glyph);

        var isField = el.matches(FIELDS);
        host.classList.add('mmb-q-host');   // position:relative anchor
        if (isField) {
            /* fields: compact badge pinned to the field itself */
            badge.classList.add('mmb-q--sm', 'mmb-q--field');
        } else {
            /* compact badge on compact controls (pager, row actions) */
            if (el.offsetHeight && el.offsetHeight < 34) badge.classList.add('mmb-q--sm');
            /* bare inline text links: badge floats at the end of the text,
               not pinned to a padded button corner */
            if (el.tagName === 'A' && !el.classList.contains('btn') &&
                !el.classList.contains('nav-link') && !el.classList.contains('dropdown-item')) {
                badge.classList.add('mmb-q--link');
            }
        }

        host.appendChild(badge);
        if (isField) {
            fieldBadges.push({ badge: badge, field: el, host: host });
            positionFieldBadge(host, badge, el);
        }
    }

    function scanBadges(scope) {
        var list;
        try { list = (scope || document).querySelectorAll(SEL); } catch (err) { return; }
        for (var i = 0; i < list.length; i++) addQBadge(list[i]);
        repositionFieldBadges();
    }

    /* coalesce mutation bursts (POS re-renders, DT paging) into one scan */
    var scanQueued = false;
    function scheduleScan() {
        if (scanQueued) return;
        scanQueued = true;
        (window.requestAnimationFrame || window.setTimeout)(function () {
            scanQueued = false;
            scanBadges(document);
        }, 0);
    }

    function bootBadges() {
        scanBadges(document);
        if (window.MutationObserver && document.body) {
            new MutationObserver(scheduleScan)
                .observe(document.body, { childList: true, subtree: true });
        }
        /* Bootstrap surfaces that reveal pre-rendered controls. Tab panes
           matter especially: switching tabs only toggles classes (no DOM
           mutation), so field badges pinned while the pane was hidden
           must be re-positioned once it becomes visible. */
        ['shown.bs.modal', 'shown.bs.offcanvas', 'shown.bs.dropdown', 'shown.bs.tab']
            .forEach(function (ev) { document.addEventListener(ev, scheduleScan, true); });
        /* responsive reflow: re-pin field badges to their fields */
        window.addEventListener('resize', scheduleScan);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootBadges);
    } else {
        bootBadges();
    }

    window.mmbTooltips = { version: '2.0', badges: true };
})();
