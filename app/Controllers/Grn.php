<?php
namespace App\Controllers;

use App\Models\GrnModel;
use App\Models\GrnDetailModel;
use App\Models\SupplierModel;
use App\Models\ItemModel;
use App\Models\UnitModel;
use App\Models\QcResultModel;
use App\Models\PurchaseOrderModel;
use App\Models\BatchModel;
use App\Models\GrnCategoryModel;
use App\Models\ArnModel;
use App\Models\LocationModel;
use App\Models\StorageConditionModel;
use App\Models\StorageLocationModel;


class Grn extends BaseController
{
    protected $grnModel;
    protected $grnDetailModel;
    protected $supplierModel;
    protected $itemModel;
    protected $unitModel;
    protected $qcModel;
    protected $poModel;
    protected $batchModel;
    protected $db;
    protected $arnModel;
    protected $categoryModel;



    public function __construct() {
        helper(['activity']); // log_activity
        $this->grnModel = new GrnModel();
        $this->grnDetailModel = new GrnDetailModel();
        $this->supplierModel = new SupplierModel();
        $this->itemModel = new ItemModel();
        $this->unitModel = new UnitModel();
        $this->qcModel = new QcResultModel();
        $this->poModel = new PurchaseOrderModel();
        $this->batchModel = new BatchModel();
        $this->db = \Config\Database::connect();
        $this->categoryModel = new GrnCategoryModel();
        $this->arnModel = new ArnModel();

    }

    public function index() {
        return redirect()->to('/grn/list');
    }

    public function list() {
        $search = trim($this->request->getGet('search') ?? '');
        $status = trim($this->request->getGet('status') ?? '');

        $builder = $this->grnModel
        ->select('grn.*, suppliers.name AS supplier_name, users.name AS received_by_name')
        ->join('suppliers', 'suppliers.id = grn.supplier_id', 'left')
        ->join('users', 'users.id = grn.received_by', 'left');

        if ($search !== '') {
            $builder->groupStart()
            ->like('grn.grn_no', $search)
            ->orLike('suppliers.name', $search)
            ->orLike('users.name', $search)
            ->groupEnd();
        }

        if ($status !== '') {
            $builder->where('LOWER(grn.status)', strtolower($status));
        }

        $data['grns'] = $builder
        ->orderBy('grn.id', 'DESC')
        ->findAll();

        $data['breadcrumbs'] = [
            ['title' => 'Home',
                'url' => '/dashboard'],
            ['title' => 'Goods Receipt Notes']
        ];

        return view('grn/list', $data);
    }

