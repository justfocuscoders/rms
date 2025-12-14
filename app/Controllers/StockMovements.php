<?php
namespace App\Controllers;

use App\Models\StockMovementModel;
use CodeIgniter\Controller;

class StockMovements extends BaseController
{
    protected $movementModel;
    protected $db;

    public function __construct()
    {
        $this->movementModel = new StockMovementModel();
        $this->db = \Config\Database::connect();
    }

    /** =====================================================
     * 📦 STOCK MOVEMENTS — In/Out/Adjustments Log
     * ===================================================== */
    public function index()
    {
        $query = $this->db->query("
            SELECT 
                sm.id,
                sm.movement_type,
                sm.qty,
                sm.balance_after,
                sm.remarks,
                sm.moved_at,
                i.name AS item_name,
                st.batch_no,
                sl.code AS location_code,
                sl.name AS location_name,
                u.username AS moved_by_name
            FROM stock_movements sm
            JOIN stock st ON st.id = sm.stock_id
            JOIN items i ON i.id = st.item_id
            LEFT JOIN storage_locations sl ON sl.id = st.location_id
            LEFT JOIN users u ON u.id = sm.moved_by
            ORDER BY sm.moved_at DESC
        ");

        $data['movements'] = $query->getResultArray();

        // ✅ Breadcrumbs (used in header layout)
        $data['breadcrumbs'] = [
            ['title' => 'Dashboard', 'url' => '/dashboard'],
            ['title' => 'Store Department', 'url' => '/store'],
            ['title' => 'Stock Movement Log']
        ];

        $data['page_title'] = 'Stock Movement Logs';
        $data['sub_title']  = 'View all inward, outward, and adjustment history of materials';

        return view('store/stock_movements', $data);
    }

    /** =====================================================
     * 📤 EXPORT MOVEMENTS TO CSV
     * ===================================================== */
    public function export()
    {
        $movements = $this->db->query("
            SELECT 
                sm.id,
                sm.moved_at,
                i.name AS item_name,
                st.batch_no,
                sm.movement_type,
                sm.qty,
                sm.balance_after,
                sm.remarks,
                u.username AS moved_by
            FROM stock_movements sm
            JOIN stock st ON st.id = sm.stock_id
            JOIN items i ON i.id = st.item_id
            LEFT JOIN users u ON u.id = sm.moved_by
            ORDER BY sm.moved_at DESC
        ")->getResultArray();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Stock_Movements_' . date('Ymd_His') . '.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['#', 'Date/Time', 'Item', 'Batch', 'Movement', 'Qty', 'Balance After', 'Remarks', 'Moved By']);

        foreach ($movements as $m) {
            fputcsv($out, [
                $m['id'],
                $m['moved_at'],
                $m['item_name'],
                $m['batch_no'],
                $m['movement_type'],
                $m['qty'],
                $m['balance_after'],
                $m['remarks'],
                $m['moved_by']
            ]);
        }

        fclose($out);
        exit;
    }
}
