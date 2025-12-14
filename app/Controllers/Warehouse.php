<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class Warehouse extends BaseController
{
    /** =====================================================
    * 🏬 WAREHOUSE DASHBOARD — Verification + Inventory Tabs
    * ===================================================== */
    public function dashboard()
    {
        $db = \Config\Database::connect();

        // 1️⃣ QC-approved items pending warehouse verification
        $verificationItems = $db->table('qc_results qr')
            ->select('qr.id, g.id AS grn_id, g.grn_no, i.name AS item_name, gd.batch_no,
                  s.name AS supplier_name, gd.qty_received AS approved_qty,
                  qr.remarks, qr.store_status, gd.id AS grn_detail_id')
            ->join('grn_details gd', 'gd.id = qr.grn_detail_id')
            ->join('grn g', 'g.id = gd.grn_id')
            ->join('items i', 'i.id = gd.item_id')
            ->join('suppliers s', 's.id = g.supplier_id', 'left')
            ->where('qr.qc_status', 'Accepted')
            ->where('qr.store_status', 'Pending')
            ->groupStart()
            ->whereIn('g.status', ['QC Completed', 'QC In Progress'])
            ->orWhere('g.status IS NOT NULL', null, false)
            ->groupEnd()
            ->orderBy('g.id', 'DESC')
            ->orderBy('gd.id', 'ASC')
            ->get()
            ->getResultArray();

        // 🧩 Supplier & Item Filters
        $supplier_id = $this->request->getGet('supplier_id');
        $item_id = $this->request->getGet('item_id');

        // Fetch dropdown data
        $suppliers = $db->table('suppliers')->select('id, name')->orderBy('name', 'ASC')->get()->getResultArray();
        $items = $db->table('items')->select('id, name')->orderBy('name', 'ASC')->get()->getResultArray();

        // Base query for inventory
        $builder = $db->table('stock st')
            ->select("
                st.id, st.batch_no, st.expiry_date, st.qty_available, st.created_at,
                i.name AS item_name, i.code AS item_code,
                u.name AS uom,
                sl.name AS location_name,
                s.name AS supplier_name,
                g.grn_no
            ")
            ->join('items i', 'i.id = st.item_id')
            ->join('units u', 'u.id = i.unit_id', 'left')
            ->join('storage_locations sl', 'sl.id = st.location_id', 'left')
            ->join('grn_details gd', 'gd.id = st.grn_detail_id', 'left')
            ->join('grn g', 'g.id = gd.grn_id', 'left')
            ->join('suppliers s', 's.id = g.supplier_id', 'left');

        // Apply filters dynamically
        if (!empty($supplier_id)) {
            $builder->where('s.id', $supplier_id);
        }
        if (!empty($item_id)) {
            $builder->where('i.id', $item_id);
        }

        $inventoryItems = $builder->orderBy('st.id', 'DESC')->get()->getResultArray();

        // Pass filter data to view
        $data['suppliers'] = $suppliers;
        $data['items'] = $items;
        $data['selectedSupplier'] = $supplier_id;
        $data['selectedItem'] = $item_id;

        // 2️⃣ Current Stock / Inventory
        $inventoryItems = $db->table('stock st')
            ->select('st.id, i.name AS item_name, i.code AS item_code, st.batch_no, st.expiry_date,
                  st.qty_available, u.name AS uom, sl.name AS location_name, sl.code AS location_code,
                  qr.qc_status, qr.store_status, g.grn_no, s.name AS supplier_name, st.created_at')
            ->join('items i', 'i.id = st.item_id')
            ->join('units u', 'u.id = i.unit_id', 'left')
            ->join('storage_locations sl', 'sl.id = st.location_id', 'left')
            ->join('qc_results qr', 'qr.grn_detail_id = st.grn_detail_id', 'left')
            ->join('grn_details gd', 'gd.id = st.grn_detail_id', 'left')
            ->join('grn g', 'g.id = gd.grn_id', 'left')
            ->join('suppliers s', 's.id = g.supplier_id', 'left')
            ->orderBy('st.id', 'DESC')
            ->get()
            ->getResultArray();

        // 3️⃣ Stock Movement Logs
        $movements = $db->table('stock_movements sm')
            ->select('sm.*, i.name AS item_name, st.batch_no, u.name AS moved_by_name')
            ->join('stock st', 'st.id = sm.stock_id')
            ->join('items i', 'i.id = st.item_id')
            ->join('users u', 'u.id = sm.moved_by', 'left')
            ->orderBy('sm.moved_at', 'DESC')
            ->get()
            ->getResultArray();

        // 🟡 Expiry Alerts (next 60 days)
        $expiryAlerts = $db->table('stock st')
            ->select('i.name AS item_name, st.batch_no, st.expiry_date, DATEDIFF(st.expiry_date, CURDATE()) AS days_left', false)
            ->join('items i', 'i.id = st.item_id')
            ->where('st.expiry_date IS NOT NULL', null, false)
            ->where('st.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 60 DAY)', null, false)
            ->where('st.qty_available >', 0)
            ->orderBy('st.expiry_date', 'ASC')
            ->get()
            ->getResultArray();

        $data['expiryAlerts'] = $expiryAlerts;

        // Determine expiry badge color
        $expiryBadgeColor = 'success';
        foreach ($expiryAlerts as $alert) {
            if ($alert['days_left'] <= 10) {
                $expiryBadgeColor = 'danger';
                break;
            } elseif ($alert['days_left'] <= 30) {
                $expiryBadgeColor = 'warning';
            }
        }
        $data['expiryBadgeColor'] = $expiryBadgeColor;

        // 🧭 Breadcrumbs
        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Warehouse Department']
        ];

        // ✅ Pass all data to view
        $data['verificationItems'] = $verificationItems;
        $data['inventoryItems'] = $inventoryItems;
        $data['movements'] = $movements;
        $data['page_title'] = 'Warehouse Verification & Inventory';
        $data['sub_title'] = 'Review QC-approved items and manage warehouse stock';

        return view('warehouse/dashboard', $data);
    }

    /** =====================================================
    * ✅ Accept QC Item — Add to Stock + Mark as Accepted
    * ===================================================== */
    public function accept($qc_id)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // 🔹 Fetch QC item info
        $item = $db->table('qc_results qr')
            ->select('qr.*, gd.item_id, gd.batch_no, gd.expiry_date, gd.qty_received, gd.id AS grn_detail_id')
            ->join('grn_details gd', 'gd.id = qr.grn_detail_id')
            ->where('qr.id', $qc_id)
            ->get()
            ->getRowArray();

        if (!$item) {
            return redirect()->back()->with('error', 'Invalid item selected.');
        }

        // 🔹 Check if stock exists
        $exists = $db->table('stock')
            ->where('item_id', $item['item_id'])
            ->where('batch_no', $item['batch_no'])
            ->get()
            ->getRowArray();

        $movedBy = session()->get('user_id') ?? null;
        $remarks = 'Accepted from QC (ID: ' . $qc_id . ')';

        if ($exists) {
            $newQty = (float)$exists['qty_available'] + (float)$item['qty_received'];

            $db->table('stock')
                ->where('id', $exists['id'])
                ->update(['qty_available' => $newQty]);

            $stockId = $exists['id'];
            $msg = 'Item quantity updated in existing stock.';
        } else {
            $insertData = [
                'item_id' => $item['item_id'],
                'grn_detail_id' => $item['grn_detail_id'],
                'batch_no' => $item['batch_no'] ?? null,
                'expiry_date' => $item['expiry_date'] ?? null,
                'qty_available' => $item['qty_received'] ?? 0,
                'location_id' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $db->table('stock')->insert($insertData);
            $stockId = $db->insertID();
            $newQty = (float)$item['qty_received'];
            $msg = 'New stock item added successfully.';
        }

        // ✅ Log stock movement
        $db->table('stock_movements')->insert([
            'stock_id' => $stockId,
            'movement_type' => 'IN',
            'reference_table' => 'qc_results',
            'reference_id' => $qc_id,
            'qty' => $item['qty_received'],
            'balance_after' => $newQty,
            'remarks' => $remarks,
            'moved_by' => $movedBy,
            'moved_at' => date('Y-m-d H:i:s')
        ]);

        // ✅ Update QC store_status
        $db->table('qc_results')
            ->where('id', $qc_id)
            ->update([
                'store_status' => 'Accepted',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Transaction failed.');
        }

        return redirect()->to('/warehouse')->with('success', $msg);
    }

    /** =====================================================
    * ❌ Reject QC Item — Update store_status = Rejected
    * ===================================================== */
    public function reject($qc_id)
    {
        $db = \Config\Database::connect();

        $exists = $db->table('qc_results')->where('id', $qc_id)->get()->getRowArray();
        if (!$exists) {
            return redirect()->back()->with('error', 'Invalid item selected.');
        }

        $db->table('qc_results')->where('id', $qc_id)->update([
            'store_status' => 'Rejected',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/warehouse')->with('success', 'Item rejected by warehouse.');
    }

    /** =====================================================
    * 🩹 Backward compatibility - /warehouse/index route
    * ===================================================== */
    public function index()
    {
        return $this->dashboard();
    }

    public function view($id)
    {
        $db = \Config\Database::connect();

        $data['item'] = $db->table('stock s')
            ->select("
                s.*,
                i.name AS item_name,
                i.code AS item_code,
                u.name AS unit_name,
                l.name AS location_name,
                qr.qc_status,
                qr.store_status,
                qr.remarks AS qc_remarks
            ")
            ->join('items i', 'i.id = s.item_id', 'left')
            ->join('units u', 'u.id = i.unit_id', 'left')
            ->join('storage_locations l', 'l.id = s.location_id', 'left')
            ->join('qc_results qr', 'qr.grn_detail_id = s.grn_detail_id', 'left')
            ->where('s.id', $id)
            ->get()
            ->getRowArray();

        if (!$data['item']) {
            return redirect()->to('/warehouse')->with('error', 'Item not found.');
        }

        $from = $this->request->getGet('from');
        if (!in_array($from, ['inventory', 'verification'])) {
            $from = 'inventory';
        }

        $data['back_url'] = base_url('/warehouse?tab=' . $from);

        $data['breadcrumbs'] = [
            ['title' => 'Dashboard', 'url' => '/dashboard'],
            ['title' => 'Warehouse Department', 'url' => '/warehouse'],
            ['title' => 'Material Details']
        ];

        return view('warehouse/view', $data);
    }

    /** =====================================================
    * ➕ Add Manual Stock Movement (Outward / Adjustment)
    * ===================================================== */
    public function addMovement()
    {
        $db = \Config\Database::connect();
        $post = $this->request->getPost();

        if (!$post) {
            return redirect()->back()->with('error', 'Invalid request.');
        }

        $stock_id = (int)$post['stock_id'];
        $movement_type = $post['movement_type'];
        $qty = (float)$post['qty'];
        $remarks = trim($post['remarks']);
        $user_id = session()->get('user_id') ?? null;

        $stock = $db->table('stock')->where('id', $stock_id)->get()->getRowArray();
        if (!$stock) {
            return redirect()->back()->with('error', 'Invalid stock item selected.');
        }

        $newBalance = (float)$stock['qty_available'];

        if ($movement_type === 'OUT') {
            if ($qty > $newBalance) {
                return redirect()->back()->with('error', 'Not enough stock available for OUT movement.');
            }
            $newBalance -= $qty;
        } elseif ($movement_type === 'ADJUSTMENT') {
            $newBalance = $qty;
        }

        $db->table('stock')->where('id', $stock_id)->update(['qty_available' => $newBalance]);

        $db->table('stock_movements')->insert([
            'stock_id' => $stock_id,
            'movement_type' => $movement_type,
            'qty' => $qty,
            'balance_after' => $newBalance,
            'remarks' => $remarks,
            'moved_by' => $user_id,
            'moved_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/warehouse?tab=movement')->with('success', 'Stock movement added successfully.');
    }

    public function getStockQty($stock_id)
    {
        $db = \Config\Database::connect();
        $stock = $db->table('stock')
            ->select('qty_available')
            ->where('id', $stock_id)
            ->get()
            ->getRowArray();

        return $this->response->setJSON($stock ?? ['qty_available' => 0]);
    }

    public function get_storage_locations()
    {
        $locId = $this->request->getPost('location_id');
        $storageModel = new \App\Models\StorageLocationModel();
        return $this->response->setJSON(
            $storageModel->where('location_id', $locId)->findAll()
        );
    }

    public function accept_item()
    {
        $itemId = $this->request->getPost('item_id');
        $locationId = $this->request->getPost('location_id');
        $storageId = $this->request->getPost('storage_id');

        $this->grnItemModel->update($itemId, [
            'store_status' => 'accepted',
            'location_id' => $locationId,
            'storage_id' => $storageId
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }
}
