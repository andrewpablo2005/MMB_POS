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
$totalDiscountAllTime = $dashboardManager->getTotalDiscountAllTime();
$totalVatExemptionAllTime = $dashboardManager->getTotalVatExemptionAllTime();

$recentTransactions  = $dashboardManager->getRecentTransactions(5);
$topProducts         = $dashboardManager->getTopSellingProducts(5);
$lowStockItems       = $dashboardManager->getLowStockAlerts();
$expiringItems       = $dashboardManager->getExpiringProducts();
$monthlySalesTrend   = $dashboardManager->getMonthlySalesTrend();

date_default_timezone_set('Asia/Manila');
?>

<!-- Inter Font & Dashboard CSS -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/dashboard.css?v=3">

<!-- Pass PHP data to dashboard.js without mixing PHP into the JS file -->
<script>
  window.dashboardData = {
    monthlySalesTrend: <?php echo json_encode($monthlySalesTrend); ?>
  };
</script>

<div class="dash-wrapper">

  <!-- Header -->
  <div class="dash-header">
    <h4>Business Overview</h4>
    <span class="date-badge"><?php echo date('F j, Y'); ?></span>
  </div>

  <!-- ── Sales Summary ── -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
      <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-peso-sign"></i></div>
        <div>
          <div class="stat-label">Today's Sales</div>
          <div class="stat-value">₱<?php echo number_format($netSalesToday, 2); ?></div>
          <div class="stat-sub text-danger">Refunds: -₱<?php echo number_format($totalRefundToday, 2); ?></div>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-chart-line"></i></div>
        <div>
          <div class="stat-label">Monthly Sales</div>
          <div class="stat-value">₱<?php echo number_format($totalSalesMonth, 2); ?></div>
          <div class="stat-sub"><?php echo date('F Y'); ?></div>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card">
        <div class="stat-icon lime"><i class="fas fa-chart-bar"></i></div>
        <div>
          <div class="stat-label">Yearly Sales</div>
          <div class="stat-value">₱<?php echo number_format($totalSalesYear, 2); ?></div>
          <div class="stat-sub"><?php echo date('Y'); ?></div>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card">
        <div class="stat-icon teal"><i class="fas fa-money-bill-trend-up"></i></div>
        <div>
          <div class="stat-label">Real Revenue Today</div>
          <div class="stat-value">₱<?php echo number_format($realRevenueToday, 2); ?></div>
          <div class="stat-sub"><?php echo date('F j, Y'); ?></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── Chart ── -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="dash-card">
        <div class="dash-card-header">
          <h6><i class="fas fa-bar-chart me-2 text-primary"></i>Monthly Sales Performance</h6>
          <span class="pill pill-green"><?php echo date('Y'); ?></span>
        </div>
        <div class="dash-card-body">
          <div class="chart-wrapper">
            <canvas id="salesChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── Transactions & Top Products ── -->
  <div class="row g-3 mb-4">
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
                  <th>Customer</th>
                  <th>Discount</th>
                  <th>VAT Exempt</th>
                  <th>Total Amount</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recentTransactions as $tx): ?>
                  <tr>
                    <td class="ref-id">#<?php echo str_pad($tx['id'], 6, '0', STR_PAD_LEFT); ?></td>
                    <td><?php echo date('M d, H:i', strtotime($tx['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($tx['customer_name'] ?? 'Guest'); ?></td>
                    <td class="amount-neg">₱<?php echo number_format($tx['discount_total'] ?? 0, 2); ?></td>
                    <td class="amount-neg">₱<?php echo number_format($tx['total_vat_exemption'] ?? 0, 2); ?></td>
                    <td class="amount-pos"><strong>₱<?php echo number_format($tx['total_amount'], 2); ?></strong></td>
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
          <h6><i class="fas fa-trophy me-2 text-warning"></i>Top Selling Products</h6>
        </div>
        <div class="dash-card-body">
          <?php if (empty($topProducts)): ?>
            <div class="empty-state"><i class="fas fa-box-open"></i>No sales data yet</div>
          <?php else: ?>
            <?php foreach ($topProducts as $i => $product): ?>
              <div class="top-product-item">
                <div class="d-flex align-items-center">
                  <div class="product-rank"><?php echo $i + 1; ?></div>
                  <div>
                    <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
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
    <div class="col-12 col-md-6">
      <div class="dash-card alert-card-warn">
        <div class="dash-card-header">
          <h6><i class="fas fa-triangle-exclamation me-2"></i>Low Stock Alerts</h6>
          <?php if (!empty($lowStockItems)): ?>
            <span class="badge-warn"><?php echo count($lowStockItems); ?> items</span>
          <?php endif; ?>
        </div>
        <div class="dash-card-body">
          <?php if (empty($lowStockItems)): ?>
            <div class="empty-state"><i class="fas fa-circle-check" style="color:#16a34a;opacity:1"></i>All inventory looks good!</div>
          <?php else: ?>
            <table class="dash-table">
              <thead><tr><th>Product</th><th>Stock</th><th>Reorder At</th></tr></thead>
              <tbody>
                <?php foreach ($lowStockItems as $item): ?>
                  <tr>
                    <td class="fw-semibold"><?php echo htmlspecialchars($item['name']); ?></td>
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
    <div class="col-12 col-md-6">
      <div class="dash-card alert-card-danger">
        <div class="dash-card-header">
          <h6><i class="fas fa-calendar-xmark me-2"></i>Expiring Soon (30 Days)</h6>
          <?php if (!empty($expiringItems)): ?>
            <span class="badge-danger"><?php echo count($expiringItems); ?> items</span>
          <?php endif; ?>
        </div>
        <div class="dash-card-body">
          <?php if (empty($expiringItems)): ?>
            <div class="empty-state"><i class="fas fa-circle-check" style="color:#16a34a;opacity:1"></i>No immediate expiries.</div>
          <?php else: ?>
            <table class="dash-table">
              <thead><tr><th>Product</th><th>Expiry Date</th><th>Status</th></tr></thead>
              <tbody>
                <?php foreach ($expiringItems as $item): ?>
                  <?php $days = (strtotime($item['expiry_date']) - time()) / 86400; ?>
                  <tr>
                    <td class="fw-semibold"><?php echo htmlspecialchars($item['name']); ?></td>
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
<script src="../js/dashboard.js"></script>