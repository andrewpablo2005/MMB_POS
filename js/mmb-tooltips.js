/* ═══════════════════════════════════════════════════════════════
   MMB DRUGSTORE — GLOBAL HELP LAYER  v1.4
   Hover tooltips + visible "?" badges on every interactive control
   so staff always knows what a function does.

   How it works
   ────────────
   • No markup changes needed anywhere. Tooltips are resolved
     lazily at hover time, so elements rendered later (DataTables
     redraws, POS cart rows, modals, quick-cash buttons) are
     covered automatically.
   • v1.4 — "?" BADGES: every control the resolver can explain also
     gets a small crimson question badge pinned to its corner, so
     help is DISCOVERABLE, not accidental. Badges are injected at
     runtime (span + FA glyph only, zero text nodes — labels and
     dictionary lookups are untouched) and re-attached automatically
     for DOM that appears later (MutationObserver + Bootstrap
     shown events).
   • Resolution order:
       1. data-bs-title attribute (explicit override)
       2. existing native title="" attribute (upgraded to styled)
       3. element rules (KPI tiles, pay button, qty controls,
          category pills, DataTables paging, password eye, etc.)
       4. LABEL_TIPS dictionary keyed by the visible label text
       5. prefix rules ("Pay Now ₱…", quick-cash "₱…")
       6. ICON_TIPS map for icon-only buttons
       7. fallback: aria-label or short visible text
   • Powered by Bootstrap 5 Tooltip (already in the bundle) —
     zero new dependencies. Silent no-op if Bootstrap is missing.
   Loaded once per page from conn/connection_links.php.
   ═══════════════════════════════════════════════════════════════ */

