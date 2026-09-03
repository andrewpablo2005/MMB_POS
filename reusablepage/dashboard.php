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
$totalSalesYear    = $dashboardManager->getTotalSalesYear();
$realRevenueToday  = $dashboardManager->getRealRevenueToday();
$realRevenueMonth  = $dashboardManager->getRealRevenueMonth();
$realRevenueYear   = $dashboardManager->getRealRevenueYear();
$transactionsToday = $dashboardManager->getTransactionCountToday();
$totalProducts     = $dashboardManager->getTotalProducts();
$totalDiscountToday = $dashboardManager->getTotalDiscountToday();
$totalDiscountMonth = $dashboardManager->getTotalDiscountMonth();
$totalVatExemptionToday = $dashboardManager->getTotalVatExemptionToday();
$totalVatExemptionMonth = $dashboardManager->getTotalVatExemptionMonth();
$totalTransactionsAllTime = $dashboardManager->getTotalTransactionsAllTime();
$averageTransactionValue = $dashboardManager->getAverageTransactionValue();

$recentTransactions  = $dashboardManager->getRecentTransactions(5);
$topProducts         = $dashboardManager->getTopSellingProducts(5);
$lowStockItems       = $dashboardManager->getLowStockAlerts();
$expiringItems       = $dashboardManager->getExpiringProducts();
$monthlySalesTrend   = $dashboardManager->getMonthlySalesTrend();

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
<link rel="stylesheet" href="../css/dashboard.css?v=10">

