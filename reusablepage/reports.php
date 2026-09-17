<?php
require_once __DIR__ . '/guard.php'; guard_require_roles(['owner','admin']);
require_once __DIR__ . "/../conn/database.php";
require_once __DIR__ . "/../conn/connection_links.php";
require_once __DIR__ . "/../function/Reports.php";

use Classes\Reports;

// FIX: month names for the Monthly Sales Trend modal (previously undefined)
$monthNames = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June',
               7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];

$report = new Reports($db);

// GET ALL DATA
$data = $report->getAllReports();

// ASSIGN VARIABLES
$sales = $data['sales'];
$topProducts = $data['topProducts'];
$inventory = $data['inventory'];
$discounts = $data['discounts'];
$cashiers = $data['cashiers'];
$dailySales = $data['dailySales'];
$yearlySales = $data['yearlySales'];
$discountBreakdown = $data['discountBreakdown'];
$allTransactions = $data['transactions'];
$totalDiscounts = $data['totalDiscounts'];
$totalVatExemption = $data['totalVatExemption'];
$realRevenueToday = $data['realRevenueToday'];
$realRevenueMonth = $data['realRevenueMonth'];
$realRevenueYear = $data['realRevenueYear'];
$totalSalesYear = $data['totalSalesYear'];
$detailCashierId = isset($_GET['detail_cashier']) ? (int)$_GET['detail_cashier'] : 0;
$detailPeriod = $_GET['detail_period'] ?? 'date';
$detailValue = $_GET['detail_value'] ?? date('Y-m-d');
$cashierList = $report->getCashierList();
$salesDetail = $report->getSalesDetailReport($detailPeriod, $detailValue, $detailCashierId);
$salesDetailRows = $salesDetail['rows'];
$sellingCashierId = isset($_GET['selling_cashier']) ? (int) $_GET['selling_cashier'] : 0;
$sellingPeriod = $_GET['selling_period'] ?? 'date';
$sellingValue = $_GET['selling_value'] ?? date('Y-m-d');
$sellingReport = $report->getSellingProductsReport($sellingPeriod, $sellingValue, $sellingCashierId);
$sellingProducts = $sellingReport['rows'];
$vatCashierId = isset($_GET['vat_cashier']) ? (int) $_GET['vat_cashier'] : 0;
$vatPeriod = $_GET['vat_period'] ?? 'date';
$vatValue = $_GET['vat_value'] ?? date('Y-m-d');
$vatReport = $report->getVatDiscountReport($vatPeriod, $vatValue, $vatCashierId);
$vatDiscountRows = $vatReport['rows'];
$registerCashierId = isset($_GET['register_cashier']) ? (int) $_GET['register_cashier'] : 0;
$registerPeriod = $_GET['register_period'] ?? 'date';
$registerValue = $_GET['register_value'] ?? date('Y-m-d');
$filteredRegisterClosings = $report->getRegisterClosings($registerPeriod, $registerValue, $registerCashierId);
$accountPeriod = $_GET['account_period'] ?? 'date';
$accountValue = $_GET['account_value'] ?? date('Y-m-d');
$accountReport = $report->getAccountActivityReport($accountPeriod, $accountValue, 0);
$accountActivityRows = $accountReport['rows'];
$registerClosings = $report->getRegisterClosings();

// =========================================================
// ACTIVITY LOGS (audit trail) — Task 42
// =========================================================
require_once __DIR__ . '/../conn/activity_log.php';
mmb_activity_log_ensure_table($db);
$activityLogFilters = [
    'username'  => trim((string)($_GET['alog_user'] ?? '')),
    'action'    => trim((string)($_GET['alog_action'] ?? '')),
    'module'    => trim((string)($_GET['alog_module'] ?? '')),
    'date_from' => trim((string)($_GET['alog_from'] ?? '')),
    'date_to'   => trim((string)($_GET['alog_to'] ?? '')),
];
$activityLogPage = isset($_GET['alog_page']) ? max(1, (int)$_GET['alog_page']) : 1;
$activityLogsData = $report->getActivityLogs($activityLogFilters, $activityLogPage, 50);
$activityLogRows = $activityLogsData['rows'];
$activityLogUsers = $report->getActivityLogUsers();
$activityLogSummary = $report->getActivityLogSummary();

/** Build a pagination link that preserves all active activity-log filters. */
function alog_page_url(int $page): string
{
    $q = $_GET;
    $q['tab'] = 'reports';
    $q['alog_page'] = $page;
    unset($q['detail_cashier'], $q['detail_period'], $q['detail_value']);
    return '?' . http_build_query($q);
}

/** Bootstrap badge color for an activity action. */
function alog_badge(string $action): string
{
    if (preg_match('/(_delete|_failed|_blocked|_rejected|disposal)/', $action)) {
        return 'danger';
    }
    if (preg_match('/(_add|_open|_completed|_approved|^login$|^user_registration$)/', $action)) {
        return 'success';
    }
    if (preg_match('/(_update|_close|_processed)/', $action)) {
        return 'warning';
    }
    return 'secondary';
}
$detailGrossTotal = 0.0;
$detailDiscountTotal = 0.0;
$detailVatTotal = 0.0;
$detailNetTotal = 0.0;
$detailRefundTotal = 0.0;
$detailCogsReversedTotal = 0.0;
$detailNetAfterRefundTotal = 0.0;
$detailRealRevenueTotal = 0.0;
function report_text($value): string
{
    $value = trim((string)$value);
    return htmlspecialchars($value !== '' ? $value : 'N/A', ENT_QUOTES, 'UTF-8');
}

foreach ($salesDetailRows as $detailRow) {
    $detailGrossTotal += (float)($detailRow['gross_subtotal'] ?? 0);
    $detailDiscountTotal += (float)($detailRow['discount_total'] ?? 0);
    $detailVatTotal += (float)($detailRow['total_vat_exemption'] ?? 0);
    $detailNetTotal += (float)($detailRow['total_amount'] ?? 0);
    $detailRefundTotal += (float)($detailRow['refund_total'] ?? 0);
    $detailCogsReversedTotal += (float)($detailRow['cogs_reversed'] ?? 0);
    $detailNetAfterRefundTotal += (float)($detailRow['net_after_refund'] ?? 0);
    $detailRealRevenueTotal += (float)($detailRow['real_revenue'] ?? 0);
}
?>

<link rel="stylesheet" href="../css/report.css?v=5">

