<?php
// UPDATE_ID: 11:01:45
namespace Classes;

class DashboardManager
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getTotalSalesToday()
    {
        $sql = "SELECT SUM(total_amount) as total FROM transactions WHERE DATE(created_at) = CURDATE()";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getTotalRefundToday()
    {
        $sql = "SELECT COALESCE(SUM(refund_amount), 0) AS total
            FROM return_transactions rt
            JOIN transactions t ON t.id = rt.original_transaction_id
            WHERE DATE(t.created_at) = CURDATE()";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getTotalSalesMonth()
    {
        $sql = "SELECT SUM(total_amount) as total FROM transactions WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getTotalSalesYear()
    {
        $sql = "SELECT SUM(total_amount) as total FROM transactions WHERE YEAR(created_at) = YEAR(CURDATE())";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getTransactionCountToday()
    {
        $sql = "SELECT COUNT(*) as total FROM transactions WHERE DATE(created_at) = CURDATE()";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getTotalProducts()
    {
        $sql = "SELECT COUNT(*) as total FROM products";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getLowStockAlerts()
    {
        // Join with inventory to get actual stock and reorder level
        $sql = "SELECT p.generic_name as name, p.branded_name, p.imageproduct, i.current_quantity as stock_quantity, 15 as reorder_level, pc.category_name 
                FROM products p 
                JOIN inventory i ON p.id = i.product_id 
                LEFT JOIN product_categories pc ON p.category_id = pc.id 
                WHERE i.current_quantity <= 15 
                ORDER BY i.current_quantity ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getExpiringProducts()
    {
        // Join with inventory to get expiry_date
        $sql = "SELECT p.generic_name as name, p.branded_name, p.imageproduct, i.expiry_date, pc.category_name 
                FROM products p 
                JOIN inventory i ON p.id = i.product_id 
                LEFT JOIN product_categories pc ON p.category_id = pc.id 
                WHERE i.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) 
                ORDER BY i.expiry_date ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getRecentTransactions($limit = 5)
    {
        $sql = "SELECT t.*, u.username 
                FROM transactions t 
                LEFT JOIN users u ON t.user_id = u.id 
                ORDER BY t.created_at DESC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTopSellingProducts($limit = 5)
    {
        $sql = "SELECT p.generic_name as name, p.branded_name, p.imageproduct, SUM(ti.quantity) as total_sold, ti.price 
                FROM transaction_items ti 
                JOIN products p ON ti.product_id = p.id 
                GROUP BY ti.product_id, p.generic_name, p.branded_name, p.imageproduct, ti.price 
                ORDER BY total_sold DESC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getMonthlySalesTrend()
    {
        $salesTrend = array_fill(1, 12, 0); // Fill with 0s for Jan-Dec
        $sql = "SELECT MONTH(created_at) as month, SUM(total_amount) as total 
                FROM transactions 
                WHERE YEAR(created_at) = YEAR(CURDATE()) 
                GROUP BY MONTH(created_at)";
        $stmt = $this->db->query($sql);
        while ($row = $stmt->fetch()) {
            $salesTrend[(int)$row['month']] = (float)$row['total'];
        }
        return array_values($salesTrend); // Return as index 0-11 for JS
    }

    public function getTotalDiscountToday()
    {
        $sql = "SELECT SUM(discount_total) as total FROM transactions WHERE DATE(created_at) = CURDATE()";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getTotalDiscountMonth()
    {
        $sql = "SELECT SUM(discount_total) as total FROM transactions WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getTotalVatExemptionToday()
    {
        $sql = "SELECT SUM(total_vat_exemption) as total FROM transactions WHERE DATE(created_at) = CURDATE()";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getTotalVatExemptionMonth()
    {
        $sql = "SELECT SUM(total_vat_exemption) as total FROM transactions WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getRealRevenueToday()
    {
        return $this->getRealRevenueForPeriod('DATE(t.created_at) = CURDATE()');
    }

    public function getRealRevenueMonth()
    {
        return $this->getRealRevenueForPeriod('MONTH(t.created_at) = MONTH(CURDATE()) AND YEAR(t.created_at) = YEAR(CURDATE())');
    }

    public function getRealRevenueYear()
    {
        return $this->getRealRevenueForPeriod('YEAR(t.created_at) = YEAR(CURDATE())');
    }

    private function getRealRevenueForPeriod(string $where): float
    {
        $sql = "SELECT COALESCE(SUM(
                    CASE WHEN COALESCE((SELECT SUM(rt.refund_amount)
                                        FROM return_transactions rt
                                        WHERE rt.original_transaction_id = t.id), 0) >= t.total_amount THEN 0
                    ELSE t.total_amount
                        - COALESCE((SELECT SUM(rt.refund_amount)
                                    FROM return_transactions rt
                                    WHERE rt.original_transaction_id = t.id), 0)
                        + COALESCE((SELECT SUM(CASE WHEN ri.restocked = 1 THEN
                                    (ri.quantity / NULLIF(ti_return.quantity, 0)) *
                                    COALESCE((SELECT SUM(tib_return.quantity * tib_return.purchase_cost)
                                              FROM transaction_item_batches tib_return
                                              WHERE tib_return.transaction_item_id = ti_return.id), 0)
                                ELSE 0 END)
                            FROM return_transactions rt_return
                            JOIN return_items ri ON ri.return_transaction_id = rt_return.id
                            JOIN transaction_items ti_return
                              ON ti_return.transaction_id = rt_return.original_transaction_id
                             AND ti_return.product_id = ri.product_id
                            WHERE rt_return.original_transaction_id = t.id), 0)
                        - COALESCE((SELECT SUM(tib.quantity * tib.purchase_cost)
                            FROM transaction_items ti2
                            JOIN transaction_item_batches tib ON tib.transaction_item_id = ti2.id
                            WHERE ti2.transaction_id = t.id), 0)
                    END), 0) AS real_revenue
                FROM transactions t WHERE {$where}";
        $stmt = $this->db->query($sql);
        return (float)($stmt->fetch()['real_revenue'] ?? 0);
    }

    public function getTotalTransactionsAllTime()
    {
        $sql = "SELECT COUNT(*) as total FROM transactions";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }

    public function getAverageTransactionValue()
    {
        $sql = "SELECT AVG(total_amount) as avg_value FROM transactions";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return (float)($result['avg_value'] ?? 0);
    }

    public function getTotalDiscountAllTime()
    {
        $sql = "SELECT COALESCE(SUM(discount_total), 0) as total_discount FROM transactions";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return (float)($result['total_discount'] ?? 0);
    }

    public function getTotalVatExemptionAllTime()
    {
        $sql = "SELECT COALESCE(SUM(total_vat_exemption), 0) as total_vat FROM transactions";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return (float)($result['total_vat'] ?? 0);
    }
}