<!-- Pass PHP data to dashboard.js without mixing PHP into the JS file -->
<script>
  window.dashboardData = {
    monthlySalesTrend: <?php echo json_encode($monthlySalesTrend); ?>,
    periods: {
      today: {
        sales:   <?php echo json_encode('₱' . number_format($netSalesToday, 2)); ?>,
        sub:     <?php echo json_encode('Refunds today: -₱' . number_format($totalRefundToday, 2)); ?>,
        revenue: <?php echo json_encode('₱' . number_format($realRevenueToday, 2)); ?>,
        revSub:  'Net of refunds & restock costs — today'
      },
      month: {
        sales:   <?php echo json_encode('₱' . number_format($totalSalesMonth, 2)); ?>,
        sub:     <?php echo json_encode(date('F Y') . ' · gross'); ?>,
        revenue: <?php echo json_encode('₱' . number_format($realRevenueMonth, 2)); ?>,
        revSub:  <?php echo json_encode(date('F Y')); ?>
      },
      year: {
        sales:   <?php echo json_encode('₱' . number_format($totalSalesYear, 2)); ?>,
        sub:     <?php echo json_encode(date('Y') . ' · gross'); ?>,
        revenue: <?php echo json_encode('₱' . number_format($realRevenueYear, 2)); ?>,
        revSub:  <?php echo json_encode(date('Y')); ?>
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
          <div class="d-flex align-items-center justify-content-between">
            <div class="stat-label">Sales</div>
            <select class="period-select" id="salesPeriod" aria-label="Sales period">
              <option value="today" selected>Today</option>
              <option value="month">This Month</option>
              <option value="year">This Year</option>
            </select>
          </div>
          <div class="stat-value stat-value--xl" id="salesValue">₱<?php echo number_format($netSalesToday, 2); ?></div>
          <div class="stat-sub" id="salesSub">Refunds today: -₱<?php echo number_format($totalRefundToday, 2); ?></div>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-4">
      <div class="stat-card">
        <div class="stat-icon wine"><i class="fas fa-money-bill-trend-up"></i></div>
        <div class="min-w-0">
          <div class="stat-label">Real Revenue</div>
          <div class="stat-value" id="revenueValue">₱<?php echo number_format($realRevenueToday, 2); ?></div>
          <div class="stat-sub" id="revenueSub">Net of refunds & restock costs — today</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-4">
      <div class="stat-card">
        <div class="stat-icon rose"><i class="fas fa-receipt"></i></div>
        <div class="min-w-0">
          <div class="stat-label">Transactions Today</div>
          <div class="stat-value"><?php echo number_format((float)$transactionsToday); ?></div>
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
          <h6><i class="fas fa-chart-column me-2 text-primary"></i>Sales Performance</h6>
          <div class="d-flex align-items-center gap-2">
            <span class="pill pill-gray" id="chartTotal">YTD ₱<?php echo number_format(array_sum($monthlySalesTrend), 2); ?></span>
            <select class="period-select period-select--sm" id="chartRange" aria-label="Chart range">
              <option value="6">Last 6 months</option>
              <option value="12" selected>Last 12 months</option>
            </select>
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
          <div class="stat-sub"><?php echo count($lowStockItems); ?> low stock · <?php echo count($expiringItems); ?> expiring in 30d</div>
        </div>
      </div>
      <div class="dash-card flex-grow-1">
        <div class="dash-card-header">
          <h6><i class="fas fa-tags me-2 text-primary"></i>Discounts & VAT</h6>
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
          <h6><i class="fas fa-clock-rotate-left me-2"></i>Recent Transactions</h6>
          <a href="#" class="pill pill-gray text-decoration-none">View All</a>
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
          <h6><i class="fas fa-trophy me-2 text-primary"></i>Top Selling Products</h6>
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

  <!-- ── Alert Widgets ── -->
  <div class="row g-3">
    <!-- Low Stock -->
    <div class="col-12 col-xl-6">
      <div class="dash-card alert-card-warn">
        <div class="dash-card-header">
          <h6><i class="fas fa-triangle-exclamation me-2"></i>Low Stock Alerts</h6>
          <?php if (!empty($lowStockItems)): ?>
            <span class="badge-warn"><?php echo count($lowStockItems); ?> items</span>
          <?php endif; ?>
        </div>
        <div class="dash-card-body">
          <?php if (empty($lowStockItems)): ?>
            <div class="empty-state"><i class="fas fa-circle-check" style="color:#dc2626;opacity:1"></i>All inventory looks good!</div>
          <?php else: ?>
            <table class="dash-table">
              <thead><tr><th></th><th>Product</th><th>Stock</th><th>Reorder At</th></tr></thead>
              <tbody>
                <?php foreach ($lowStockItems as $item): ?>
                  <tr>
                    <td class="pt-2 pb-2"><?php echo dash_product_thumb($item['imageproduct'] ?? null); ?></td>
                    <td class="fw-semibold"><?php echo htmlspecialchars(trim(($item['branded_name'] ?? '') !== '' ? $item['branded_name'] : $item['name'])); ?></td>
                    <td><span class="badge-warn"><?php echo $item['stock_quantity']; ?></span></td>
                    <td style="color:#94a3b8;font-size:.8rem"><?php echo $item['reorder_level']; ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Expiring Soon -->
    <div class="col-12 col-xl-6">
      <div class="dash-card alert-card-danger">
        <div class="dash-card-header">
          <h6><i class="fas fa-calendar-xmark me-2"></i>Expiring Soon (30 Days)</h6>
          <?php if (!empty($expiringItems)): ?>
            <span class="badge-danger"><?php echo count($expiringItems); ?> items</span>
          <?php endif; ?>
        </div>
        <div class="dash-card-body">
          <?php if (empty($expiringItems)): ?>
            <div class="empty-state"><i class="fas fa-circle-check" style="color:#dc2626;opacity:1"></i>No immediate expiries.</div>
          <?php else: ?>
            <table class="dash-table">
              <thead><tr><th></th><th>Product</th><th>Expiry Date</th><th>Status</th></tr></thead>
              <tbody>
                <?php foreach ($expiringItems as $item): ?>
                  <?php $days = (strtotime($item['expiry_date']) - time()) / 86400; ?>
                  <tr>
                    <td class="pt-2 pb-2"><?php echo dash_product_thumb($item['imageproduct'] ?? null); ?></td>
                    <td class="fw-semibold"><?php echo htmlspecialchars(trim(($item['branded_name'] ?? '') !== '' ? $item['branded_name'] : $item['name'])); ?></td>
                    <td style="font-size:.82rem"><?php echo date('M d, Y', strtotime($item['expiry_date'])); ?></td>
                    <td>
                      <?php if ($days <= 10): ?>
                        <span class="badge-danger">Critical</span>
                      <?php else: ?>
                        <span class="badge-warn">Warning</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

</div><!-- end .dash-wrapper -->

<!-- Dashboard Chart -->
<script src="../js/dashboard.js?v=8"></script>