(function () {
    'use strict';
    if (window.mmbTooltips) return;
    if (!window.bootstrap || !bootstrap.Tooltip) return; // CDN failed → no-op

    /* ── Staff-facing explanations, keyed by normalized visible text ──
       Keep sidebar entries short (≤ ~40 chars) so bubbles stay compact —
       two lines at most — instead of wide bars. */
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

        /* Common buttons */
        'add item':           'Register a new product in the catalog',
        'add user':           'Create a new staff account',
        'view':               'Open the full details for this record',
        'edit':               'Change the details of this record',
        'delete':             'Remove this record permanently',
        'cancel':             'Discard changes and close',
        'close':              'Close this window',
        'done':               'Finish and close this window',
        'save user':          'Create the account with the details entered',
        'save product':       'Save the new product to the catalog',
        'save batch':         'Add this stock batch to inventory',
        'save changes':       'Save your changes',
        'add supplier':       'Save this supplier',
        'dispose':            'Confirm writing off this batch',
        'approve':            'Accept this request',
        'reject':             'Decline this request',
        'enable':             'Re-enable this account',
        'disable':            'Temporarily disable this account',
        'activate':           'Activate this account',
        'lookup':             'Find this transaction by ID',
        'confirm refund':     'Process the refund and restock the item',
        'print voucher':      'Print the refund voucher',
        'print receipt':      'Print a copy of the receipt',
        'verify':             'Check the ID and apply the discount',
        'decline':            'Reject the discount request',
        'confirm id verified':'Confirm the ID was checked — unlocks the discount',
        'verify senior id':   'Open Senior Citizen ID verification',
        'verify pwd id':      'Open PWD ID verification',
        'override':           'Request a manager-approved price change',
        'void':               'Cancel this transaction (requires PIN)',
        'refresh':            'Reload product and price data',
        'auto':               'Generate a barcode for this product',
        'yes':                'Prescription is required to sell this item',
        'no':                 'No prescription needed for this item',
        'go back':            'Return without saving',
        'logout':             'Sign out of your account',

        /* POS header actions */
        'process return  f9': 'Open the returns window (keyboard shortcut: F9)',
        'close register':     'End your shift — reconcile the cash drawer',
        'confirm closing':    'Submit the counted cash and close the register',
        'confirm payment':    'Complete the sale and print the receipt',
        'authorize override': 'Approve the price change with your PIN',
        'confirm remove':     'Remove this item from the sale',
        'close cashier register': 'End your shift — reconcile the cash drawer',

        /* Inventory tabs */
        'current inventory':  'Stock currently on hand',
        'disposed / expired': 'Batches that were written off',
        'returned products':  'Stock returned by customers',

        /* Export toolbar */
        'copy':               'Copy this table to the clipboard',
        'excel':              'Download this table as an Excel file',
        'pdf':                'Download this table as a PDF file',
        'print':              'Print this table',
        'view report':        'Generate the report for the selected date range',

        /* Topbar */
        'toggle navigation':  'Show or hide the menu',
        'open notifications': 'Notifications and low-stock alerts',
        'close notifications':'Dismiss the notifications bar'
    };

    /* ── Icon-only buttons (matched by the Font Awesome icon inside) ── */
    var ICON_TIPS = {
        'fa-eye':            'View details',
        'fa-eye-slash':      'Hide password',
        'fa-pen':            'Edit',
        'fa-pen-to-square':  'Edit',
        'fa-edit':           'Edit',
        'fa-trash':          'Delete',
        'fa-trash-can':      'Remove',
        'fa-times':          'Remove',
        'fa-xmark':          'Close',
        'fa-copy':           'Copy',
        'fa-file-excel':     'Export to Excel',
        'fa-file-pdf':       'Export to PDF',
        'fa-print':          'Print',
        'fa-magnifying-glass':'Search',
        'fa-user-plus':      'Add a new user',
        'fa-rotate-left':    'Process a return',
        'fa-cash-register':  'Close the register',
        'fa-bell':           'Notifications',
        'fa-bars':           'Open the menu',
        'fa-plus':           'Add',
        'fa-minus':          'Decrease quantity',
        'fa-arrow-up':       'Increase quantity',
        'fa-check':          'Confirm',
        'fa-ban':            'Block'
    };

    /* ── Interactive elements this layer covers ── */
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

    /* Note: the sidebar hamburger (data-bs-toggle="offcanvas") is NOT
       skipped — the Offcanvas instance lives on the #sidebar target,
       not the button, so a styled tooltip is safe there.
       Fields (input/select/textarea) ARE covered now — they get a
       hover-only tooltip (never on focus, which would pop while the
       cashier types). */
    var SKIP = '.page-link';
    var FIELDS = 'input, select, textarea';
    var initialized = new WeakSet();

    /* Elements already hosting another Bootstrap component (nav pills,
       tab buttons, dropdown toggles) CANNOT register a Tooltip in
       Bootstrap 5.3 — Data.set refuses a second component per element
       ("Bootstrap doesn't allow more than one instance per element")
       which produces zombie instances, duplicate tip elements and
       console errors. Detect them up front and fall back to a native
       title tooltip so staff hints still cover everything. */
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

    /* Visible label wired to a field (closest <label> or label[for]) */
    function labelOf(el) {
        var lab = el.closest('label');
        if (lab) return norm(lab.textContent);
        if (el.id) {
            var wired = document.querySelector('label[for="' + el.id + '"]');
            if (wired) return norm(wired.textContent);
        }
        return '';
    }

    /* ── Smart placement: keep bubbles inside the viewport ── */
    function placementOf(el) {
        /* Fields: bubble above — never sideways into neighbouring inputs */
        if (el.matches(FIELDS)) return 'top';

        /* Vertical navs (the app sidebar): bubble floats to the RIGHT of
           the menu. Without this the pill falls through to the .d-flex
           heuristic below (the whole layout wrapper is .d-flex) and the
           bubble renders BELOW the pill — a wide dark bar laid across
           the entire menu column. */
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
        /* Close (×) buttons — pure glyph, no label anywhere */
        if (el.classList.contains('btn-close')) return 'Close this window';

        /* Fields: search boxes, selects, checkboxes, text inputs */
        if (el.matches(FIELDS)) {
            if (el.closest('.dt-length')) return 'Number of rows to show per page';
            if (el.tagName === 'SELECT') {
                var sLabel = labelOf(el);
                return sLabel ? 'Open this list and pick \u2014 ' + sLabel
                              : 'Open this list and pick an option';
            }
            if (el.type === 'checkbox' || el.type === 'radio') {
                return labelOf(el) || el.getAttribute('aria-label') || 'Toggle this option';
            }
            var ph = (el.getAttribute('placeholder') || '').trim();
            if (/^search/i.test(ph)) return 'Type to filter this table';
            if (ph) return ph;
            var al = (el.getAttribute('aria-label') || '').trim();
            if (al) return al;
            var fLabel = labelOf(el);
            if (fLabel) return 'Enter the ' + fLabel.toLowerCase();
            return 'Type in this field';
        }

        /* KPI tiles — keyed by their stat label */
        if (el.classList.contains('stat-card')) {
            var label = norm(el.querySelector('.stat-label') ? el.querySelector('.stat-label').textContent : '');
            var kpi = {
                "today's sales":       'Gross sales recorded for today',
                'monthly sales':       'Total sales for the current month',
                'yearly sales':        'Total sales for the current year',
                'real revenue today':  'Net revenue today — refunds already deducted',
                'transactions':        'Number of sales transactions today',
                'discounts':           'Total discounts given today'
            };
            return kpi[label] || null;
        }

        /* POS pay button — state-aware */
        if (el.id === 'weposPayBtn' || el.classList.contains('wepos-pay-btn')) {
            return el.disabled
                ? 'Cart is empty — add products to enable payment'
                : 'Complete the sale and accept the customer\u2019s payment';
        }

        /* Cart quantity controls */
        if (el.closest('.wepos-qty-ctrl')) {
            return el.querySelector('.fa-minus') || textOf(el).indexOf('-') === 0
                ? 'Decrease the quantity by one'
                : 'Increase the quantity by one';
        }

        /* Category pills */
        if (el.classList.contains('wepos-cat-btn')) {
            return textOf(el) === 'all'
                ? 'Show all products'
                : 'Show only products in this category';
        }

        /* Discount selector */
        if (el.classList.contains('wepos-select')) {
            return 'Choose a discount — Senior and PWD discounts require ID verification';
        }

        /* Account chip in the topbar */
        if (el.classList.contains('user-chip')) {
            return 'Your account — profile and sign out';
        }

        /* Password show/hide toggle (login and settings) */
        if (el.id === 'togglePassword') {
            return 'Show or hide the password';
        }

        /* DataTables pagination */
        if (el.classList.contains('dt-paging-button')) {
            var t = textOf(el);
            if (t === 'previous' || t === '\u2039' || t === '\u00ab') return 'Previous page';
            if (t === 'next' || t === '\u203a' || t === '\u00bb')     return 'Next page';
            return 'Go to page ' + t;
        }

        /* Product card hover overlay */
        if (el.classList.contains('wepos-add-btn-fake')) {
            return 'Add this product to the cart';
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

        /* 4. prefix rules (labels with live amounts) */
        if (t.indexOf('pay now') === 0) {
            return el.disabled
                ? 'Cart is empty — add products to enable payment'
                : 'Complete the sale and accept the customer\u2019s payment';
        }
        if (t.indexOf('\u20b1') === 0) {
            return 'Insert ' + t + ' as the cash payment';
        }

        /* 5. dictionary by visible label */
        if (LABEL_TIPS[t]) return LABEL_TIPS[t];

        /* 6. icon-only buttons (never the "?" badge's own glyph) */
        var icon = null, glyphs = el.querySelectorAll('i[class*="fa-"]');
        for (var g = 0; g < glyphs.length; g++) {
            if (!glyphs[g].closest('.mmb-q')) { icon = glyphs[g]; break; }
        }
        if (icon && t === '') {
            var classes = icon.className || '';
            for (var key in ICON_TIPS) {
                if (classes.indexOf(key) !== -1) return ICON_TIPS[key];
            }
            return null;
        }

        /* 7. last resort: accessible name (icon buttons with aria-label) */
        var aria = el.getAttribute('aria-label');
        if (aria && aria.trim()) return norm(aria) in LABEL_TIPS ? LABEL_TIPS[norm(aria)] : aria.trim();

        /* Short labels not in the dictionary: echo the label so the "?"
           badge never promises a hint it cannot show. */
        if (t && t.length <= 28) {
            return t.charAt(0).toUpperCase() + t.slice(1);
        }

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
        if (!tip) return; // nothing meaningful to say — stay quiet

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
       A small crimson question badge pinned to the corner of every
       control the resolver can explain, so staff can SEE where hover
       help exists — not only discover it by accident. Injected at
       runtime (no markup edits) and re-attached automatically for DOM
       that appears later (DataTables paging, POS cart re-renders,
       modals, dropdowns).

       The badge holds ONLY a Font Awesome <i> glyph — no text node —
       so visible labels and every dictionary lookup above are
       completely untouched. ".mmb-q" is aria-hidden and
       pointer-events:none: hovering it hovers the control itself.
       ═══════════════════════════════════════════════════════════════ */
    var BADGE_SKIP = '.mmb-q, .tooltip, .popover, .modal-backdrop, noscript';

    /* Fields cannot hold children — their badge is appended to a wrapper
       but PINNED TO THE FIELD ITSELF (see positionFieldBadge), so every
       field keeps its own personal "?" even when siblings share the
       same wrapper div. */
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

        /* no explanation → no badge: the question mark must never lie */
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
            /* compact badge on compact controls (qty ±, pager, row actions) */
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
        /* Bootstrap surfaces that reveal pre-rendered controls */
        ['shown.bs.modal', 'shown.bs.offcanvas', 'shown.bs.dropdown']
            .forEach(function (ev) { document.addEventListener(ev, scheduleScan, true); });
        /* responsive reflow: re-pin field badges to their fields */
        window.addEventListener('resize', scheduleScan);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootBadges);
    } else {
        bootBadges();
    }

    window.mmbTooltips = { version: '1.4', badges: true };
})();
