$(function () {

    // 🔹 NORMAL TABLES (no buttons)
    $('.myTable').each(function () {
        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({
                responsive: true,
                autoWidth: false
            });
        }
    });

    // 🔥 TABLES WITH EXPORT BUTTONS ONLY
    $('.myTableExport').each(function () {
        if (!$.fn.DataTable.isDataTable(this)) {
            initDataTable(this);
        }
    });

    // Focus search input on page load
    focusSearchInput();
    initUserModals();

});

function initDataTable(table) {
    return $(table).DataTable({
        responsive: true,
        autoWidth: false,
        dom: 'fBrtip',
        buttons: ['copy', 'excel', 'pdf', 'print']
    });
}

function focusSearchInput() {
    setTimeout(function() {
        $('input[type="search"]').first().focus();
    }, 200);
}
