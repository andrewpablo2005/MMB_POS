<?php
require_once __DIR__ . '/guard.php'; guard_require_roles(['owner','admin','staff']);
// UPDATE_ID: 11:01:45
require_once "../conn/database.php";
require_once "../function/dashboard.php";

use Classes\DashboardManager;

$dashboardManager = new DashboardManager($db);

$totalSalesToday   = $dashboardManager->getTotalSalesToday();
$totalRefundToday  = $dashboardManager->getTotalRefundToday();
$netSalesToday     = (float)$totalSalesToday - (float)$totalRefundToday;
$totalSalesMonth   = $dashboardManager->getTotalSalesMonth();
$totalRefundMonth  = $dashboardManager->getTotalRefundMonth();
$netSalesMonth     = (float)$totalSalesMonth - (float)$totalRefundMonth;
$totalSalesYear    = $dashboardManager->getTotalSalesYear();
$totalRefundYear   = $dashboardManager->getTotalRefundYear();
$netSalesYear      = (float)$totalSalesYear - (float)$totalRefundYear;
$realRevenueToday  = $dashboardManager->getRealRevenueToday();
$realRevenueMonth  = $dashboardManager->getRealRevenueMonth();
$realRevenueYear   = $dashboardManager->getRealRevenueYear();
$transactionsToday = $dashboardManager->getTransactionCountToday();
$transactionsMonth = $dashboardManager->getTransactionCountMonth();
$transactionsYear  = $dashboardManager->getTransactionCountYear();
$totalProducts     = $dashboardManager->getTotalProducts();
$totalDiscountToday = $dashboardManager->getTotalDiscountToday();
$totalDiscountMonth = $dashboardManager->getTotalDiscountMonth();
$totalVatExemptionToday = $dashboardManager->getTotalVatExemptionToday();
$totalVatExemptionMonth = $dashboardManager->getTotalVatExemptionMonth();
$totalTransactionsAllTime = $dashboardManager->getTotalTransactionsAllTime();
$averageTransactionValue = $dashboardManager->getAverageTransactionValue();

$recentTransactions  = $dashboardManager->getRecentTransactions(5);
$topProducts         = $dashboardManager->getTopSellingProducts(5);
$salesTrend = $dashboardManager->getSalesTrend(
  (string) ($_GET['chart_period'] ?? 'year'),
  (string) ($_GET['chart_value'] ?? date('Y'))
);
$chartPeriodLabel = $salesTrend['period'] === 'date'
  ? date('M j, Y', strtotime($salesTrend['value']))
  : ($salesTrend['period'] === 'month'
    ? date('F Y', strtotime($salesTrend['value'] . '-01'))
    : $salesTrend['value']);
$chartInputType = $salesTrend['period'] === 'date' ? 'date' : ($salesTrend['period'] === 'month' ? 'month' : 'number');

date_default_timezone_set('Asia/Manila');

/* Product image helper — thumbnail markup with icon fallback (no inline JS) */
function dash_product_thumb(?string $image, string $sizeClass = 'mmb-thumb'): string
{
    $image = trim((string)$image);
    if ($image !== '') {
        return '<span class="' . $sizeClass . '"><img src="../img/' . htmlspecialchars($image, ENT_QUOTES, 'UTF-8') . '" alt="" loading="lazy"></span>';
    }
    return '<span class="' . $sizeClass . ' mmb-thumb--empty"><i class="fas fa-capsules"></i></span>';
}
?>

<!-- Inter Font & Dashboard CSS -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/dashboard.css?v=13">

<!-- Pass PHP data to dashboard.js without mixing PHP into the JS file -->
<script>
  window.dashboardData = {
    salesTrend: <?php echo json_encode($salesTrend); ?>,
    chartDefaults: {
      date: <?php echo json_encode(date('Y-m-d')); ?>,
      month: <?php echo json_encode(date('Y-m')); ?>,
      year: <?php echo json_encode(date('Y')); ?>
    },
    periods: {
      today: {
        sales:   <?php echo json_encode('₱' . number_format($netSalesToday, 2)); ?>,
        sub:     <?php echo json_encode("Returns from today's sales: -₱" . number_format($totalRefundToday, 2)); ?>,
        revenue: <?php echo json_encode('₱' . number_format($realRevenueToday, 2)); ?>,
        revSub:  'After refunds & product costs',
        transactions: <?php echo json_encode(number_format((float)$transactionsToday)); ?>,
        transactionsLabel: 'Transactions Todasy'
      },
      month: {
        sales:   <?php echo json_encode('₱' . number_format($netSalesMonth, 2)); ?>,
        sub:     <?php echo json_encode(date('F Y') . ' · net'); ?>,
        revenue: <?php echo json_encode('₱' . number_format($realRevenueMonth, 2)); ?>,
        revSub:  'After refunds & product costs',
        transactions: <?php echo json_encode(number_format((float)$transactionsMonth)); ?>,
        transactionsLabel: 'Transactions This Month'
      },
      year: {
        sales:   <?php echo json_encode('₱' . number_format($netSalesYear, 2)); ?>,
        sub:     <?php echo json_encode(date('Y') . ' · net'); ?>,
        revenue: <?php echo json_encode('₱' . number_format($realRevenueYear, 2)); ?>,
        revSub:  'After refunds & product costs',
        transactions: <?php echo json_encode(number_format((float)$transactionsYear)); ?>,
        transactionsLabel: 'Transactions This Year'
      }
    }
  };