<div class="report-page">

        <!-- PAGE HEADER -->
        <div class="page-head">
            <div>
                <h2>Reports &amp; Analytics</h2>
                <p class="page-sub">Sales details, account activity, refunds, and inventory insights.</p>
            </div>
            <span class="report-date-pill"><?= date('F j, Y') ?></span>
        </div>

        <div id="reportContent">

            <!-- SUMMARY -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm summary-card" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#topProductsModal">
                        <div>Top Selling Product</div>
                        <div class="summary-value">
                            <?= htmlspecialchars($topProducts[0]['product_name'] ?? 'N/A') ?>
                        </div>
                        <small class="text-muted"><?= $topProducts[0]['total_sold'] ?? 0 ?> units sold</small>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm summary-card" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#salesDetailModal">
                        <div>Sales Detail Report</div>
                        <div class="summary-value">₱<?= number_format($detailRealRevenueTotal, 2) ?></div>
                        <small class="text-muted">Real profit for <?= htmlspecialchars($salesDetail['value']) ?></small>
                    </div>
                </div>

                <div class="col-md-4">
                    <?php 
                        $expiredProducts = array_filter($inventory, function($row) {
                            return $row['expiry_date'] && strtotime($row['expiry_date']) < time();
                        });
                        $expiredCount = count($expiredProducts);
                    ?>
                    <div class="card shadow-sm summary-card" style="cursor: pointer; <?= $expiredCount > 0 ? 'border-danger; background-color: #fef2f2;' : '' ?>" data-bs-toggle="modal" data-bs-target="#expiredProductsModal">
                        <div>Expired Products</div>
                        <div class="summary-value <?= $expiredCount > 0 ? 'text-danger' : '' ?>">
                            <?= $expiredCount ?>
                        </div>
                        <small class="text-muted"><?= $expiredCount > 0 ? 'Action required' : 'All items safe' ?></small>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm summary-card" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#vatDiscountModal">
                        <div>VAT Exemption & Discount</div>
                        <div class="summary-value">
                            ₱<?= number_format(($totalVatExemption ?? 0) + ($totalDiscounts ?? 0), 2) ?>
                        </div>
                        <small class="text-muted">
                            VAT: ₱<?= number_format($totalVatExemption ?? 0, 2) ?> | Discount: ₱<?= number_format($totalDiscounts ?? 0, 2) ?>
                        </small>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm summary-card" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#registerClosingReportModal">
                        <div>Register Opening &amp; Closing Report</div>
                        <div class="summary-value"><?= count($registerClosings) ?></div>
                        <small class="text-muted">Opening and closing drawer records</small>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm summary-card" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#cashierModal">
                        <div>All Account Activity</div>
                        <div class="summary-value"><?= count($accountActivityRows) ?></div>
                        <small class="text-muted">Accounts and transactions processed</small>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm summary-card" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#activityLogsModal">
                        <div>Activity Logs</div>
                        <div class="summary-value"><?= (int) $activityLogSummary['today_count'] ?></div>
                        <small class="text-muted">
                            <?php if (!empty($activityLogSummary['last_activity'])): ?>
                                Actions today &middot; last <?= date('M j, g:i A', strtotime((string)$activityLogSummary['last_activity'])) ?>
                            <?php else: ?>
                                Full audit trail &mdash; who did what, when
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div><!-- End of #reportContent -->

        <!-- MONTHLY SALES TREND MODAL -->
        <div class="modal fade" id="salesDetailModal" tabindex="-1" aria-labelledby="salesDetailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="salesDetailModalLabel">Sales Detail Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <form method="GET" id="salesDetailFilterForm" class="row g-2 align-items-end mb-3">
                            <input type="hidden" name="tab" value="reports">
                            <div class="col-md-3">
                                <label for="detail_period" class="form-label">Report period</label>
                                <select id="detail_period" name="detail_period" class="form-select">
                                    <option value="date" <?= $salesDetail['period'] === 'date' ? 'selected' : '' ?>>Specific date</option>
                                    <option value="month" <?= $salesDetail['period'] === 'month' ? 'selected' : '' ?>>Month</option>
                                    <option value="year" <?= $salesDetail['period'] === 'year' ? 'selected' : '' ?>>Year</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="detail_cashier" class="form-label">Cashier</label>
                                <select id="detail_cashier" name="detail_cashier" class="form-select">
                                    <option value="0" <?= $salesDetail['cashier_id'] === 0 ? 'selected' : '' ?>>All cashiers</option>
                                    <?php foreach ($cashierList as $cashier): ?>
                                        <option value="<?= (int)$cashier['id'] ?>" <?= (int)$salesDetail['cashier_id'] === (int)$cashier['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cashier['cashier_name'] ?? $cashier['username'] ?? 'Unknown') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="detail_value" class="form-label">Choose date, month, or year</label>
                                <input id="detail_value" name="detail_value" class="form-control" value="<?= htmlspecialchars($salesDetail['value']) ?>" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">View Report</button>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table id="salesDetailTable" class="table table-striped table-hover table-sm align-middle myTableExport" data-empty="<?= !$salesDetailRows ? '1' : '0' ?>" data-no-responsive="1">
                                <thead class="table-dark"><tr><th>Ref #</th><th>Date & Time</th><th>Cashier</th><th>Items</th><th>Before Discount/VAT</th><th>Discount</th><th>VAT Exempt</th><th>Total Amount</th><th>Refund / Return</th><th>COGS Return Status</th><th>Net After Refund</th><th>Real Profit</th></tr></thead>
                                <tbody>
                                    <?php if (!$salesDetailRows): ?>
                                        <tr><td colspan="12" class="text-center text-muted">No sales found for this period.</td></tr>
                                    <?php else: foreach ($salesDetailRows as $detailRow): ?>
                                        <tr>
                                            <td>#<?= (int)$detailRow['id'] ?></td>
                                            <td><?= date('M d, Y h:i A', strtotime($detailRow['transaction_date'])) ?></td>
                                            <td><?= htmlspecialchars($detailRow['username'] ?? 'N/A') ?></td>
                                            <td><?= (int)($detailRow['items_count'] ?? 0) ?></td>
                                            <td>₱<?= number_format((float)$detailRow['gross_subtotal'], 2) ?></td>
                                            <td class="text-danger">-₱<?= number_format((float)$detailRow['discount_total'], 2) ?></td>
                                            <td class="text-danger">-₱<?= number_format((float)$detailRow['total_vat_exemption'], 2) ?></td>
                                            <td class="text-primary"><strong>₱<?= number_format((float)$detailRow['total_amount'], 2) ?></strong></td>
                                            <td class="text-danger"><?= (float)$detailRow['refund_total'] > 0 ? '-₱' . number_format((float)$detailRow['refund_total'], 2) : '—' ?></td>
                                            <td class="text-warning">
                                                <?php if ((float)$detailRow['cogs_reversed'] > 0): ?>
                                                    <strong>Restocked</strong><br>+₱<?= number_format((float)$detailRow['cogs_reversed'], 2) ?> reversed
                                                <?php elseif ((float)$detailRow['refund_total'] > 0): ?>
                                                    <strong>Disposed</strong><br>COGS retained
                                                <?php else: ?>
                                                    <span class="text-muted">No return</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-primary">₱<?= number_format((float)$detailRow['net_after_refund'], 2) ?></td>
                                            <td class="text-primary"><strong>₱<?= number_format((float)$detailRow['real_revenue'], 2) ?></strong></td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                                <tfoot class="table-light fw-bold"><tr><td colspan="4">Period totals</td><td>₱<?= number_format($detailGrossTotal, 2) ?></td><td class="text-danger">-₱<?= number_format($detailDiscountTotal, 2) ?></td><td class="text-danger">-₱<?= number_format($detailVatTotal, 2) ?></td><td class="text-primary">₱<?= number_format($detailNetTotal, 2) ?></td><td class="text-danger">-₱<?= number_format($detailRefundTotal, 2) ?></td><td class="text-warning">Restocked: +₱<?= number_format($detailCogsReversedTotal, 2) ?></td><td class="text-primary">₱<?= number_format($detailNetAfterRefundTotal, 2) ?></td><td class="text-primary">₱<?= number_format($detailRealRevenueTotal, 2) ?></td></tr></tfoot>
                            </table>
                        </div>
                        <div class="alert alert-light border mt-3 mb-0 small">
                            <strong>Real Profit formula:</strong> Net After Refund − COGS + COGS Reversed. A restocked return reverses its cost; a disposed return keeps the cost as an expense.
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
                </div>
            </div>
        </div>

        <!-- REGISTER CLOSING REPORT MODAL -->
        <div class="modal fade" id="registerClosingReportModal" tabindex="-1" aria-labelledby="registerClosingReportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerClosingReportModalLabel">Register Opening &amp; Closing Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <form method="GET" id="registerFilterForm" class="row g-2 align-items-end mb-3">
                            <input type="hidden" name="tab" value="reports">
                            <div class="col-md-3">
                                <label for="register_period" class="form-label">Report period</label>
                                <select id="register_period" name="register_period" class="form-select">
                                    <option value="date" <?= $registerPeriod === 'date' ? 'selected' : '' ?>>Specific date</option>
                                    <option value="month" <?= $registerPeriod === 'month' ? 'selected' : '' ?>>Month</option>
                                    <option value="year" <?= $registerPeriod === 'year' ? 'selected' : '' ?>>Year</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="register_cashier" class="form-label">Cashier</label>
                                <select id="register_cashier" name="register_cashier" class="form-select">
                                    <option value="0" <?= $registerCashierId === 0 ? 'selected' : '' ?>>All cashiers</option>
                                    <?php foreach ($cashierList as $cashier): ?>
                                        <option value="<?= (int) $cashier['id'] ?>" <?= $registerCashierId === (int) $cashier['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cashier['cashier_name'] ?? $cashier['username'] ?? 'Unknown') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="register_value" class="form-label">Choose date, month, or year</label>
                                <input id="register_value" name="register_value" class="form-control" value="<?= htmlspecialchars($registerValue) ?>" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">View Report</button>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm align-middle myTableExport">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Opening ID</th>
                                        <th>Close ID</th>
                                        <th>Business Date</th>
                                        <th>Cashier</th>
                                        <th>Opening Cash</th>
                                        <th>System Cash</th>
                                        <th>Counted Cash</th>
                                        <th>Variance</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                        <th>Opened At</th>
                                        <th>Closed At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!$filteredRegisterClosings): ?>
                                        <tr><td colspan="12" class="text-center text-muted">No register opening or closing records.</td></tr>
                                    <?php else: foreach ($filteredRegisterClosings as $closing): ?>
                                        <?php $variance = (float)($closing['variance'] ?? 0); ?>
                                        <tr>
                                            <td><?= $closing['opening_id'] ? '#' . (int)$closing['opening_id'] : '—' ?></td>
                                            <td><?= $closing['closing_id'] ? '#' . (int)$closing['closing_id'] : '—' ?></td>
                                            <td><?= htmlspecialchars($closing['business_date']) ?></td>
                                            <td><?= htmlspecialchars($closing['cashier_name'] ?? 'N/A') ?></td>
                                            <td>₱<?= number_format((float)$closing['opening_cash'], 2) ?></td>
                                            <td>₱<?= number_format((float)$closing['system_cash'], 2) ?></td>
                                            <td>₱<?= number_format((float)$closing['counted_cash'], 2) ?></td>
                                            <td class="<?= $variance === 0.0 ? 'text-body' : ($variance < 0 ? 'text-danger' : 'text-warning') ?>">
                                                <?= $variance < 0 ? '-₱' : '₱' ?><?= number_format(abs($variance), 2) ?>
                                            </td>
                                            <td><?= htmlspecialchars($closing['register_status'] ?? '—') ?></td>
                                            <td><?= htmlspecialchars($closing['closing_notes'] ?? $closing['opening_notes'] ?? '—') ?></td>
                                            <td><?= $closing['opened_at'] ? date('M d, Y h:i A', strtotime($closing['opened_at'])) : '—' ?></td>
                                            <td><?= $closing['closed_at'] ? date('M d, Y h:i A', strtotime($closing['closed_at'])) : '—' ?></td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer" style="background: #f8f9fa;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MONTHLY SALES TREND MODAL -->
        <div class="modal fade" id="monthlySalesModal" tabindex="-1" aria-labelledby="monthlySalesModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="monthlySalesModalLabel">
                                Monthly Sales Trend & Performance
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover myTableExport">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Month & Year</th>
                                            <th>Total Transactions</th>
                                            <th>Gross Revenue</th>
                                            <th>Net Revenue</th>
                                            <th>Cost</th>
                                            <th>Profit</th>
                                            <th>Average per Transaction</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $prevMonthSales = 0;
                                            foreach ($yearlySales as $row):
                                                $grossRevenue = $row['gross_revenue'] ?? 0;
                                                $netRevenue = $row['net_revenue'] ?? 0;
                                                $totalCost = $row['total_cost'] ?? 0;
                                                $profit = $row['profit'] ?? 0;
                                                $percentChange = $prevMonthSales > 0
                                                    ? (($netRevenue - $prevMonthSales) / $prevMonthSales) * 100
                                                    : 0;
                                                $performanceClass = $percentChange >= 0 ? 'text-primary' : 'text-danger';
                                                $performanceIcon = $percentChange >= 0
                                                    ? '<i class="fas fa-arrow-trend-up me-1"></i>'
                                                    : '<i class="fas fa-arrow-trend-down me-1"></i>';
                                        ?>
                                        <tr>
                                            <td>
                                                <strong>
                                                    <?= htmlspecialchars((string)($monthNames[(int)$row['sale_month']] ?? $row['sale_month']), ENT_QUOTES, 'UTF-8') ?>
                                                    <?= $row['sale_year'] ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    <?= $row['total_transactions'] ?>
                                                </span>
                                            </td>
                                            <td class="text-info">
                                                <strong>
                                                    ₱<?= number_format($grossRevenue, 2) ?>
                                                </strong>
                                            </td>
                                            <td class="text-primary">
                                                <strong>
                                                    ₱<?= number_format($netRevenue, 2) ?>
                                                </strong>
                                            </td>
                                            <td class="text-warning">
                                                ₱<?= number_format($totalCost, 2) ?>
                                            </td>
                                            <td class="<?= $profit >= 0 ? 'text-primary' : 'text-danger' ?>">
                                                <strong>
                                                    ₱<?= number_format($profit, 2) ?>
                                                </strong>
                                            </td>
                                            <td>
                                                ₱<?= number_format($row['monthly_avg'] ?? 0, 2) ?>
                                            </td>
                                        </tr>
                                        <?php 
                                            $prevMonthSales = $netRevenue;
                                            endforeach; 
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="modal-footer" style="background: #f8f9fa;">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    

    <!-- TRANSACTION DETAILS MODAL -->
    <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionModalLabel">
                        Transaction Details (Last 500)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm myTableExport">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ref #</th>
                                    <th>Date & Time</th>
                                    <th>Cashier</th>
                                    <th>Items</th>
                                    <th>Before Discount/VAT</th>
                                    <th>Discount</th>
                                    <th>VAT Exempt</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $grandTotalAllTransactions = 0;
                                $grandTotalDiscounts = 0;
                                $grandTotalVatExemption = 0;
                                foreach ($allTransactions as $row): 
                                    $grandTotalAllTransactions += $row['total_amount'] ?? 0;
                                    $grandTotalDiscounts += $row['discount_total'] ?? 0;
                                    $grandTotalVatExemption += $row['total_vat_exemption'] ?? 0;
                                ?>
                                    <tr>
                                        <td><small><code>#<?= $row['id'] ?></code></small></td>
                                        <td><?= date('M d, Y h:i A', strtotime($row['transaction_date'])) ?></td>
                                        <td><?= htmlspecialchars($row['username'] ?? 'N/A') ?></td>
                                        <td><small><?= $row['items_count'] ?? 0 ?> items</small></td>
                                        <td>₱<?= number_format($row['gross_subtotal'] ?? 0, 2) ?></td>
                                        <td class="text-danger">
                                            <?= $row['discount_total'] > 0 ? '-₱' . number_format($row['discount_total'], 2) : '—' ?>
                                        </td>
                                        <td class="text-danger">
                                            <?= $row['total_vat_exemption'] > 0 ? '-₱' . number_format($row['total_vat_exemption'], 2) : '—' ?>
                                        </td>
                                        <td class="text-primary"><strong>₱<?= number_format($row['total_amount'] ?? 0, 2) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- GRAND TOTALS -->
                    <div class="alert alert-info mt-3 mb-0">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Total Transactions:</strong> <span class="badge bg-primary"><?= count($allTransactions) ?></span>
                            </div>
                            <div class="col-md-3">
                                <strong>Total Discounts:</strong> 
                                <span class="badge bg-danger">₱<?= number_format($grandTotalDiscounts, 2) ?></span>
                            </div>
                            <div class="col-md-3">
                                <strong>Total VAT Exemption:</strong> 
                                <span class="badge bg-warning">₱<?= number_format($grandTotalVatExemption, 2) ?></span>
                            </div>
                            <div class="col-md-3">
                                <strong>Grand Total Sales:</strong> 
                                <span class="badge badge-soft-danger">₱<?= number_format($grandTotalAllTransactions, 2) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                   
                </div>
            </div>
        </div>
    </div>

    <!-- TOP PRODUCTS MODAL -->
    <div class="modal fade" id="topProductsModal" tabindex="-1" aria-labelledby="topProductsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="topProductsModalLabel">
                        All Selling Products
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="GET" id="sellingProductsFilterForm" class="row g-2 align-items-end mb-3">
                        <input type="hidden" name="tab" value="reports">
                        <div class="col-md-3">
                            <label for="selling_period" class="form-label">Report period</label>
                            <select id="selling_period" name="selling_period" class="form-select">
                                <option value="date" <?= $sellingReport['period'] === 'date' ? 'selected' : '' ?>>Specific date</option>
                                <option value="month" <?= $sellingReport['period'] === 'month' ? 'selected' : '' ?>>Month</option>
                                <option value="year" <?= $sellingReport['period'] === 'year' ? 'selected' : '' ?>>Year</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="selling_cashier" class="form-label">Cashier</label>
                            <select id="selling_cashier" name="selling_cashier" class="form-select">
                                <option value="0" <?= $sellingReport['cashier_id'] === 0 ? 'selected' : '' ?>>All cashiers</option>
                                <?php foreach ($cashierList as $cashier): ?>
                                    <option value="<?= (int) $cashier['id'] ?>" <?= $sellingReport['cashier_id'] === (int) $cashier['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cashier['cashier_name'] ?? $cashier['username'] ?? 'Unknown') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="selling_value" class="form-label">Choose date, month, or year</label>
                            <input id="selling_value" name="selling_value" class="form-control" value="<?= htmlspecialchars($sellingReport['value']) ?>" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">View Report</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover myTableExport">
                            <thead class="table-dark">
                                <tr>
                                    <th>Rank</th><th></th><th>Item Name</th><th>Serving</th><th>Product Form</th><th>Package Size</th><th>Category</th><th>Product Code</th><th>Average Price</th><th data-priority="1">Quantity Purchased</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sellingProducts as $rank => $row): ?>
                                    <tr<?= $rank === 0 ? ' class="mmb-top1-row"' : '' ?>>
                                        <td><?php if ($rank < 3): ?><span class="mmb-rank-medal mmb-rank-medal--<?= $rank + 1 ?>"><i class="fas fa-medal"></i></span><?php else: ?><strong><?= $rank + 1 ?></strong><?php endif; ?></td>
                                        <td>
                                            <?php if (!empty(trim((string)($row['imageproduct'] ?? '')))): ?>
                                                <span class="mmb-thumb mmb-thumb--md"><img src="../img/<?= report_text($row['imageproduct']) ?>" alt="<?= report_text(trim(($row['branded_name'] ?? '') . ' ' . ($row['generic_name'] ?? ''))) ?>" loading="lazy"></span>
                                            <?php else: ?>
                                                <span class="mmb-thumb mmb-thumb--md mmb-thumb--empty"><i class="fas fa-capsules"></i></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?= report_text(trim(($row['branded_name'] ?? '') . ' ' . ($row['generic_name'] ?? ''))) ?></strong></td>
                                        <td><?= report_text(trim(($row['strength'] ?? '') . ' ' . ($row['measurement_name'] ?? ''))) ?></td>
                                        <td><?= report_text($row['dosage_form'] ?? '') ?></td>
                                        <td><?= report_text(((float)($row['strength_per_quantity'] ?? 0) > 0 ? (string)$row['strength_per_quantity'] . ' ' : '') . trim((string)($row['strength_per_quantity_unit'] ?? ''))) ?></td>
                                        <td><?= report_text($row['category_name'] ?? '') ?></td>
                                        <td><?= report_text($row['barcode'] ?? '') ?></td>
                                        <td>₱<?= number_format((float)($row['average_price'] ?? 0), 2) ?></td>
                                        <td><span class="badge bg-primary fs-6"><?= (int)$row['total_sold'] ?> units</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ALL SELLING PRODUCTS MODAL -->
    <div class="modal fade" id="top5ProductsModal" tabindex="-1" aria-labelledby="top5ProductsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="top5ProductsModalLabel">
                        All Selling Products
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover myTableExport">
                            <thead class="table-dark">
                                <tr><th>Rank</th><th></th><th>Item Name</th><th>Serving</th><th>Product Form</th><th>Package Size</th><th>Category</th><th>Product Code</th><th>Average Price</th><th data-priority="1">Quantity Purchased</th></tr>
                            </thead>
                            <tbody>
                                <?php 
                                    foreach ($sellingProducts as $i => $product):
                                ?>
                                    <tr<?= $i === 0 ? ' class="mmb-top1-row"' : '' ?>>
                                        <td><?php if ($i < 3): ?><span class="mmb-rank-medal mmb-rank-medal--<?= $i + 1 ?>"><i class="fas fa-medal"></i></span><?php else: ?><strong><?= $i + 1 ?></strong><?php endif; ?></td>
                                        <td>
                                            <?php if (!empty(trim((string)($product['imageproduct'] ?? '')))): ?>
                                                <span class="mmb-thumb mmb-thumb--md"><img src="../img/<?= report_text($product['imageproduct']) ?>" alt="<?= report_text(trim(($product['branded_name'] ?? '') . ' ' . ($product['generic_name'] ?? ''))) ?>" loading="lazy"></span>
                                            <?php else: ?>
                                                <span class="mmb-thumb mmb-thumb--md mmb-thumb--empty"><i class="fas fa-capsules"></i></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?= report_text(trim(($product['branded_name'] ?? '') . ' ' . ($product['generic_name'] ?? ''))) ?></strong></td>
                                        <td><?= report_text(trim(($product['strength'] ?? '') . ' ' . ($product['measurement_name'] ?? ''))) ?></td>
                                        <td><?= report_text($product['dosage_form'] ?? '') ?></td>
                                        <td><?= report_text(((float)($product['strength_per_quantity'] ?? 0) > 0 ? (string)$product['strength_per_quantity'] . ' ' : '') . trim((string)($product['strength_per_quantity_unit'] ?? ''))) ?></td>
                                        <td><?= report_text($product['category_name'] ?? '') ?></td>
                                        <td><?= report_text($product['barcode'] ?? '') ?></td>
                                        <td>₱<?= number_format((float)($product['average_price'] ?? 0), 2) ?></td>
                                        <td><span class="badge bg-primary fs-6"><?= (int)$product['total_sold'] ?> units</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ALL ACCOUNT ACTIVITY MODAL -->
    <div class="modal fade" id="cashierModal" tabindex="-1" aria-labelledby="cashierModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cashierModalLabel">
                        All Account Activity Report
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="GET" id="accountActivityFilterForm" class="row g-2 align-items-end mb-3">
                        <input type="hidden" name="tab" value="reports">
                        <div class="col-md-3">
                            <label for="account_period" class="form-label">Report period</label>
                            <select id="account_period" name="account_period" class="form-select">
                                <option value="date" <?= $accountReport['period'] === 'date' ? 'selected' : '' ?>>Specific date</option>
                                <option value="month" <?= $accountReport['period'] === 'month' ? 'selected' : '' ?>>Month</option>
                                <option value="year" <?= $accountReport['period'] === 'year' ? 'selected' : '' ?>>Year</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="account_value" class="form-label">Choose date, month, or year</label>
                            <input id="account_value" name="account_value" class="form-control" value="<?= htmlspecialchars($accountReport['value']) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">View Report</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover myTableExport">
                            <thead class="table-dark">
                                <tr>
                                    <th data-priority="4">Account Name</th>
                                    <th data-priority="5">Username</th>
                                    <th data-priority="6">Position</th>
                                    <th data-priority="1">Account Created</th>
                                    <th data-priority="3">Transactions Processed</th>
                                    <th data-priority="2">Total Sales</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($accountActivityRows as $row): ?>
                                    <tr>
                                        <td><strong><?= report_text($row['account_name'] ?? '') ?></strong></td>
                                        <td><?= report_text($row['username'] ?? '') ?></td>
                                        <td><?= report_text($row['position'] ?? '') ?></td>
                                        <td><?= !empty($row['created_at']) ? date('M d, Y', strtotime($row['created_at'])) : 'N/A' ?></td>
                                        <td><span class="badge bg-info"><?= (int) ($row['total_transactions'] ?? 0) ?> transactions</span></td>
                                        <td>₱<?= number_format((float) ($row['total_sales'] ?? 0), 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- EXPIRED PRODUCTS MODAL -->
    <div class="modal fade" id="expiredProductsModal" tabindex="-1" aria-labelledby="expiredProductsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="expiredProductsModalLabel">
                        Expired Products (Action Required)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <?php 
                        $expiredProducts = array_filter($inventory, function($row) {
                            return $row['expiry_date'] && strtotime($row['expiry_date']) < time();
                        });
                    ?>
                    <?php if (empty($expiredProducts)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle"></i> No expired products. All items are within expiry dates.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover myTableExport">
                                <thead class="table-dark">
                                    <tr>
                                        <th data-priority="1">Product</th><th data-priority="2">Strength</th><th data-priority="3">Product Form</th><th data-priority="9">Category</th><th data-priority="10">Product Code</th><th data-priority="4">Batch No</th><th data-priority="5">Date Expired</th><th data-priority="6">Quantity</th><th data-priority="7">Purchase Cost</th><th data-priority="8">Sale Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($expiredProducts as $row): ?>
                                        <tr class="table-danger">
                                            <td><strong><?= report_text(trim(($row['branded_name'] ?? '') . ' ' . ($row['generic_name'] ?? ''))) ?></strong></td>
                                            <td><?= report_text(trim(($row['strength'] ?? '') . ' ' . ($row['measurement_name'] ?? ''))) ?></td>
                                            <td><?= report_text($row['dosage_form'] ?? '') ?></td>
                                            <td><?= report_text($row['category_name'] ?? '') ?></td>
                                            <td><?= report_text($row['barcode'] ?? '') ?></td>
                                            <td><?= report_text($row['batch_number'] ?? '') ?></td>
                                            <td><?= date('M d, Y', strtotime($row['expiry_date'])) ?> <span class="badge bg-danger"><?= ceil((time() - strtotime($row['expiry_date'])) / 86400) ?> days ago</span></td>
                                            <td><span class="badge bg-warning"><?= $row['quantity'] ?> units</span></td>
                                            <td>₱<?= number_format((float)($row['purchase_cost'] ?? 0), 2) ?></td>
                                            <td>₱<?= number_format((float)($row['sale_price'] ?? 0), 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer" style="background: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- DAILY SALES SUMMARY MODAL -->
    <div class="modal fade" id="dailySalesModal" tabindex="-1" aria-labelledby="dailySalesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dailySalesModalLabel">
                        Daily Sales Summary (Last 30 Days)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover myTableExport">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="fas fa-calendar-alt me-2"></i>Date</th>
                                    <th><i class="fas fa-receipt me-2"></i>Transactions</th>
                                    <th><i class="fas fa-money-bill-wave me-2"></i>Daily Total</th>
                                    <th><i class="fas fa-calculator me-2"></i>Average</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $totalForSummary = 0;
                                    foreach ($dailySales as $row): 
                                        $totalForSummary += $row['daily_total'] ?? 0;
                                ?>
                                    <tr>
                                        <td><strong><?= date('l, M d, Y', strtotime($row['sale_date'])) ?></strong></td>
                                        <td><span class="badge bg-info"><?= $row['total_transactions'] ?></span></td>
                                        <td class="text-primary"><strong>₱<?= number_format($row['daily_total'] ?? 0, 2) ?></strong></td>
                                        <td>₱<?= number_format($row['daily_avg'] ?? 0, 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr style="background-color: #f8f9fa; font-weight: bold;">
                                    <td colspan="2">Total (Last 30 Days)</td>
                                    <td class="text-primary">₱<?= number_format($totalForSummary, 2) ?></td>
                                    <td>₱<?= number_format(count($dailySales) > 0 ? $totalForSummary / count($dailySales) : 0, 2) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- VAT EXEMPTION & DISCOUNT MODAL -->
    <div class="modal fade" id="vatDiscountModal" tabindex="-1" aria-labelledby="vatDiscountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vatDiscountModalLabel">
                        VAT Exemption & Discount Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <form method="GET" id="vatDiscountFilterForm" class="row g-2 align-items-end mb-3">
                        <input type="hidden" name="tab" value="reports">
                        <div class="col-md-3">
                            <label for="vat_period" class="form-label">Report period</label>
                            <select id="vat_period" name="vat_period" class="form-select">
                                <option value="date" <?= $vatReport['period'] === 'date' ? 'selected' : '' ?>>Specific date</option>
                                <option value="month" <?= $vatReport['period'] === 'month' ? 'selected' : '' ?>>Month</option>
                                <option value="year" <?= $vatReport['period'] === 'year' ? 'selected' : '' ?>>Year</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="vat_cashier" class="form-label">Cashier</label>
                            <select id="vat_cashier" name="vat_cashier" class="form-select">
                                <option value="0" <?= $vatReport['cashier_id'] === 0 ? 'selected' : '' ?>>All cashiers</option>
                                <?php foreach ($cashierList as $cashier): ?>
                                    <option value="<?= (int) $cashier['id'] ?>" <?= $vatReport['cashier_id'] === (int) $cashier['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cashier['cashier_name'] ?? $cashier['username'] ?? 'Unknown') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="vat_value" class="form-label">Choose date, month, or year</label>
                            <input id="vat_value" name="vat_value" class="form-control" value="<?= htmlspecialchars($vatReport['value']) ?>" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">View Report</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm myTableExport">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Transaction Date</th>
                                    <th>Customer Name</th>
                                    <th>Customer Type</th>
                                    <th>VAT Exemption</th>
                                    <th>Discount</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $grandTotalVatExemp = 0;
                                $grandTotalDiscount = 0;
                                $grandTotal = 0;
                                foreach ($vatDiscountRows as $row): 
                                    $vatAmount = $row['total_vat_exemption'] ?? 0;
                                    $discAmount = $row['discount_total'] ?? 0;
                                    
                                    if ($vatAmount > 0 || $discAmount > 0):
                                        $grandTotalVatExemp += $vatAmount;
                                        $grandTotalDiscount += $discAmount;
                                        $rowTotal = $vatAmount + $discAmount;
                                        $grandTotal += $rowTotal;
                                ?>
                                    <tr>
                                        <td>
                                            <small>
                                                <?php 
                                                    if (in_array($row['customer_type'], ['pwd', 'senior']) && $row['govt_id_number']) {
                                                        echo '<code style="background-color: #dbeafe; padding: 2px 6px; border-radius: 4px;">' . htmlspecialchars($row['govt_id_number']) . '</code>';
                                                    } else {
                                                        echo '<code>#' . htmlspecialchars($row['id']) . '</code>';
                                                    }
                                                ?>
                                            </small>
                                        </td>
                                        <td><small><?= date('M d, Y h:i A', strtotime($row['transaction_date'])) ?></small></td>
                                        <td><?= htmlspecialchars($row['customer_name'] ?? 'N/A') ?></td>
                                        <td>
                                            <?php 
                                                $typeClass = match($row['customer_type']) {
                                                    'pwd' => 'bg-info',
                                                    'senior' => 'bg-warning',
                                                    default => 'bg-secondary'
                                                };
                                                $typeLabel = $row['customer_type'] ? ucfirst($row['customer_type']) : 'Regular';
                                            ?>
                                            <span class="badge <?= $typeClass ?>"><?= $typeLabel ?></span>
                                        </td>
                                        <td class="text-warning"><strong>₱<?= number_format($vatAmount, 2) ?></strong></td>
                                        <td class="text-danger"><strong>₱<?= number_format($discAmount, 2) ?></strong></td>
                                        <td class="text-info"><strong>₱<?= number_format($rowTotal, 2) ?></strong></td>
                                    </tr>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-totals">
                                    <td colspan="4" style="text-align: right; padding: 15px;">TOTAL</td>
                                    <td style="padding: 15px; border-left: 2px solid #fbbf24;"><i class="fas fa-check-circle me-2"></i>₱<?= number_format($grandTotalVatExemp, 2) ?></td>
                                    <td style="padding: 15px; border-left: 2px solid #ef4444;"><i class="fas fa-tag me-2"></i>₱<?= number_format($grandTotalDiscount, 2) ?></td>
                                    <td style="padding: 15px; border-left: 2px solid #06b6d4; background-color: rgba(252, 252, 252, 0.1);">₱<?= number_format($grandTotal, 2) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ACTIVITY LOGS MODAL (audit trail) — Task 42 -->
    <div class="modal fade" id="activityLogsModal" tabindex="-1" aria-labelledby="activityLogsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="activityLogsModalLabel">Activity Log Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <p class="text-muted small mb-3">
                        Full audit trail of the system &mdash; who logged in at which time, and every add, edit,
                        delete, sale, return, and settings change recorded with user, timestamp, and IP address.
                    </p>

                    <form method="GET" id="activityLogFilterForm" class="row g-2 align-items-end mb-3">
                        <input type="hidden" name="tab" value="reports">
                        <div class="col-6 col-md-2">
                            <label for="alog_user" class="form-label">User</label>
                            <select id="alog_user" name="alog_user" class="form-select">
                                <option value="">All users</option>
                                <?php foreach ($activityLogUsers as $alogUser): ?>
                                    <option value="<?= htmlspecialchars($alogUser, ENT_QUOTES, 'UTF-8') ?>"
                                        <?= $activityLogFilters['username'] === $alogUser ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($alogUser, ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="alog_module" class="form-label">Module</label>
                            <select id="alog_module" name="alog_module" class="form-select">
                                <option value="">All modules</option>
                                <?php foreach (['auth' => 'Authentication', 'products' => 'Products', 'inventory' => 'Inventory', 'sales' => 'Sales', 'users' => 'Users', 'settings' => 'Settings'] as $alogMod => $alogModLabel): ?>
                                    <option value="<?= $alogMod ?>" <?= $activityLogFilters['module'] === $alogMod ? 'selected' : '' ?>><?= $alogModLabel ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="alog_action" class="form-label">Action</label>
                            <select id="alog_action" name="alog_action" class="form-select">
                                <option value="">All actions</option>
                                <optgroup label="Authentication">
                                    <option value="login" <?= $activityLogFilters['action'] === 'login' ? 'selected' : '' ?>>Sign-in</option>
                                    <option value="login_failed" <?= $activityLogFilters['action'] === 'login_failed' ? 'selected' : '' ?>>Failed sign-in</option>
                                    <option value="login_blocked" <?= $activityLogFilters['action'] === 'login_blocked' ? 'selected' : '' ?>>Blocked sign-in</option>
                                    <option value="logout" <?= $activityLogFilters['action'] === 'logout' ? 'selected' : '' ?>>Sign-out</option>
                                    <option value="password_reset_requested" <?= $activityLogFilters['action'] === 'password_reset_requested' ? 'selected' : '' ?>>Password reset requested</option>
                                    <option value="password_reset_completed" <?= $activityLogFilters['action'] === 'password_reset_completed' ? 'selected' : '' ?>>Password reset completed</option>
                                </optgroup>
                                <optgroup label="Products">
                                    <option value="product_add" <?= $activityLogFilters['action'] === 'product_add' ? 'selected' : '' ?>>Product added</option>
                                    <option value="product_update" <?= $activityLogFilters['action'] === 'product_update' ? 'selected' : '' ?>>Product updated</option>
                                    <option value="product_delete" <?= $activityLogFilters['action'] === 'product_delete' ? 'selected' : '' ?>>Product deleted</option>
                                    <option value="category_add,category_update,category_delete" <?= $activityLogFilters['action'] === 'category_add,category_update,category_delete' ? 'selected' : '' ?>>Category changes</option>
                                    <option value="supplier_add" <?= $activityLogFilters['action'] === 'supplier_add' ? 'selected' : '' ?>>Supplier added</option>
                                    <option value="dosage_form_add,dosage_form_delete" <?= $activityLogFilters['action'] === 'dosage_form_add,dosage_form_delete' ? 'selected' : '' ?>>Product form changes</option>
                                    <option value="measurement_add" <?= $activityLogFilters['action'] === 'measurement_add' ? 'selected' : '' ?>>Measurement added</option>
                                    <option value="serving_unit_add,serving_unit_delete" <?= $activityLogFilters['action'] === 'serving_unit_add,serving_unit_delete' ? 'selected' : '' ?>>Serving unit changes</option>
                                </optgroup>
                                <optgroup label="Inventory">
                                    <option value="stock_update" <?= $activityLogFilters['action'] === 'stock_update' ? 'selected' : '' ?>>Stock updated</option>
                                    <option value="batch_add" <?= $activityLogFilters['action'] === 'batch_add' ? 'selected' : '' ?>>Batch added</option>
                                    <option value="batch_disposal" <?= $activityLogFilters['action'] === 'batch_disposal' ? 'selected' : '' ?>>Batch disposed</option>
                                </optgroup>
                                <optgroup label="Sales">
                                    <option value="sale_completed" <?= $activityLogFilters['action'] === 'sale_completed' ? 'selected' : '' ?>>Sale completed</option>
                                    <option value="return_processed" <?= $activityLogFilters['action'] === 'return_processed' ? 'selected' : '' ?>>Return processed</option>
                                    <option value="register_open" <?= $activityLogFilters['action'] === 'register_open' ? 'selected' : '' ?>>Register opened</option>
                                    <option value="register_close" <?= $activityLogFilters['action'] === 'register_close' ? 'selected' : '' ?>>Register closed</option>
                                </optgroup>
                                <optgroup label="Users">
                                    <option value="user_add" <?= $activityLogFilters['action'] === 'user_add' ? 'selected' : '' ?>>User created</option>
                                    <option value="user_update" <?= $activityLogFilters['action'] === 'user_update' ? 'selected' : '' ?>>User updated</option>
                                    <option value="user_delete" <?= $activityLogFilters['action'] === 'user_delete' ? 'selected' : '' ?>>User deleted</option>
                                    <option value="user_status_change" <?= $activityLogFilters['action'] === 'user_status_change' ? 'selected' : '' ?>>User enabled/disabled</option>
                                    <option value="user_registration" <?= $activityLogFilters['action'] === 'user_registration' ? 'selected' : '' ?>>Registration submitted</option>
                                    <option value="user_registration_rejected" <?= $activityLogFilters['action'] === 'user_registration_rejected' ? 'selected' : '' ?>>Registration rejected</option>
                                    <option value="user_approved" <?= $activityLogFilters['action'] === 'user_approved' ? 'selected' : '' ?>>Registration approved</option>
                                </optgroup>
                                <optgroup label="Settings">
                                    <option value="settings_update" <?= $activityLogFilters['action'] === 'settings_update' ? 'selected' : '' ?>>Store settings changed</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="alog_from" class="form-label">From</label>
                            <input type="date" id="alog_from" name="alog_from" class="form-control" value="<?= htmlspecialchars($activityLogFilters['date_from'], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="alog_to" class="form-label">To</label>
                            <input type="date" id="alog_to" name="alog_to" class="form-control" value="<?= htmlspecialchars($activityLogFilters['date_to'], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-12 col-md-1 d-grid">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </form>

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <div class="text-muted small">
                            <?= (int) $activityLogsData['total'] ?> total entries
                            <?php if ($activityLogsData['pages'] > 1): ?>
                                &middot; page <?= (int) $activityLogsData['page'] ?> of <?= (int) $activityLogsData['pages'] ?>
                            <?php endif; ?>
                        </div>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Activity log pagination">
                            <?php if ($activityLogsData['page'] > 1): ?>
                                <a class="btn btn-outline-secondary" href="<?= htmlspecialchars(alog_page_url($activityLogsData['page'] - 1), ENT_QUOTES, 'UTF-8') ?>#activityLogsModal">&laquo; Newer</a>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary" disabled>&laquo; Newer</button>
                            <?php endif; ?>
                            <?php if ($activityLogsData['page'] < $activityLogsData['pages']): ?>
                                <a class="btn btn-outline-secondary" href="<?= htmlspecialchars(alog_page_url($activityLogsData['page'] + 1), ENT_QUOTES, 'UTF-8') ?>#activityLogsModal">Older &raquo;</a>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary" disabled>Older &raquo;</button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Timestamp</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Module</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($activityLogRows)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No activity recorded yet for this filter. Perform actions in the system
                                            (sign in, add products, make a sale) and they will appear here.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($activityLogRows as $alogRow): ?>
                                        <tr>
                                            <td style="white-space: nowrap;"><small><?= date('M j, Y g:i:s A', strtotime((string)($alogRow['created_at'] ?? ''))) ?></small></td>
                                            <td>
                                                <?php if (!empty($alogRow['username'])): ?>
                                                    <strong><?= htmlspecialchars((string)$alogRow['username'], ENT_QUOTES, 'UTF-8') ?></strong>
                                                <?php else: ?>
                                                    <span class="text-muted">system / guest</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($alogRow['role'])): ?>
                                                    <span class="badge bg-secondary"><?= htmlspecialchars(ucfirst((string)$alogRow['role']), ENT_QUOTES, 'UTF-8') ?></span>
                                                <?php else: ?>
                                                    &mdash;
                                                <?php endif; ?>
                                            </td>
                                            <td><small><?= htmlspecialchars(ucfirst((string)($alogRow['module'] ?? '')), ENT_QUOTES, 'UTF-8') ?></small></td>
                                            <td><span class="badge bg-<?= alog_badge((string)($alogRow['action'] ?? '')) ?>"><?= htmlspecialchars((string)($alogRow['action'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></td>
                                            <td><small><?= htmlspecialchars((string)($alogRow['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></small></td>
                                            <td><small class="text-muted"><?= htmlspecialchars((string)($alogRow['ip_address'] ?? ''), ENT_QUOTES, 'UTF-8') ?: '&mdash;' ?></small></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    </div><!-- End of .container -->

    <!-- Initialize DataTable Export on modals -->
    <script>
        (function () {
            const periodSelect = document.getElementById('detail_period');
            const cashierSelect = document.getElementById('detail_cashier');
            const valueInput = document.getElementById('detail_value');
            const filterForm = document.getElementById('salesDetailFilterForm');
            if (!periodSelect || !valueInput || !filterForm) return;

            const defaults = {
                date: '<?= date('Y-m-d') ?>',
                month: '<?= date('Y-m') ?>',
                year: '<?= date('Y') ?>'
            };

            function updatePeriodInput() {
                const period = periodSelect.value;
                valueInput.type = period === 'date' ? 'date' : period === 'month' ? 'month' : 'number';
                if (period === 'year') {
                    valueInput.min = '2000';
                    valueInput.max = '2100';
                } else {
                    valueInput.removeAttribute('min');
                    valueInput.removeAttribute('max');
                }
                const validValue = period === 'date' ? /^\d{4}-\d{2}-\d{2}$/.test(valueInput.value)
                    : period === 'month' ? /^\d{4}-\d{2}$/.test(valueInput.value)
                    : /^\d{4}$/.test(valueInput.value);
                if (!validValue) valueInput.value = defaults[period];
            }

            function refreshReport() {
                if (valueInput.value) {
                    filterForm.requestSubmit();
                }
            }

            periodSelect.addEventListener('change', function () {
                updatePeriodInput();
                refreshReport();
            });
            if (cashierSelect) {
                cashierSelect.addEventListener('change', refreshReport);
            }
            valueInput.addEventListener('change', refreshReport);
            updatePeriodInput();
        }());

        (function () {
            const periodSelect = document.getElementById('selling_period');
            const cashierSelect = document.getElementById('selling_cashier');
            const valueInput = document.getElementById('selling_value');
            const filterForm = document.getElementById('sellingProductsFilterForm');
            if (!periodSelect || !valueInput || !filterForm) return;

            const defaults = {
                date: '<?= date('Y-m-d') ?>',
                month: '<?= date('Y-m') ?>',
                year: '<?= date('Y') ?>'
            };

            function updateSellingPeriodInput() {
                const period = periodSelect.value;
                valueInput.type = period === 'date' ? 'date' : period === 'month' ? 'month' : 'number';
                if (period === 'year') {
                    valueInput.min = '2000';
                    valueInput.max = '2100';
                } else {
                    valueInput.removeAttribute('min');
                    valueInput.removeAttribute('max');
                }
                const validValue = period === 'date' ? /^\d{4}-\d{2}-\d{2}$/.test(valueInput.value)
                    : period === 'month' ? /^\d{4}-\d{2}$/.test(valueInput.value)
                    : /^\d{4}$/.test(valueInput.value);
                if (!validValue) valueInput.value = defaults[period];
            }

            function refreshSellingReport() {
                if (valueInput.value) filterForm.requestSubmit();
            }

            periodSelect.addEventListener('change', function () {
                updateSellingPeriodInput();
                refreshSellingReport();
            });
            if (cashierSelect) cashierSelect.addEventListener('change', refreshSellingReport);
            valueInput.addEventListener('change', refreshSellingReport);
            updateSellingPeriodInput();
        }());

        (function () {
            const periodSelect = document.getElementById('vat_period');
            const cashierSelect = document.getElementById('vat_cashier');
            const valueInput = document.getElementById('vat_value');
            const filterForm = document.getElementById('vatDiscountFilterForm');
            if (!periodSelect || !valueInput || !filterForm) return;

            const defaults = {
                date: '<?= date('Y-m-d') ?>',
                month: '<?= date('Y-m') ?>',
                year: '<?= date('Y') ?>'
            };

            function updateVatPeriodInput() {
                const period = periodSelect.value;
                valueInput.type = period === 'date' ? 'date' : period === 'month' ? 'month' : 'number';
                if (period === 'year') {
                    valueInput.min = '2000';
                    valueInput.max = '2100';
                } else {
                    valueInput.removeAttribute('min');
                    valueInput.removeAttribute('max');
                }
                const validValue = period === 'date' ? /^\d{4}-\d{2}-\d{2}$/.test(valueInput.value)
                    : period === 'month' ? /^\d{4}-\d{2}$/.test(valueInput.value)
                    : /^\d{4}$/.test(valueInput.value);
                if (!validValue) valueInput.value = defaults[period];
            }

            function refreshVatReport() {
                if (valueInput.value) filterForm.requestSubmit();
            }

            periodSelect.addEventListener('change', function () {
                updateVatPeriodInput();
                refreshVatReport();
            });
            if (cashierSelect) cashierSelect.addEventListener('change', refreshVatReport);
            valueInput.addEventListener('change', refreshVatReport);
            updateVatPeriodInput();
        }());

        (function () {
            const periodSelect = document.getElementById('register_period');
            const cashierSelect = document.getElementById('register_cashier');
            const valueInput = document.getElementById('register_value');
            const filterForm = document.getElementById('registerFilterForm');
            if (!periodSelect || !valueInput || !filterForm) return;

            const defaults = {
                date: '<?= date('Y-m-d') ?>',
                month: '<?= date('Y-m') ?>',
                year: '<?= date('Y') ?>'
            };

            function updateRegisterPeriodInput() {
                const period = periodSelect.value;
                valueInput.type = period === 'date' ? 'date' : period === 'month' ? 'month' : 'number';
                if (period === 'year') {
                    valueInput.min = '2000';
                    valueInput.max = '2100';
                } else {
                    valueInput.removeAttribute('min');
                    valueInput.removeAttribute('max');
                }
                const validValue = period === 'date' ? /^\d{4}-\d{2}-\d{2}$/.test(valueInput.value)
                    : period === 'month' ? /^\d{4}-\d{2}$/.test(valueInput.value)
                    : /^\d{4}$/.test(valueInput.value);
                if (!validValue) valueInput.value = defaults[period];
            }

            function refreshRegisterReport() {
                if (valueInput.value) filterForm.requestSubmit();
            }

            periodSelect.addEventListener('change', function () {
                updateRegisterPeriodInput();
                refreshRegisterReport();
            });
            if (cashierSelect) cashierSelect.addEventListener('change', refreshRegisterReport);
            valueInput.addEventListener('change', refreshRegisterReport);
            updateRegisterPeriodInput();
        }());

        (function () {
            const periodSelect = document.getElementById('account_period');
            const valueInput = document.getElementById('account_value');
            const filterForm = document.getElementById('accountActivityFilterForm');
            if (!periodSelect || !valueInput || !filterForm) return;

            const defaults = {
                date: '<?= date('Y-m-d') ?>',
                month: '<?= date('Y-m') ?>',
                year: '<?= date('Y') ?>'
            };

            function updateAccountPeriodInput() {
                const period = periodSelect.value;
                valueInput.type = period === 'date' ? 'date' : period === 'month' ? 'month' : 'number';
                if (period === 'year') {
                    valueInput.min = '2000';
                    valueInput.max = '2100';
                } else {
                    valueInput.removeAttribute('min');
                    valueInput.removeAttribute('max');
                }
                const validValue = period === 'date' ? /^\d{4}-\d{2}-\d{2}$/.test(valueInput.value)
                    : period === 'month' ? /^\d{4}-\d{2}$/.test(valueInput.value)
                    : /^\d{4}$/.test(valueInput.value);
                if (!validValue) valueInput.value = defaults[period];
            }

            function refreshAccountReport() {
                if (valueInput.value) filterForm.requestSubmit();
            }

            periodSelect.addEventListener('change', function () {
                updateAccountPeriodInput();
                refreshAccountReport();
            });
            valueInput.addEventListener('change', refreshAccountReport);
            updateAccountPeriodInput();
        }());

        $(function () {
            const reportUrl = new URL(window.location.href);
            if (reportUrl.searchParams.has('detail_period') && reportUrl.searchParams.has('detail_value')) {
                const salesDetailModal = document.getElementById('salesDetailModal');
                if (salesDetailModal && window.bootstrap) {
                    bootstrap.Modal.getOrCreateInstance(salesDetailModal).show();
                }
            }
            if (reportUrl.searchParams.has('selling_period') && reportUrl.searchParams.has('selling_value')) {
                const sellingProductsModal = document.getElementById('topProductsModal');
                if (sellingProductsModal && window.bootstrap) {
                    bootstrap.Modal.getOrCreateInstance(sellingProductsModal).show();
                }
            }
            if (reportUrl.searchParams.has('vat_period') && reportUrl.searchParams.has('vat_value')) {
                const vatDiscountModal = document.getElementById('vatDiscountModal');
                if (vatDiscountModal && window.bootstrap) {
                    bootstrap.Modal.getOrCreateInstance(vatDiscountModal).show();
                }
            }
            if (reportUrl.searchParams.has('register_period') && reportUrl.searchParams.has('register_value')) {
                const registerModal = document.getElementById('registerClosingReportModal');
                if (registerModal && window.bootstrap) {
                    bootstrap.Modal.getOrCreateInstance(registerModal).show();
                }
            }
            if (reportUrl.searchParams.has('account_period') && reportUrl.searchParams.has('account_value')) {
                const accountActivityModal = document.getElementById('cashierModal');
                if (accountActivityModal && window.bootstrap) {
                    bootstrap.Modal.getOrCreateInstance(accountActivityModal).show();
                }
            }
            // Activity Logs (Task 42): re-open the modal after filter/pagination navigation
            if (reportUrl.searchParams.has('alog_page')
                || reportUrl.searchParams.has('alog_user')
                || reportUrl.searchParams.has('alog_action')
                || reportUrl.searchParams.has('alog_module')
                || reportUrl.searchParams.has('alog_from')
                || reportUrl.searchParams.has('alog_to')) {
                const activityLogsModal = document.getElementById('activityLogsModal');
                if (activityLogsModal && window.bootstrap) {
                    bootstrap.Modal.getOrCreateInstance(activityLogsModal).show();
                }
            }

            // Initialize export tables when modals are shown
            $('#registerClosingReportModal, #salesDetailModal, #transactionModal, #monthlySalesModal, #topProductsModal, #top5ProductsModal, #cashierModal, #expiredProductsModal, #dailySalesModal, #vatDiscountModal').on('shown.bs.modal', function () {
                $(this).find('.myTableExport').each(function () {
                    if (this.dataset.empty === '1') {
                        return;
                    }
                    if (!$.fn.DataTable.isDataTable(this)) {
                        $(this).DataTable({
                            responsive: this.dataset.noResponsive !== '1',
                            scrollX: this.dataset.noResponsive === '1',
                            autoWidth: false,
                            dom: 'fBrtip',
                            buttons: ['copy', 'excel', 'pdf', 'print']
                        });
                    }
                });
            });
        });
    </script>

    <!-- PDF SCRIPT -->
    <script src="../js/reports.js?v=2">
    </script>

    <!-- Bootstrap JS for Modal -->
    <!-- bootstrap.bundle already loaded once via conn/connection_links.php (duplicate removed) -->
