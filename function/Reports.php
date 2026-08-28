<?php
namespace Classes;

use PDO;

class Reports
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /* =========================
       GET ALL REPORT DATA
    ========================= */
    public function getAllReports(): array
    {
        return [
            'sales' => $this->getSalesSummary(),
            'topProducts' => $this->getTopProducts(),
            'inventory' => $this->getInventory(),
            'discounts' => $this->getDiscountUsage(),
            'cashiers' => $this->getCashierPerformance(),
            'dailySales' => $this->getDailySales(),
            'yearlySales' => $this->getYearlySales(),
            'discountBreakdown' => $this->getDiscountBreakdown(),
            'transactions' => $this->getAllTransactions(),
            'totalDiscounts' => $this->getTotalDiscounts(),
            'totalVatExemption' => $this->getTotalVatExemption(),
            'realRevenueToday' => $this->getRealRevenueToday(),
            'realRevenueMonth' => $this->getRealRevenueMonth(),
            'realRevenueYear' => $this->getRealRevenueYear(),
            'totalSalesYear' => $this->getTotalSalesYear()
        ];
    }

    /* =========================
       SALES SUMMARY
    ========================= */
    public function getSalesSummary(): array
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) total_transactions,
                   SUM(total_amount) total_sales,
                   AVG(total_amount) avg_sale
            FROM transactions
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /* =========================
       TOP PRODUCTS
    ========================= */
    public function getTopProducts(): array
    {
        $stmt = $this->db->prepare("
            SELECT p.generic_name AS product_name, SUM(ti.quantity) total_sold
            FROM transaction_items ti
            JOIN products p ON ti.product_id = p.id
            GROUP BY p.generic_name
            ORDER BY total_sold DESC
            LIMIT 5
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       INVENTORY
    ========================= */
    public function getInventory(): array
    {
        $stmt = $this->db->prepare("
            SELECT p.id AS product_id,
                   p.branded_name,
                   p.generic_name,
                   p.strength,
                   p.dosage_form,
                   p.strength_per_quantity,
                   p.strength_per_quantity_unit,
                   i.id AS inventory_id,
                   i.supplier_id,
                   i.received_quantity,
                   i.current_quantity AS quantity,
                   i.expiry_date,
                   s.supplier_name,
                   s.contact_person,
                   s.contact_number,
                   s.email,
                   s.address,
                   s.supplier_type
            FROM inventory i
            JOIN products p ON i.product_id = p.id
            LEFT JOIN suppliers s ON i.supplier_id = s.id
            ORDER BY i.expiry_date ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       DISCOUNTS
    ========================= */
    public function getDiscountUsage(): array
    {
        $stmt = $this->db->prepare("
            SELECT d.discount_name, COUNT(t.id) used_count
            FROM transactions t
            JOIN discounts d ON t.discount_id = d.id
            GROUP BY d.discount_name
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       CASHIER PERFORMANCE
    ========================= */
    public function getCashierPerformance(): array
    {
        $stmt = $this->db->prepare("
            SELECT u.username, COUNT(t.id) total_transactions
            FROM transactions t
            JOIN users u ON t.user_id = u.id
            GROUP BY u.username
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCashierList(): array
    {
       $stmt = $this->db->prepare("
           SELECT u.id,
                  u.username,
                  COALESCE(NULLIF(CONCAT_WS(' ', ui.firstname, ui.lastname), ''), u.username) AS cashier_name
           FROM users u
           LEFT JOIN users_info ui ON ui.user_id = u.id
           ORDER BY cashier_name ASC, u.username ASC
       ");
       $stmt->execute();
       return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       DAILY SALES
    ========================= */
    public function getDailySales(): array
    {
        $stmt = $this->db->prepare("
            SELECT DATE(t.created_at) sale_date, 
                   COUNT(*) total_transactions,
                   SUM(t.total_amount) daily_total,
                   AVG(t.total_amount) daily_avg
            FROM transactions t
            GROUP BY DATE(t.created_at)
            ORDER BY t.created_at DESC
            LIMIT 30
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       YEARLY SALES
    ========================= */
    public function getYearlySales(): array
    {
        $stmt = $this->db->prepare("
            SELECT YEAR(t.created_at) sale_year,
                   MONTH(t.created_at) sale_month,
                   COUNT(*) total_transactions,
                   SUM(ti.price * ti.quantity) gross_revenue,
                   SUM(COALESCE(i.purchase_cost, 0) * ti.quantity) total_cost,
                   SUM(ti.price * ti.quantity) - SUM(COALESCE(i.purchase_cost, 0) * ti.quantity) profit,
                   SUM(t.total_amount) net_revenue,
                   AVG(t.total_amount) monthly_avg
            FROM transactions t
            LEFT JOIN transaction_items ti ON t.id = ti.transaction_id
            LEFT JOIN products p ON ti.product_id = p.id
            LEFT JOIN inventory i ON p.id = i.product_id
            GROUP BY YEAR(t.created_at), MONTH(t.created_at)
            ORDER BY t.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       DISCOUNT BREAKDOWN (PWD & SENIOR)
    ========================= */
    public function getDiscountBreakdown(): array
    {
        $stmt = $this->db->prepare("
            SELECT d.discount_name,
                   COUNT(DISTINCT t.id) usage_count,
                   SUM(t.discount_total) total_discount_given,
                   AVG(t.discount_total) avg_discount,
                   SUM(t.total_vat_exemption) total_vat_exemption,
                   AVG(t.total_vat_exemption) avg_vat_exemption
            FROM transactions t
            JOIN discounts d ON t.discount_id = d.id
            WHERE d.discount_name IN ('PWD', 'Senior Citizen', 'Senior')
            GROUP BY d.discount_name
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       ALL TRANSACTIONS
    ========================= */
    public function getAllTransactions(): array
    {
        $stmt = $this->db->prepare("
            SELECT t.id,
                   t.created_at transaction_date,
                   u.username,
                   t.customer_name,
                   t.customer_id,
                   t.customer_type,
                   CASE 
                       WHEN t.customer_type = 'senior' THEN (SELECT id_number FROM senior_customers WHERE id = t.customer_id LIMIT 1)
                       WHEN t.customer_type = 'pwd' THEN (SELECT id_number FROM pwd_customers WHERE id = t.customer_id LIMIT 1)
                       ELSE NULL
                   END as govt_id_number,
                   d.discount_name,
                   COALESCE(SUM(ti.price * ti.quantity), 0) gross_subtotal,
                   COALESCE(SUM(ti.subtotal), 0) net_subtotal,
                   COALESCE(SUM(ti.price * ti.quantity), 0) - t.total_amount discount_amount,
                   t.discount_total,
                   t.total_vat_exemption,
                   t.total_amount,
                   COUNT(DISTINCT ti.id) items_count
            FROM transactions t
            LEFT JOIN users u ON t.user_id = u.id
            LEFT JOIN discounts d ON t.discount_id = d.id
            LEFT JOIN transaction_items ti ON t.id = ti.transaction_id
            GROUP BY t.id
            ORDER BY t.created_at DESC
            LIMIT 500
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRegisterClosings(): array
    {
        $stmt = $this->db->prepare("SELECT rc.id,
                   rc.business_date,
                   rc.system_cash,
                   rc.counted_cash,
                   rc.variance,
                   rc.notes,
                   rc.closed_at,
                   COALESCE(NULLIF(CONCAT_WS(' ', ui.firstname, ui.lastname), ''), u.username) AS cashier_name
            FROM register_closings rc
            LEFT JOIN users u ON u.id = rc.user_id
            LEFT JOIN users_info ui ON ui.user_id = u.id
            ORDER BY rc.business_date DESC, rc.closed_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSalesDetailReport(string $period, string $value, ?int $cashierId = null): array
    {
        $period = in_array($period, ['date', 'month', 'year'], true) ? $period : 'date';
        $value = trim($value);
        $cashierId = (int)($cashierId ?? 0);

        if ($period === 'date' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $value = date('Y-m-d');
        } elseif ($period === 'month' && !preg_match('/^\d{4}-\d{2}$/', $value)) {
            $value = date('Y-m');
        } elseif ($period === 'year' && !preg_match('/^\d{4}$/', $value)) {
            $value = date('Y');
        }

        $where = '1 = 1';
        $parameters = [];
        if ($period === 'date') {
            $where = 'DATE(t.created_at) = ?';
            $parameters[] = $value;
        } elseif ($period === 'month') {
            $where = "DATE_FORMAT(t.created_at, '%Y-%m') = ?";
            $parameters[] = $value;
        } else {
            $where = 'YEAR(t.created_at) = ?';
            $parameters[] = (int)$value;
        }

        if ($cashierId > 0) {
            $where .= ' AND t.user_id = ?';
            $parameters[] = $cashierId;
        }

        $stmt = $this->db->prepare("SELECT t.id,
                   t.created_at transaction_date,
                   u.username,
                   COALESCE(SUM(ti.price * ti.quantity), 0) gross_subtotal,
                   COALESCE(MAX(t.discount_total), 0) discount_total,
                   COALESCE(MAX(t.total_vat_exemption), 0) total_vat_exemption,
                   t.total_amount,
                                     COALESCE((SELECT SUM(rt.refund_amount)
                                                         FROM return_transactions rt
                                                         WHERE rt.original_transaction_id = t.id), 0) refund_total,
                                     t.total_amount
                                         - COALESCE((SELECT SUM(rt.refund_amount)
                                                                 FROM return_transactions rt
                                                                 WHERE rt.original_transaction_id = t.id), 0) net_after_refund,
                                     COALESCE((SELECT SUM(
                                                                            CASE WHEN ri.restocked = 1 THEN
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
                                                         WHERE rt_return.original_transaction_id = t.id), 0) cogs_reversed,
                                     CASE WHEN COALESCE((SELECT SUM(rt_full.refund_amount)
                                                         FROM return_transactions rt_full
                                                         WHERE rt_full.original_transaction_id = t.id), 0) >= t.total_amount THEN 0
                                     ELSE t.total_amount
                                         - COALESCE((SELECT SUM(rt.refund_amount)
                                                                 FROM return_transactions rt
                                                                 WHERE rt.original_transaction_id = t.id), 0)
                                         + COALESCE((SELECT SUM(
                                                                            CASE WHEN ri.restocked = 1 THEN
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
                                                                 WHERE ti2.transaction_id = t.id), 0) END real_revenue,
                   COUNT(DISTINCT ti.id) items_count
            FROM transactions t
            LEFT JOIN users u ON t.user_id = u.id
            LEFT JOIN transaction_items ti ON t.id = ti.transaction_id
            WHERE {$where}
            GROUP BY t.id
            ORDER BY t.created_at DESC");
        $stmt->execute($parameters);
        return [
            'period' => $period,
            'value' => $value,
            'cashier_id' => $cashierId,
            'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC),
        ];
    }

    /* =========================
       TOTAL DISCOUNTS
    ========================= */
    public function getTotalDiscounts(): float
    {
        $stmt = $this->db->prepare("SELECT SUM(discount_total) as total FROM transactions");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($result['total'] ?? 0);
    }

    /* =========================
       TOTAL VAT EXEMPTION
    ========================= */
    public function getTotalVatExemption(): float
    {
        $stmt = $this->db->prepare("SELECT SUM(total_vat_exemption) as total FROM transactions");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($result['total'] ?? 0);
    }

    /* =========================
       REAL REVENUE TODAY
    ========================= */
    public function getRealRevenueToday(): float
    {
        $stmt = $this->db->prepare("
            SELECT
                COALESCE(SUM((SELECT COALESCE(SUM(ti.subtotal), 0) FROM transaction_items ti WHERE ti.transaction_id = t.id)
                    - (SELECT COALESCE(SUM(rt.refund_amount), 0) FROM return_transactions rt WHERE rt.original_transaction_id = t.id)
                    - (SELECT COALESCE(SUM(tib.quantity * tib.purchase_cost), 0)
                       FROM transaction_items ti2 JOIN transaction_item_batches tib ON tib.transaction_item_id = ti2.id
                       WHERE ti2.transaction_id = t.id)), 0) AS total_selling,
                0 AS total_cost
            FROM transactions t
            WHERE DATE(t.created_at) = CURDATE()
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $selling = (float)($result['total_selling'] ?? 0);
        $cost = (float)($result['total_cost'] ?? 0);
        return $selling - $cost;
    }

    /* =========================
       REAL REVENUE THIS MONTH
    ========================= */
    public function getRealRevenueMonth(): float
    {
        $stmt = $this->db->prepare("
            SELECT
                COALESCE(SUM((SELECT COALESCE(SUM(ti.subtotal), 0) FROM transaction_items ti WHERE ti.transaction_id = t.id)
                    - (SELECT COALESCE(SUM(rt.refund_amount), 0) FROM return_transactions rt WHERE rt.original_transaction_id = t.id)
                    - (SELECT COALESCE(SUM(tib.quantity * tib.purchase_cost), 0)
                       FROM transaction_items ti2 JOIN transaction_item_batches tib ON tib.transaction_item_id = ti2.id
                       WHERE ti2.transaction_id = t.id)), 0) AS total_selling,
                0 AS total_cost
            FROM transactions t
            WHERE MONTH(t.created_at) = MONTH(CURDATE()) 
            AND YEAR(t.created_at) = YEAR(CURDATE())
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $selling = (float)($result['total_selling'] ?? 0);
        $cost = (float)($result['total_cost'] ?? 0);
        return $selling - $cost;
    }

    /* =========================
       REAL REVENUE THIS YEAR
    ========================= */
    public function getRealRevenueYear(): float
    {
        $stmt = $this->db->prepare("
            SELECT
                COALESCE(SUM((SELECT COALESCE(SUM(ti.subtotal), 0) FROM transaction_items ti WHERE ti.transaction_id = t.id)
                    - (SELECT COALESCE(SUM(rt.refund_amount), 0) FROM return_transactions rt WHERE rt.original_transaction_id = t.id)
                    - (SELECT COALESCE(SUM(tib.quantity * tib.purchase_cost), 0)
                       FROM transaction_items ti2 JOIN transaction_item_batches tib ON tib.transaction_item_id = ti2.id
                       WHERE ti2.transaction_id = t.id)), 0) AS total_selling,
                0 AS total_cost
            FROM transactions t
            WHERE YEAR(t.created_at) = YEAR(CURDATE())
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $selling = (float)($result['total_selling'] ?? 0);
        $cost = (float)($result['total_cost'] ?? 0);
        return $selling - $cost;
    }

    /* =========================
       TOTAL SALES THIS YEAR
    ========================= */
    public function getTotalSalesYear(): float
    {
        $stmt = $this->db->prepare("SELECT SUM(total_amount) as total FROM transactions WHERE YEAR(created_at) = YEAR(CURDATE())");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($result['total'] ?? 0);
    }
}