</script>

<div class="dash-wrapper">

  <!-- Header -->
  <div class="dash-header">
    <h4>Business Overview</h4>
    <span class="date-badge"><?php echo date('l, F j, Y'); ?></span>
  </div>

  <!-- ── KPI Row: one flagship Sales card with a period toggle ── -->
  <div class="row g-3 mb-3">
    <div class="col-12 col-sm-6 col-xl-4">
      <div class="stat-card stat-card--sales">
        <div class="stat-icon crimson"><i class="fas fa-peso-sign"></i></div>
        <div class="flex-grow-1 min-w-0">
          <div class="d-flex align-items-center justify-content-between gap-2">
            <div class="stat-label">Net Sales</div>
            <div class="custom-period-picker" id="salesPeriodPicker">
              <button type="button" class="period-dropdown-toggle" id="salesPeriodToggle" aria-expanded="false" aria-haspopup="listbox" aria-label="Sales period">
                <span class="period-dropdown-label">Today</span>
                <span class="period-dropdown-caret" aria-hidden="true">▾</span>
              </button>
              <div class="period-dropdown-menu" role="listbox" aria-label="Sales period">
                <button type="button" class="period-dropdown-item is-selected" data-value="today" role="option" aria-selected="true">Today</button>
                <button type="button" class="period-dropdown-item" data-value="month" role="option" aria-selected="false">This Month</button>
                <button type="button" class="period-dropdown-item" data-value="year" role="option" aria-selected="false">This Year</button>
              </div>
            </div>
          </div>
          <div class="stat-value stat-value--xl" id="salesValue">₱<?php echo number_format($netSalesToday, 2); ?></div>
          <div class="stat-sub" id="salesSub">Returns from today's sales: -₱<?php echo number_format($totalRefundToday, 2); ?></div>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-4">
      <div class="stat-card">
        <div class="stat-icon wine"><i class="fas fa-money-bill-trend-up"></i></div>
        <div class="min-w-0">
          <div class="stat-label">Profit</div>
          <div class="stat-value" id="revenueValue">₱<?php echo number_format($realRevenueToday, 2); ?></div>
          <div class="stat-sub" id="revenueSub">After refunds & product costs</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-4">
      <div class="stat-card">
        <div class="stat-icon rose"><i class="fas fa-receipt"></i></div>
        <div class="min-w-0">
          <div class="stat-label" id="transactionsLabel">Transactions Today</div>
          <div class="stat-value" id="transactionsValue"><?php echo number_format((float)$transactionsToday); ?></div>
          <div class="stat-sub">Avg basket ₱<?php echo number_format((float)$averageTransactionValue, 2); ?> · <?php echo number_format((float)$totalTransactionsAllTime); ?> all-time</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── Chart + right rail ── -->
  <div class="row g-3 mb-3">
    <div class="col-12 col-xl-8">
      <div class="dash-card">
        <div class="dash-card-header">
          <h6>Sales Performance</h6>
          <div class="d-flex align-items-center gap-2">
            <span class="pill pill-gray" id="chartTotal"><?= htmlspecialchars($chartPeriodLabel, ENT_QUOTES, 'UTF-8') ?> · ₱<?= number_format((float) $salesTrend['total'], 2) ?></span>
            <form method="GET" action="dashboard.php" class="dashboard-chart-filters d-flex align-items-center gap-2">
              <input type="hidden" name="tab" value="dashboard">
              <label class="visually-hidden" for="chartPeriod">Sales chart period</label>
              <select class="form-select form-select-sm" id="chartPeriod" name="chart_period">
                <option value="date" <?= $salesTrend['period'] === 'date' ? 'selected' : '' ?>>Day</option>
                <option value="month" <?= $salesTrend['period'] === 'month' ? 'selected' : '' ?>>Month</option>
                <option value="year" <?= $salesTrend['period'] === 'year' ? 'selected' : '' ?>>Year</option>
              </select>
              <label class="visually-hidden" for="chartValue">Choose chart date, month, or year</label>
              <input class="form-control form-control-sm" id="chartValue" name="chart_value" type="<?= $chartInputType ?>" value="<?= htmlspecialchars($salesTrend['value'], ENT_QUOTES, 'UTF-8') ?>" <?= $salesTrend['period'] === 'year' ? 'min="2000" max="2100"' : '' ?> required>
            </form>
          </div>
        </div>
        <div class="dash-card-body">
          <div class="chart-wrapper">
            <canvas id="salesChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Right rail: catalog + discounts -->
    <div class="col-12 col-xl-4 d-flex flex-column gap-3">
      <div class="stat-card">
        <div class="stat-icon slate"><i class="fas fa-boxes-stacked"></i></div>
        <div class="min-w-0">
          <div class="stat-label">Catalog</div>
          <div class="stat-value"><?php echo number_format((float)$totalProducts); ?> products</div>
        </div>
      </div>
      <div class="dash-card dash-card--discounts">
        <div class="dash-card-header">
          <h6>Discounts & VAT</h6>
          <a class="btn btn-sm btn-outline-primary" href="dashboard.php?tab=reports&amp;vat_period=date&amp;vat_value=<?= htmlspecialchars(date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>#v-pills-reports">See all</a>
        </div>
        <div class="dash-card-body py-3">
          <div class="mini-stat-row">
            <span>Discounts today</span>
            <strong>₱<?php echo number_format((float)$totalDiscountToday, 2); ?></strong>
          </div>
          <div class="mini-stat-row">
            <span>Discounts this month</span>
            <strong>₱<?php echo number_format((float)$totalDiscountMonth, 2); ?></strong>
          </div>
          <div class="mini-stat-row">
            <span>VAT exempt today</span>
            <strong>₱<?php echo number_format((float)$totalVatExemptionToday, 2); ?></strong>
          </div>
          <div class="mini-stat-row">
            <span>VAT exempt this month</span>
            <strong>₱<?php echo number_format((float)$totalVatExemptionMonth, 2); ?></strong>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── Transactions & Top Products ── -->
  <div class="row g-3 mb-3">
    <!-- Recent Transactions -->
    <div class="col-12 col-xl-7">
      <div class="dash-card">
        <div class="dash-card-header">
          <h6>Recent Transactions</h6>
          <a href="dashboard.php?tab=reports&amp;detail_period=date&amp;detail_value=<?= htmlspecialchars(date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>#v-pills-reports" class="pill pill-gray text-decoration-none">View All</a>
        </div>
        <div class="dash-card-body">
          <?php if (empty($recentTransactions)): ?>
            <div class="empty-state"><i class="fas fa-inbox"></i>No transactions yet</div>
          <?php else: ?>
            <table class="dash-table">
              <thead>
                <tr>
                  <th>Ref ID</th>
                  <th>Date / Time</th>
                  <th>Cashier</th>
                  <th>Customer</th>
                  <th class="text-end">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recentTransactions as $tx): ?>
                  <tr>
                    <td class="ref-id">#<?php echo str_pad($tx['id'], 6, '0', STR_PAD_LEFT); ?></td>
                    <td><?php echo date('M d, H:i', strtotime($tx['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($tx['username'] ?? '—'); ?></td>
                    <td><?php echo htmlspecialchars($tx['customer_name'] ?? 'Guest'); ?></td>
                    <td class="text-end amount-pos"><strong>₱<?php echo number_format($tx['total_amount'], 2); ?></strong></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Top Selling Products -->
    <div class="col-12 col-xl-5">
      <div class="dash-card">
        <div class="dash-card-header">
          <h6>Top Selling Products</h6>
          <?php if (in_array(strtolower(trim((string)($_SESSION['position'] ?? ''))), ['owner', 'admin'], true)): ?>
            <a class="btn btn-sm btn-outline-primary" href="?tab=reports&amp;open_top_products=1">See All</a>
          <?php endif; ?>
        </div>
        <div class="dash-card-body">
          <?php if (empty($topProducts)): ?>
            <div class="empty-state"><i class="fas fa-box-open"></i>No sales data yet</div>
          <?php else: ?>
            <?php foreach ($topProducts as $i => $product): ?>
              <div class="top-product-item">
                <div class="d-flex align-items-center min-w-0">
                  <div class="product-rank"><?php echo $i + 1; ?></div>
                  <?php echo dash_product_thumb($product['imageproduct'] ?? null); ?>
                  <div class="min-w-0">
                    <div class="product-name text-truncate"><?php echo htmlspecialchars(trim(($product['branded_name'] ?? '') !== '' ? $product['branded_name'] : ($product['name'] ?? ''))); ?></div>
                    <div class="product-sold"><?php echo $product['total_sold']; ?> units sold</div>
                  </div>
                </div>
                <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

</div><!-- end .dash-wrapper -->

<!-- Dashboard Chart -->
<script src="../js/dashboard.js?v=11"></script>