<?php
namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * 🧭 Returns filtered dashboard data for Admin (used by AJAX filters)
     * @param string $period e.g. this_month, last_3_months, this_year
     * @param string $dept Department ID or 'all'
     */
    public function getFilteredDashboardData($period = 'this_month', $dept = 'all')
    {
        $whereDept = '';
        if ($dept !== 'all') {
            $whereDept = "AND department_id = " . $this->db->escape($dept);
        }

        // Define date filter conditions
        $dateFilter = match ($period) {
            'this_month' => "MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())",
            'last_3_months' => "created_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)",
            'this_year' => "YEAR(created_at) = YEAR(CURDATE())",
            default => "1=1"
        };

        // Summary metrics
        $total_pos = $this->db->query("SELECT COUNT(*) AS total FROM purchase_orders WHERE $dateFilter")->getRow()->total ?? 0;
        $total_mrs = $this->db->query("SELECT COUNT(*) AS total FROM mrs WHERE $dateFilter $whereDept")->getRow()->total ?? 0;
        $total_grn = $this->db->query("SELECT COUNT(*) AS total FROM grn WHERE $dateFilter")->getRow()->total ?? 0;

        // Stock chart (top 5 items)
        $stock_chart = $this->db->query("
            SELECT i.name AS item_name, s.qty_available
            FROM stock s
            JOIN items i ON s.item_id = i.id
            ORDER BY s.qty_available DESC
            LIMIT 5
        ")->getResultArray();

        // PO trend (line chart)
        $po_trend = $this->db->query("
            SELECT DATE_FORMAT(created_at, '%b') AS month, COUNT(*) AS total
            FROM purchase_orders
            WHERE $dateFilter
            GROUP BY MONTH(created_at)
            ORDER BY MONTH(created_at)
        ")->getResultArray();

        // Return array for AJAX response
        return [
            'total_pos' => $total_pos,
            'total_mrs' => $total_mrs,
            'total_grn' => $total_grn,
            'stock_chart' => $stock_chart,
            'po_trend' => $po_trend
        ];
    }

    /* Optional utility methods — used if you want static values for dashboard */
    public function getLowStockCount()
    {
        return $this->db->table('stock')->where('qty_available <', 10)->countAllResults();
    }

    public function getPendingPOCount()
    {
        return $this->db->table('purchase_orders')->where('status', 'Pending')->countAllResults();
    }

    public function getPendingMRSCount()
    {
        return $this->db->table('mrs')->where('status', 'Pending')->countAllResults();
    }

    public function getPendingQCCount()
    {
        return $this->db->table('qc_results')->where('qc_status', 'Pending')->countAllResults();
    }

    public function getTotalPOs()
    {
        return $this->db->table('purchase_orders')->countAllResults();
    }

    public function getTotalMRS()
    {
        return $this->db->table('mrs')->countAllResults();
    }

    public function getTotalGRN()
    {
        return $this->db->table('grn')->countAllResults();
    }

    public function getTotalUsers()
    {
        return $this->db->table('users')->countAllResults();
    }

    public function getTotalSuppliers()
    {
        return $this->db->table('suppliers')->countAllResults();
    }

    public function getTotalStock()
    {
        return $this->db->table('stock')->countAllResults();
    }

    public function getStockChart()
    {
        return $this->db->query("
            SELECT i.name AS item_name, s.qty_available
            FROM stock s
            JOIN items i ON s.item_id = i.id
            ORDER BY s.qty_available DESC
            LIMIT 5
        ")->getResultArray();
    }

    public function getPOTrend()
    {
        return $this->db->query("
            SELECT DATE_FORMAT(created_at, '%b') AS month, COUNT(*) AS total
            FROM purchase_orders
            GROUP BY MONTH(created_at)
            ORDER BY MONTH(created_at)
        ")->getResultArray();
    }
}
