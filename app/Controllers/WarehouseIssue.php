<?php

namespace App\Controllers;

use App\Models\MrsModel;
use App\Models\MrsDetailModel;
use App\Models\StockModel;
use App\Models\StockMovementModel;
use Config\Database;

class WarehouseIssue extends BaseController
{
    protected $db;
    protected $mrsModel;
    protected $mrsDetailModel;
    protected $stockModel;
    protected $movementModel;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->mrsModel = new MrsModel();
        $this->mrsDetailModel = new MrsDetailModel();
        $this->stockModel = new StockModel();
        $this->movementModel = new StockMovementModel();
    }

    /* ======================================================
     * 🧾 1. Pending MRS List (for Warehouse)
     * ====================================================== */
    public function index()
    {
        $data['mrs_list'] = $this->db->query("
            SELECT m.id, m.mrs_no, m.mrs_date, m.status, 
                   COUNT(md.id) AS total_items,
                   SUM(CASE WHEN md.status='Issued' THEN 1 ELSE 0 END) AS issued_items
            FROM mrs m
            LEFT JOIN mrs_details md ON md.mrs_id = m.id
            WHERE m.status IN ('Pending','Partial')
            GROUP BY m.id
            ORDER BY m.id DESC
        ")->getResultArray();

        $data['breadcrumbs'] = [
            ['title' => 'Warehouse', 'url' => '/warehouse'],
            ['title' => 'Pending MRS (Material Requests)']
        ];

        return view('warehouse/issue_list', $data);
    }

    /* ======================================================
     * 📦 2. Issue Form for a Specific MRS
     * ====================================================== */
    public function issue($mrs_id)
    {
        $data['mrs'] = $this->mrsModel->find($mrs_id);

        $data['mrs_items'] = $this->db->query("
            SELECT md.*, i.name AS item_name, s.id AS stock_id, 
                   s.batch_no, s.qty_available, s.expiry_date, l.name AS location_name
            FROM mrs_details md
            JOIN items i ON i.id = md.item_id
            LEFT JOIN stock s ON s.item_id = md.item_id
            LEFT JOIN storage_locations l ON l.id = s.location_id
            WHERE md.mrs_id = ?
        ", [$mrs_id])->getResultArray();

        $data['breadcrumbs'] = [
            ['title' => 'Warehouse', 'url' => '/warehouse'],
            ['title' => 'Issue Materials for MRS']
        ];

        return view('warehouse/issue_form', $data);
    }

    /* ======================================================
     * 💾 3. Process Material Issue
     * ====================================================== */
    public function saveIssue()
    {
        $mrs_id = $this->request->getPost('mrs_id');
        $item_ids = $this->request->getPost('item_id');
        $issued_qtys = $this->request->getPost('qty_issued');
        $stock_ids = $this->request->getPost('stock_id');
        $remarks = $this->request->getPost('remarks');
        $user_id = session()->get('user_id');

        $this->db->transStart();

        foreach ($item_ids as $i => $item_id) {
            $issued_qty = (float)$issued_qtys[$i];
            $stock_id = $stock_ids[$i] ?? null;

            if ($issued_qty <= 0 || !$stock_id) continue;

            $stock = $this->stockModel->find($stock_id);
            if (!$stock) continue;

            $new_balance = max(0, $stock['qty_available'] - $issued_qty);

            // 1️⃣ Update stock balance
            $this->stockModel->update($stock['id'], ['qty_available' => $new_balance]);

            // 2️⃣ Update MRS Detail
            $this->mrsDetailModel->where('mrs_id', $mrs_id)
                ->where('item_id', $item_id)
                ->set([
                    'qty_issued' => $issued_qty,
                    'status' => 'Issued',
                    'remarks' => $remarks[$i] ?? null
                ])->update();

            // 3️⃣ Insert Stock Movement log
            $this->movementModel->insert([
                'stock_id' => $stock['id'],
                'movement_type' => 'OUT',
                'reference_table' => 'mrs',
                'reference_id' => $mrs_id,
                'qty' => $issued_qty,
                'balance_after' => $new_balance,
                'remarks' => 'Issued against MRS #' . $mrs_id,
                'moved_by' => $user_id,
                'moved_at' => date('Y-m-d H:i:s')
            ]);
        }

        // 4️⃣ Update MRS header status
        $pending = $this->mrsDetailModel->where('mrs_id', $mrs_id)
            ->where('status', 'Pending')
            ->countAllResults();

        $this->mrsModel->update($mrs_id, [
            'status' => $pending == 0 ? 'Issued' : 'Partial'
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            session()->setFlashdata('error', 'Error during stock issue process.');
        } else {
            session()->setFlashdata('success', 'Items issued successfully and stock updated.');
        }

        return redirect()->to('/warehouseissue');
    }

    /* ======================================================
     * 📜 4. Movement Log (Recent 50)
     * ====================================================== */
    public function movements()
    {
        $data['movements'] = $this->db->query("
            SELECT sm.*, i.name AS item_name, s.batch_no, 
                   u.name AS moved_user, l.name AS location_name
            FROM stock_movements sm
            LEFT JOIN stock s ON s.id = sm.stock_id
            LEFT JOIN items i ON i.id = s.item_id
            LEFT JOIN users u ON u.id = sm.moved_by
            LEFT JOIN storage_locations l ON l.id = s.location_id
            ORDER BY sm.id DESC
            LIMIT 50
        ")->getResultArray();

        $data['breadcrumbs'] = [
            ['title' => 'Warehouse', 'url' => '/warehouse'],
            ['title' => 'Recent Stock Movements']
        ];

        return view('warehouse/stock_movements', $data);
    }
}
