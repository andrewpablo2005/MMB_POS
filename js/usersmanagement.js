$(function () {

    // DataTables must never block the POS UI with a native alert() dialog
    // (GitHub issue #5 item 1: a "DataTables warning: Requested unknown
    // parameter" alert froze the whole page on load). errMode 'none' is the
    // hard guarantee; prepareDataTableEmptyState() below (upstream fix by
    // andrewpablo2005) removes the colspan placeholder rows that trigger
    // the error and keeps their text as the emptyTable message.
    if ($.fn.dataTable) {
        $.fn.dataTable.ext.errMode = 'none';
        $.fn.dataTable.defaults.language = $.fn.dataTable.defaults.language || {};
        $.fn.dataTable.defaults.language.emptyTable = 'No records found.';
        $.fn.dataTable.defaults.language.zeroRecords = 'No matching records found.';
    }

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

    $('.myTable, .myTableExport').each(function () {
        prepareDataTableEmptyState(this);
    });

    // NORMAL TABLES (no buttons)
    $('.myTable').each(function () {
        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({
                responsive: true,
                language: { emptyTable: $(this).data('empty-message') || 'No records found.' }
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
        buttons: ['copy', 'excel', 'pdf', 'print'],
        language: { emptyTable: $(table).data('empty-message') || 'No records found.' }
    });
}

function prepareDataTableEmptyState(table) {
    var emptyRow = $(table).find('tbody tr').filter(function () {
        return $(this).children('td').length === 1 && $(this).children('td').is('[colspan]');
    }).first();

    if (!emptyRow.length) return;

    $(table).attr('data-empty-message', emptyRow.text().trim() || 'No records found.');
    emptyRow.remove();
}

function focusSearchInput() {
    setTimeout(function() {
        $('input[type="search"]').first().focus();
    }, 200);
}
