$(function () {

    // Auto-assign DataTables Responsive priorities before init so wide
    // tables collapse into child rows on mobile instead of overflowing.
    // Hand-tuned data-priority attributes on <th> always win.
    window.mmbApplyResponsivePriorities = function (table) {
        var ths = $(table).find('thead th');
        if (!ths.length || ths.is('[data-priority]')) return;
        ths.each(function (i) {
            var txt = (this.textContent || '').trim().toLowerCase();
            if (i === 0 || /action|status|view|ops/.test(txt)) {
                this.setAttribute('data-priority', '1');
            } else {
                this.setAttribute('data-priority', String(Math.min(i + 2, 9)));
            }
        });
    };

    $('.myTable, .myTableExport').each(function () {
        mmbApplyResponsivePriorities(this);
    });

    // NORMAL TABLES (no buttons)
    $('.myTable').each(function () {
        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({
                responsive: true
            });
        }
    });

    // TABLES WITH EXPORT BUTTONS ONLY
    $('.myTableExport').each(function () {
        if (!$.fn.DataTable.isDataTable(this)) {
            initDataTable(this);
        }
    });

    // Focus search input on page load
    focusSearchInput();
    // FIX: initUserModals() was never defined — calling it threw a
    // ReferenceError on every page load and aborted the remaining handlers.
    if (typeof initUserModals === 'function') {
        initUserModals();
    }

});

function initDataTable(table) {
    return $(table).DataTable({
        responsive: true,
        dom: 'fBrtip',
        buttons: ['copy', 'excel', 'pdf', 'print']
    });
}

function focusSearchInput() {
    setTimeout(function() {
        $('input[type="search"]').first().focus();
    }, 200);
}
