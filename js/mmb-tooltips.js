/* ═══════════════════════════════════════════════════════════════
   MMB DRUGSTORE — GLOBAL TOOLTIP LAYER  v1.0
   Hover tooltips on every interactive control so staff always
   knows what a function does.

   How it works
   ────────────
   • No markup changes needed anywhere. Tooltips are resolved
     lazily at hover time, so elements rendered later (DataTables
     redraws, POS cart rows, modals, quick-cash buttons) are
     covered automatically.
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

    /* ── Staff-facing explanations, keyed by normalized visible text ── */
    var LABEL_TIPS = {
        /* Sidebar navigation */
        'dashboard':          'Sales overview — today\u2019s numbers, monthly chart and recent activity',
        'product management': 'Add, view, edit and remove products in the catalog',
        'inventory':          'Manage stock batches — receive, dispose and track returns',
        'sales (pos)':        'Point of sale — ring up items, apply discounts and accept payment',
        'reports':            'Sales, inventory and product performance reports',
        'security':           'Passwords, verification PINs and account security',
        'user management':    'Create staff accounts, set roles and manage access',
        'system settings':    'Update your profile, contact details and login credentials',
        'pending accounts':   'Review and approve new staff account requests',

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
        '#togglePassword'
    ].join(',');

    var SKIP = 'input, textarea, .page-link, [data-bs-toggle="offcanvas"]';
    var initialized = new WeakSet();

    function norm(s) {
        return (s || '').replace(/\s+/g, ' ').trim().toLowerCase();
    }
    function textOf(el) {
        return norm(el.textContent);
    }

    /* ── Smart placement: keep bubbles inside the viewport ── */
    function placementOf(el) {
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

        /* 6. icon-only buttons */
        var icon = el.querySelector('i[class*="fa-"]');
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

        /* Short text buttons not in the dictionary: confirm hover target only */
        if (t && t.length <= 4 && el.querySelector('i[class*="fa-"]')) {
            return t.charAt(0).toUpperCase() + t.slice(1);
        }

        return null;
    }

    /* ── Lazy initialization on hover / keyboard focus ── */
    function maybeInit(target) {
        var el = target.closest ? target.closest(SEL) : null;
        if (!el || initialized.has(el)) return;
        if (el.matches(SKIP)) return;

        var tip = resolveTip(el);
        initialized.add(el);
        if (!tip) return; // nothing meaningful to say — stay quiet

        /* remove native tooltip to avoid double bubbles */
        if (el.getAttribute('title')) el.setAttribute('data-native-title', el.getAttribute('title'));
        el.removeAttribute('title');

        var tt = new bootstrap.Tooltip(el, {
            /* function title → re-resolved on every show, so state-aware
               hints (e.g. Pay Now: empty cart vs active) never go stale */
            title: function () { return resolveTip(el); },
            trigger: 'hover focus',
            delay: { show: 250, hide: 100 },
            placement: placementOf(el),
            fallbackPlacements: ['top', 'bottom', 'left', 'right'],
            customClass: 'mmb-tip'
        });
        /* pointer is already over the element — reveal immediately */
        tt.show();
    }

    document.addEventListener('mouseover', function (e) {
        maybeInit(e.target);
    }, true);

    document.addEventListener('focusin', function (e) {
        var el = e.target.closest ? e.target.closest(SEL) : null;
        if (el && !initialized.has(el)) maybeInit(e.target);
    }, true);

    window.mmbTooltips = { version: '1.1' };
})();