    public function form($id = null)
{
    $data = [];

    // ===== STORAGE LOCATIONS (ALWAYS LOAD) =====
    $data['storage_locations'] = (new \App\Models\StorageLocationModel())
        ->where('status', 1)
        ->findAll();

    if ($id) {

        // ===== GRN HEADER =====
        $data['grn'] = $this->grnModel
            ->select('grn.*, purchase_orders.po_number, suppliers.name AS supplier_name')
            ->join('purchase_orders', 'purchase_orders.id = grn.po_id', 'left')
            ->join('suppliers', 'suppliers.id = grn.supplier_id', 'left')
            ->where('grn.id', $id)
            ->first();

        // ===== GRN ITEMS (WITH STORAGE LOCATION) =====
        $data['grn_items'] = $this->grnDetailModel
            ->select('
                grn_details.id,
                grn_details.grn_id,
                grn_details.item_id,
                grn_details.storage_location_id,

                grn_details.batch_no,
                grn_details.lot_no,
                grn_details.capacity,
                grn_details.noc,
                grn_details.qty_received,
                grn_details.unit_id,
                grn_details.rate,
                grn_details.amount,
                grn_details.remarks,

                grn_details.mfg_date,
                grn_details.expiry_date,
                grn_details.retest_date
            ')
            ->where('grn_details.grn_id', $id)
            ->findAll();

    } else {
        $data['grn'] = null;
        $data['grn_items'] = [];
    }

    // ===== AUTO GRN NUMBER =====
    $year  = date('Y');
    $month = date('m');

    $lastGrn = $this->grnModel
        ->like('grn_no', "GRN-$year-$month", 'after')
        ->orderBy('id', 'DESC')
        ->first();

    $nextNumber = ($lastGrn && preg_match('/GRN-' . $year . '-' . $month . '-(\d+)/', $lastGrn['grn_no'], $m))
        ? intval($m[1]) + 1
        : 1;

    $data['auto_no'] = sprintf('GRN-%s-%s-%03d', $year, $month, $nextNumber);

    // ===== MASTER DATA =====
    $data['suppliers'] = $this->supplierModel->findAll();
    $data['items']     = $this->itemModel->findAll();
    $data['units']     = $this->unitModel->findAll();

    $data['conditions'] = (new StorageConditionModel())
    ->where('status', 'Active')
    ->findAll();

    $data['purchase_orders'] = $this->poModel
        ->select('purchase_orders.*, suppliers.name AS supplier_name')
        ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
        ->orderBy('purchase_orders.id', 'DESC')
        ->findAll();

    $data['categories'] = $this->categoryModel
        ->where('status', 'Active')
        ->findAll();

    $data['locations'] = (new LocationModel())
    ->orderBy('name', 'ASC')
    ->findAll();


    // ===== BREADCRUMBS =====
    $data['breadcrumbs'] = [
        ['title' => 'Home', 'url' => '/dashboard'],
        ['title' => 'GRN',  'url' => '/grn/list'],
        ['title' => $id ? 'Edit GRN' : 'New GRN']
    ];

    return view('grn/form', $data);
}



    public function save()
{
    $db = $this->db; // already set in constructor
    $db->transStart();

    try {
        $id             = $this->request->getPost('id');
        $category_id    = $this->request->getPost('category_id');
        $po_id          = $this->request->getPost('po_id');
        $supplier_id    = $this->request->getPost('supplier_id');
        $received_date  = $this->request->getPost('received_date');
        $gate_entry_no  = $this->request->getPost('gate_entry_no');
        $gate_entry_date = $this->request->getPost('gate_entry_date');
        $challan_no     = $this->request->getPost('challan_no');
        $challan_date   = $this->request->getPost('challan_date');
        $transport_name = $this->request->getPost('transport_name');
        $lr_no          = $this->request->getPost('lr_no');
        $lr_date        = $this->request->getPost('lr_date');
        $vehicle_no     = $this->request->getPost('vehicle_no');
        $location       = $this->request->getPost('location');
        $manufacturer   = $this->request->getPost('manufacturer');
        $reported_at    = $this->request->getPost('reported_at');
        $unloaded_at    = $this->request->getPost('unloaded_at');
        $expiry_type    = $this->request->getPost('expiry_type') ?? 'expiry';
        $items          = $this->request->getPost('items');

        // Basic validation
        if (empty($category_id)) {
            throw new \Exception('GRN Category is required.');
        }
        if (empty($supplier_id) || empty($received_date)) {
            throw new \Exception('Supplier and Received Date are required.');
        }

        // HEADER: update or insert
        if ($id) {
            // remove old details & QC entries
            $oldDetails = $this->grnDetailModel->where('grn_id', $id)->findAll();
            if (!empty($oldDetails)) {
                $oldIds = array_column($oldDetails, 'id');
                if (!empty($oldIds)) {
                    $this->qcModel->whereIn('grn_detail_id', $oldIds)->delete();
                }
            }
            $this->grnDetailModel->where('grn_id', $id)->delete();

            // Update header
            $this->grnModel->update($id, [
                'category_id'      => $category_id,
                'po_id'            => $po_id,
                'supplier_id'      => $supplier_id,
                'received_date'    => $received_date,
                'expiry_type'      => $expiry_type,
                'gate_entry_no'    => $gate_entry_no,
                'gate_entry_date'  => $gate_entry_date,
                'challan_no'       => $challan_no,
                'challan_date'     => $challan_date,
                'transport_name'   => $transport_name,
                'lr_no'            => $lr_no,
                'lr_date'          => $lr_date,
                'vehicle_no'       => $vehicle_no,
                'location'         => $location,
                'manufacturer'     => $manufacturer,
                'reported_at'      => $reported_at,
                'unloaded_at'      => $unloaded_at,
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);

            $grn_id = $id; // <- fixed assignment
        } else {
            // Generate GRN & ARN numbers
            $year = date('Y');
            $month = date('m');
            $last = $this->grnModel
                ->like('grn_no', "GRN-$year-$month", 'after')
                ->orderBy('id', 'DESC')
                ->first();

            $nextNumber = ($last && preg_match('/(\d+)$/', $last['grn_no'], $m))
                ? intval($m[1]) + 1 : 1;

            $grn_no = sprintf('GRN-%s-%s-%03d', $year, $month, $nextNumber);
            $arn_no = sprintf('ARN-%s-%s-%03d', $year, $month, $nextNumber);

            // Insert new GRN header
            // set grn_date from received_date (fallback to today)
$grn_date = $received_date ?: date('Y-m-d');

$insertData = [
    'category_id'     => $category_id,
    'grn_no'          => $grn_no,
    'arn_no'          => $arn_no,
    'po_id'           => $po_id,
    'supplier_id'     => $supplier_id,
    'grn_date'      => date('Y-m-d'),        // <-- required column fixed
    'received_date' => $received_date,
    //'expiry_type'     => $expiry_type,
    'gate_entry_no'   => $gate_entry_no,
    'gate_entry_date' => $gate_entry_date,
    'challan_no'      => $challan_no,
    'challan_date'    => $challan_date,
    'transport_name'  => $transport_name,
    'lr_no'           => $lr_no,
    'lr_date'         => $lr_date,
    'vehicle_no'      => $vehicle_no,
    'location'        => $location,
    'manufacturer'    => $manufacturer,
    'reported_at'     => $reported_at,
    'unloaded_at'     => $unloaded_at,
    'status'          => 'Pending',
    'received_by'     => session('user_id') ?? 1,
    // 'department_id' => session('department_id') ?? 1,  // <- remove unless column exists
    'created_at'      => date('Y-m-d H:i:s'),
];
log_message('debug', 'GRN insert payload: ' . json_encode($insertData));
if (empty($insertData) || !is_array($insertData)) {
    throw new \Exception('DEBUG: insertData is empty or not array. Payload: ' . var_export($insertData, true));
}


            $this->grnModel->insert($insertData);
            $grn_id = $this->grnModel->getInsertID();

            // If insert failed, fetch DB error
            if (empty($grn_id)) {
    $dbError = $this->grnModel->db->error();

    log_message(
        'error',
        'GRN header insert failed. DB Error: ' . json_encode($dbError) .
        ' | payload: ' . json_encode($insertData)
    );

    $errMsg = $dbError['message'] ?? 'Unknown DB error while inserting GRN header.';
    throw new \Exception('Failed to create GRN header: ' . $errMsg);
}

        }

        // DETAILS
        if (empty($items) || !is_array($items)) {
            throw new \Exception('At least one item must be added.');
        }

        foreach ($items as $row => $item) {

    if (empty($item['item_id'])) {
        continue;
    }

    $expiryOrRetestDate = $item['expiry_or_retest'] ?? null;

    if (empty($item['storage_location_id'])) {
    throw new \Exception('Storage Location is required for each item.');
}


    $detailData = [
        'grn_id'       => $grn_id,
        'item_id'      => $item['item_id'],

        'storage_location_id' => $item['storage_location_id'] ?? null,

        // identifiers
        'batch_no'     => $item['batch_no'] ?? null,
        'lot_no'       => $item['lot_no'] ?? null,

        // quantities
        'capacity'     => $item['capacity'] ?? null,
        'noc'          => $item['noc'] ?? null,
        'qty_received' => $item['quantity'] ?? null,

        // unit & pricing
        'unit_id'      => $item['unit_id'] ?? null,
        'rate'         => $item['rate'] ?? null,
        'amount'       => $item['amount'] ?? null,

        // dates
        'mfg_date'     => $item['mfg_date'] ?? null,
        'expiry_date'  => ($expiry_type === 'expiry')
                            ? $expiryOrRetestDate
                            : null,
        'retest_date'  => ($expiry_type === 'retest')
                            ? $expiryOrRetestDate
                            : null,

        // misc
        'remarks'      => $item['remarks'] ?? null,
        'status'       => 'Pending',
        'created_by'   => session('user_id') ?? 1,
        'created_at'   => date('Y-m-d H:i:s'),
    ];

    $this->grnDetailModel->insert($detailData);

    $grnDetailId = $this->grnDetailModel->getInsertID();

    // Batch table (only inventory-critical fields)
    $batchData = [
        'material_id'   => $item['item_id'],
        'supplier_id'   => $supplier_id,
        'grn_id'        => $grn_id,
        'batch_no'      => $item['batch_no'] ?? null,
        'qty_total'     => $item['quantity'] ?? 0,
        'qty_available' => $item['quantity'] ?? 0,
        'expiry_date'   => ($expiry_type === 'expiry') ? $expiryOrRetestDate : null,
        'retest_date'   => ($expiry_type === 'retest') ? $expiryOrRetestDate : null,
        'mfg_date'      => $item['mfg_date'] ?? null,
        'status'        => 'Pending',
    ];

    $this->batchModel->insert($batchData);

    // QC entry
    $this->qcModel->insert([
        'grn_detail_id' => $grnDetailId,
        'qc_status'     => 'Pending',
        'store_status'  => 'Pending',
        'created_at'    => date('Y-m-d H:i:s'),
    ]);
}


        // Finalize transaction
        $db->transComplete();

        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', '❌ Transaction failed while saving GRN.');
        }

        return redirect()->to('/grn/list')->with('success', '✅ GRN saved successfully.');

    } catch (\Throwable $e) {
        $db->transRollback();
        log_message('error', 'GRN Save Exception: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', '❌ Failed to save GRN: ' . $e->getMessage());
    }
}



    public function view($id) {
        $data['grn'] = $this->grnModel
        ->select('
                grn.*,
                purchase_orders.po_number AS po_number,
                suppliers.name AS supplier_name,
                users_received.name AS received_by_name,
                users_verified.name AS verified_by_name,
                users_approved.name AS approved_by_name
            ')
        ->join('purchase_orders', 'purchase_orders.id = grn.po_id', 'left')
        ->join('suppliers', 'suppliers.id = grn.supplier_id', 'left')
        ->join('users AS users_received', 'users_received.id = grn.received_by', 'left')
        ->join('users AS users_verified', 'users_verified.id = grn.verified_by', 'left')
        ->join('users AS users_approved', 'users_approved.id = grn.approved_by', 'left')
        ->where('grn.id', $id)
        ->first();

        if (!$data['grn']) {
            return redirect()->to('/grn/list')->with('error', 'GRN not found.');
        }

        $data['items'] = $this->grnDetailModel
        ->select('
                grn_details.id,
                grn_details.item_id,
                grn_details.batch_no,
                grn_details.mfg_date,
                grn_details.expiry_date,
                grn_details.qty_received,
                grn_details.rate,
                grn_details.amount,
                units.name AS unit_name,
                items.name AS item_name,
                items.code AS item_code
            ')
        ->join('items', 'items.id = grn_details.item_id', 'left')
        ->join('units', 'units.id = grn_details.unit_id', 'left')
        ->where('grn_details.grn_id', $id)
        ->findAll();

        $data['breadcrumbs'] = [
            ['title' => 'Home',
                'url' => '/dashboard'],
            ['title' => 'GRN',
                'url' => '/grn/list'],
            ['title' => 'View GRN']
        ];

        return view('grn/view', $data);
    }

    public function info($id) {
        $po = $this->poModel
        ->select('purchase_orders.*, suppliers.name as supplier_name')
        ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
        ->where('purchase_orders.id', $id)
        ->first();

        if (!$po) {
            return $this->response->setJSON(['success' => false, 'message' => 'PO not found']);
        }

        // Return unit_id along with unit name so front-end gets numeric id
        $poItems = $this->db->table('purchase_order_items poi')
        ->select('poi.*, items.name as item_name, items.code as item_code, units.name as unit, units.id as unit_id')
        ->join('items', 'items.id = poi.item_id', 'left')
        ->join('units', 'units.id = poi.unit_id', 'left')
        ->where('poi.po_id', $id)
        ->get()
        ->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'po' => $po,
            'items' => $poItems
        ]);
    }

    public function verify($id) {
        $this->grnModel->update($id, [
            'status' => 'QC Completed',
            'verified_by' => session('user_id'),
            'verified_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        log_activity('GRN Verified', 'grn', $id);
        return redirect()->to('/grn/view/'.$id)->with('success', 'GRN verified successfully.');
    }

    public function approve($id) {
        $this->grnModel->update($id, [
            'status' => 'Approved',
            'approved_by' => session('user_id'),
            'approved_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        log_activity('GRN Approved', 'grn', $id);
        return redirect()->to('/grn/view/'.$id)->with('success', 'GRN approved successfully.');
    }
}