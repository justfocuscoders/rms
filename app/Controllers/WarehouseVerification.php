<?php
namespace App\Controllers;

use App\Models\QcResultModel;
use App\Models\StockModel;
use App\Models\ItemModel;
use App\Models\StorageLocationModel;

class WarehouseVerification extends BaseController
{
    /** =====================================================
     * 🧾 1️⃣  Show all QC-approved items pending warehouse verification
     * ===================================================== */
    public function index()
    {
        $qcModel = new QcResultModel();

        $data['breadcrumbs'] = [
            ['title' => 'Dashboard', 'url' => '/dashboard'],
            ['title' => 'Warehouse Verification', 'url' => '']
        ];

        $data['page_title'] = 'Warehouse Verification';
        $data['sub_title'] = 'Verify QC-approved items before adding to warehouse stock';

        $data['pendingItems'] = $qcModel
            ->select('qc_results.*, items.name as item_name, grn.id as grn_id, grn.grn_no, suppliers.name as supplier_name, grn_details.batch_no, grn_details.id as grn_detail_id')
            ->join('grn_details', 'grn_details.id = qc_results.grn_detail_id', 'left')
            ->join('grn', 'grn.id = grn_details.grn_id', 'left')
            ->join('items', 'items.id = grn_details.item_id', 'left')
            ->join('suppliers', 'suppliers.id = grn.supplier_id', 'left')
            ->where('qc_results.qc_status', 'Approved')
            ->where('qc_results.store_status', null)
            ->orderBy('qc_results.id', 'DESC')
            ->findAll();

        return view('warehouse/verification_list', $data);
    }

    /** =====================================================
     * ✅ 2️⃣  Accept QC-approved item → add to stock
     * ===================================================== */
    public function accept($qcId = null)
    {
        if (!$qcId) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        $qcModel = new QcResultModel();
        $stockModel = new StockModel();

        $qc = $qcModel
            ->select('qc_results.*, grn_details.item_id, grn_details.batch_no, grn_details.qty_received')
            ->join('grn_details', 'grn_details.id = qc_results.grn_detail_id', 'left')
            ->where('qc_results.id', $qcId)
            ->first();

        if (!$qc) {
            return redirect()->back()->with('error', 'Record not found.');
        }

        // ✅ Add item to warehouse stock
        $stockModel->insert([
            'item_id'       => $qc['item_id'],
            'batch_no'      => $qc['batch_no'],
            'qty_available' => $qc['qty_received'], // use qty_received from GRN
            'location_id'   => 1, // default warehouse location
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        // ✅ Mark as accepted in QC results
        $qcModel->update($qcId, [
            'store_status' => 'Accepted',
            'updated_at'   => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/warehouseverification')->with('success', 'Item accepted and added to warehouse stock.');
    }

    /** =====================================================
     * ❌ 3️⃣  Reject QC-approved item → mark as rejected
     * ===================================================== */
    public function reject($qcId = null)
    {
        if (!$qcId) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        $qcModel = new QcResultModel();
        $qcModel->update($qcId, [
            'store_status' => 'Rejected',
            'updated_at'   => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/warehouseverification')->with('success', 'Item rejected by warehouse.');
    }
}
