/* ═══════════════════════════════════════════════════════════════════
   MMB Nav — keeps the URL in sync with the active nav tab (v1.0)
   GitHub issue #4 item 1: after opening a report modal, navigating to
   another tab and refreshing, the app used to jump back to the modal
   because pill clicks never updated ?tab= and never cleaned the
   detail_* filter params the modal form had written into the URL.

   Rules
   ─ Switching sidebar pills  → ?tab=<pane>, detail_* + hash stripped
   ─ Closing #salesDetailModal→ detail_* params stripped from the URL
   ─ Loading with a #v-pills-… hash → that tab wins over ?tab=, then
     the hash is removed (no scroll jumps)
   Loaded globally via conn/connection_links.php; no-op without #sidebar.
   ═══════════════════════════════════════════════════════════════════ */
(function () {
    'use strict';

    var STALE_KEYS = ['detail_period', 'detail_value', 'detail_cashier', 'success'];

    function tabNameFromHref(href) {
        var m = /^#v-pills-([a-z0-9-]+)$/i.exec(href || '');
        return m ? m[1].toLowerCase() : null;
    }

    function cleanUrl(tab) {
        var url = new URL(window.location.href);
        var touched = false;
        STALE_KEYS.forEach(function (k) {
            if (url.searchParams.has(k)) { url.searchParams.delete(k); touched = true; }
        });
        if (url.hash) { url.hash = ''; touched = true; }
        if (tab) {
            var current = url.searchParams.get('tab');
            if (current !== tab) { url.searchParams.set('tab', tab); touched = true; }
        }
        if (touched) {
            try { history.replaceState(history.state, '', url.href); } catch (e) { /* ignore */ }
        }
    }

    /* Load-time cleanup: only the hash goes away. detail_* params are kept
       so the Sales Detail deep link (fresh load with filters) still opens
       the report modal — they are removed later by pill clicks or when
       the modal is closed. */
    function stripHashOnly() {
        if (!window.location.hash) return;
        var url = new URL(window.location.href);
        url.hash = '';
        try { history.replaceState(history.state, '', url.href); } catch (e) { /* ignore */ }
    }

    function activate(tabName) {
        var link = document.querySelector('#sidebar .nav-link[href="#v-pills-' + tabName + '"]');
        if (link && window.bootstrap && bootstrap.Tab) {
            bootstrap.Tab.getOrCreateInstance(link).show();
        } else if (link) {
            link.click();
        }
    }

    function init() {
        var sidebar = document.getElementById('sidebar');
        if (!sidebar) return; /* login, staff POS — nothing to sync */

        /* 1. Pills → URL */
        sidebar.querySelectorAll('.nav-link[data-bs-toggle="pill"]').forEach(function (link) {
            var tab = tabNameFromHref(link.getAttribute('href'));
            link.addEventListener('click', function () {
                if (tab) cleanUrl(tab);
            });
        });

        /* 2. A stale #v-pills-… hash outranks the server-rendered tab
              (covers bookmarks made before this fix). */
        var fromHash = tabNameFromHref(window.location.hash);
        var fromQuery = (new URL(window.location.href)).searchParams.get('tab');
        if (fromHash && fromHash !== fromQuery) {
            activate(fromHash);
        }
        stripHashOnly();

        /* 3. Closing the Sales Detail report clears its GET filters so a
              refresh never re-opens the modal (the deep link still works
              on a genuine fresh load with the params present). */
        var reportModal = document.getElementById('salesDetailModal');
        if (reportModal) {
            reportModal.addEventListener('hidden.bs.modal', function () { cleanUrl(null); });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
