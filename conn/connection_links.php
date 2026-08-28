<?php // Shared CDN + theme links. Guarantees mmbpos_base_path() exists
   // even when this partial is included before loginfunction.php (e.g. login.php).
   if (!function_exists('mmbpos_base_path')) {
       require_once __DIR__ . '/basepath.php';
   }
?>
<?php //Bootstrap Links for CSS and JS ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- MMB Brand Theme (design tokens + Bootstrap primary overrides) — keep AFTER bootstrap -->
<link rel="stylesheet" href="<?= mmbpos_base_path() ?>/css/mmb-theme.css?v=1.0">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<?php //Datatables Links for CSS and JS ?>
<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>

<!-- DataTables Core -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.css">
<script src="https://cdn.datatables.net/2.3.7/js/dataTables.js"></script>

<!-- DataTables Extensions (Responsive & Buttons) -->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.6/css/buttons.dataTables.css">
<script src="https://cdn.datatables.net/buttons/3.2.6/js/dataTables.buttons.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.6/js/buttons.print.min.js"></script>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom JS -->
<script src="../js/usersmanagement.js"></script>

<!-- JS PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>