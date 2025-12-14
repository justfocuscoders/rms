<?php

namespace App\Controllers;

use App\Models\PurchaseOrderModel;
use App\Models\PurchaseOrderItemModel;
use App\Models\SupplierModel;
use App\Models\ItemModel;
use App\Models\UnitModel;
use App\Models\StorageConditionModel;
use CodeIgniter\Controller;


class PurchaseOrders extends BaseController
{
    protected $poModel;
    protected $poItemModel;
    protected $supplierModel;
    protected $itemModel;
    protected $unitModel;

    public function __construct()
    {
        helper(['activity']); // ✅ Enable activity logging helper

        $this->poModel       = new PurchaseOrderModel();
        $this->poItemModel   = new PurchaseOrderItemModel();
        $this->supplierModel = new SupplierModel();
        $this->itemModel     = new ItemModel();
        $this->unitModel     = new UnitModel();
    }

    /** Redirect to list */
    public function index()
    {
        return redirect()->to('/purchaseorders/list');
    }

    /** Purchase Order List */
    public function list()
    {
        $data['purchase_orders'] = $this->poModel
            ->select('purchase_orders.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
            ->orderBy('purchase_orders.id', 'DESC')
            ->findAll();

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Purchase Orders']
        ];

        return view('purchase_orders/list', $data);
    }

    /** Add/Edit Form */
    public function form($id = null)
    {
        $data = [];

        if ($id) {
            $data['po'] = $this->poModel->find($id);
            $data['po_items'] = $this->poItemModel->where('po_id', $id)->findAll();
        } else {
            $data['po'] = null;
            $data['po_items'] = [];
        }

        // ✅ Improved Auto Number Generation: PO-YYYY-MM-###
        $year = date('Y');
        $month = date('m');

        $lastPo = $this->poModel
            ->like('po_number', "PO-$year-$month", 'after')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastPo && preg_match('/PO-' . $year . '-' . $month . '-(\d+)/', $lastPo['po_number'], $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        $data['auto_no'] = sprintf('PO-%s-%s-%03d', $year, $month, $nextNumber);

        $data['suppliers'] = $this->supplierModel->findAll();
        $data['items'] = $this->itemModel->findAll();
        $data['units'] = $this->unitModel->findAll();

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Purchase Orders', 'url' => '/purchaseorders/list'],
            ['title' => $id ? 'Edit Purchase Order' : 'New Purchase Order']
        ];

        $data['conditions'] = (new StorageConditionModel())
    ->where('status', 'Active')
    ->findAll();


        return view('purchase_orders/form', $data);
    }

    /** Store (Create / Update) */
    public function store($id = null)
    {
        $id            = $id ?? $this->request->getPost('id');
        $supplier_id   = $this->request->getPost('supplier_id');
        $order_date    = $this->request->getPost('order_date');
        $expected_date = $this->request->getPost('expected_date');
        $remarks       = $this->request->getPost('remarks');
        $items         = $this->request->getPost('items');

        $db = \Config\Database::connect();
        $db->transStart();

        $userId = session('user_id') ?? 1;

        // 🔹 If editing existing PO
        if ($id) {
            $this->poItemModel->where('po_id', $id)->delete();

            $this->poModel->update($id, [
                'supplier_id'   => $supplier_id,
                'order_date'    => $order_date,
                'expected_date' => $expected_date,
                'remarks'       => $remarks,
                'status'        => 'Pending',
                'updated_by'    => $userId,
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);

            $po_id = $id;
        } else {
            // ✅ Generate new PO number automatically (PO-YYYY-MM-###)
            $year = date('Y');
            $month = date('m');
            $lastPo = $this->poModel
                ->like('po_number', "PO-$year-$month", 'after')
                ->orderBy('id', 'DESC')
                ->first();

            if ($lastPo && preg_match('/PO-' . $year . '-' . $month . '-(\d+)/', $lastPo['po_number'], $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            } else {
                $nextNumber = 1;
            }

            $po_number = sprintf('PO-%s-%s-%03d', $year, $month, $nextNumber);

            $po_id = $this->poModel->insert([
                'po_number'     => $po_number,
                'supplier_id'   => $supplier_id,
                'order_date'    => $order_date,
                'expected_date' => $expected_date,
                'remarks'       => $remarks,
                'status'        => 'Pending',
                'created_by'    => $userId,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        // 🔹 Insert PO Items
        if (!empty($items)) {
            foreach ($items as $item) {
                if (!empty($item['item_id'])) {
                    $this->poItemModel->insert([
                        'po_id'       => $po_id,
                        'item_id'     => $item['item_id'],
                        'qty_ordered' => $item['qty'] ?? 0,
                        'unit_price'  => $item['unit_price'] ?? 0,
                        'status'      => 'Pending',
                    ]);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Failed to save Purchase Order.');
        }

        // ✅ Log Activity (Create/Update)
        if ($id) {
            log_activity('Updated Purchase Order', 'purchase_orders', $po_id);
        } else {
            log_activity('Created Purchase Order', 'purchase_orders', $po_id);
        }

        return redirect()->to('/purchaseorders/list')->with('success', 'Purchase Order saved successfully.');
    }

    /** View PO */
    public function view($id)
    {
        $data['po'] = $this->poModel
            ->select('purchase_orders.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
            ->where('purchase_orders.id', $id)
            ->first();

        if (!$data['po']) {
            return redirect()->to('/purchaseorders/list')->with('error', 'Purchase Order not found.');
        }

        $data['items'] = $this->poItemModel
            ->select('purchase_order_items.*, items.name as item_name, items.code as item_code, units.name as unit_name')
            ->join('items', 'items.id = purchase_order_items.item_id', 'left')
            ->join('units', 'units.id = items.unit_id', 'left')
            ->where('po_id', $id)
            ->findAll();

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Purchase Orders', 'url' => '/purchaseorders/list'],
            ['title' => 'View Purchase Order']
        ];

        return view('purchase_orders/view', $data);
    }

    /** Delete PO */
    public function delete($id)
    {
        $this->poItemModel->where('po_id', $id)->delete();
        $this->poModel->delete($id);

        log_activity('Deleted Purchase Order', 'purchase_orders', $id);

        return redirect()->to('/purchaseorders/list')->with('success', 'Purchase Order deleted successfully.');
    }

    /** Approve PO */
    public function approve($id)
    {
        $this->poModel->update($id, ['status' => 'Approved']);
        log_activity('Approved Purchase Order', 'purchase_orders', $id);
        return $this->response->setJSON(['status' => 'success']);
    }

    /** Cancel PO */
    public function cancel($id)
    {
        $this->poModel->update($id, ['status' => 'Cancelled']);
        log_activity('Cancelled Purchase Order', 'purchase_orders', $id);
        return $this->response->setJSON(['status' => 'success']);
    }

    /** Dashboard Stats */
    public function stats()
    {
        $data = [
            'total'     => $this->poModel->countAllResults(),
            'pending'   => $this->poModel->where('status', 'Pending')->countAllResults(),
            'approved'  => $this->poModel->where('status', 'Approved')->countAllResults(),
            'cancelled' => $this->poModel->where('status', 'Cancelled')->countAllResults(),
        ];
        return $this->response->setJSON($data);
    }

    /** AJAX: Get PO Info for GRN */
    public function info($id)
    {
        $po = $this->poModel->find($id);
        if (!$po) {
            return $this->response->setJSON(['success' => false, 'message' => 'PO not found']);
        }

        $supplier = $this->supplierModel->find($po['supplier_id']);
        $poItems = $this->poItemModel
            ->where('po_id', $id)
            ->join('items', 'items.id = purchase_order_items.item_id', 'left')
            ->join('units', 'units.id = items.unit_id', 'left')
            ->select('purchase_order_items.*, items.name as item_name, units.name as unit_name')
            ->findAll();

        $items = array_map(fn($r) => [
            'item_id'   => $r['item_id'],
            'item_name' => $r['item_name'],
            'unit'      => $r['unit_name'] ?? '',
            'quantity'  => (float)$r['qty_ordered'],
            'rate'      => (float)$r['unit_price']
        ], $poItems);

        return $this->response->setJSON([
            'success'  => true,
            'po'       => $po,
            'supplier' => $supplier,
            'items'    => $items
        ]);
    }
    
    /* =======================================================
 * ✅ AJAX: Add Supplier from Modal
 * ======================================================= */
public function saveSupplierAjax()
{
    $data = [
        'name'          => $this->request->getPost('name'),
        'contact_person'=> $this->request->getPost('contact_person'),
        'phone'         => $this->request->getPost('phone'),
        'email'         => $this->request->getPost('email'),
        'address'       => $this->request->getPost('address'),
        'gst_number'    => $this->request->getPost('gst_number'),
        'remarks'       => $this->request->getPost('remarks'),
        'status'        => $this->request->getPost('status') ?? 1,
        'created_at'    => date('Y-m-d H:i:s'),
    ];

    if ($this->supplierModel->insert($data)) {
        $id = $this->supplierModel->getInsertID();
        $supplier = $this->supplierModel->find($id);
        log_activity('Created Supplier via PO Modal', 'suppliers', $id);
        return $this->response->setJSON(['success' => true, 'supplier' => $supplier]);
    }

    return $this->response->setJSON(['success' => false, 'message' => 'Failed to add supplier.']);
}



/* =======================================================
 * ✅ AJAX: Add Unit from Modal
 * ======================================================= */
public function saveUnitAjax()
{
    $data = [
        'name'        => $this->request->getPost('name'),
        'symbol'      => $this->request->getPost('symbol'),
        'description' => $this->request->getPost('description'),
        'status'      => $this->request->getPost('status') ?? 'active',
        'created_at'  => date('Y-m-d H:i:s'),
    ];

    if ($this->unitModel->insert($data)) {
        $id = $this->unitModel->getInsertID();
        $unit = $this->unitModel->find($id);
        log_activity('Created Unit via PO Modal', 'units', $id);
        return $this->response->setJSON(['success' => true, 'unit' => $unit]);
    }

    return $this->response->setJSON(['success' => false, 'message' => 'Failed to add unit.']);
}



/* =======================================================
 * ✅ AJAX: Add Item from Modal
 * ======================================================= */
public function saveItemAjax()
{
    $data = [
        'code'        => $this->request->getPost('code'),
        'name'        => $this->request->getPost('name'),
        'description' => $this->request->getPost('description'),
        'unit_id'     => $this->request->getPost('unit_id'),
        'unit_price'  => $this->request->getPost('unit_price'),
        'supplier_id' => $this->request->getPost('supplier_id') ?? null,
        'reorder_level' => $this->request->getPost('reorder_level') ?? 0,
        'hsn_code'      => $this->request->getPost('hsn_code'),
        'is_active'     => $this->request->getPost('is_active') ?? 1,
        'created_at'    => date('Y-m-d H:i:s'),
    ];

    if ($this->itemModel->insert($data)) {
        $id = $this->itemModel->getInsertID();
        $item = $this->itemModel->find($id);
        log_activity('Created Item via PO Modal', 'items', $id);
        return $this->response->setJSON(['success' => true, 'item' => $item]);
    }

    return $this->response->setJSON(['success' => false, 'message' => 'Failed to add item.']);
}

/* =======================================================
 * ✅ AJAX: Get Item Info by Code or ID (for auto-fill)
 * ======================================================= */
public function getItemInfo()
{
    $itemId   = $this->request->getPost('item_id');
    $itemCode = $this->request->getPost('item_code');

    $item = null;

    // Find item either by ID or Code
    if ($itemId) {
        $item = $this->itemModel
            ->select('items.*, units.name as unit_name')
            ->join('units', 'units.id = items.unit_id', 'left')
            ->find($itemId);
    } elseif ($itemCode) {
        $item = $this->itemModel
            ->select('items.*, units.name as unit_name')
            ->join('units', 'units.id = items.unit_id', 'left')
            ->where('items.code', $itemCode)
            ->first();
    }

    if (!$item) {
        return $this->response->setJSON(['success' => false, 'message' => 'Item not found']);
    }

    // ✅ Fetch last saved unit price from previous POs if available
    $lastPOItem = $this->poItemModel
        ->where('item_id', $item['id'])
        ->orderBy('id', 'DESC')
        ->first();

    $lastPrice = $lastPOItem ? $lastPOItem['unit_price'] : $item['unit_price'];

    return $this->response->setJSON([
        'success' => true,
        'item' => [
            'id'         => $item['id'],
            'code'       => $item['code'],
            'name'       => $item['name'],
            'unit_price' => $lastPrice,
            'unit'       => $item['unit_name'] ?? ''
        ]
    ]);
}

/* =======================================================
 * ✅ AJAX: Search Items (used in Select2)
 * ======================================================= */
public function searchItemsAjax()
{
    $term = trim($this->request->getGet('q') ?? '');
    $db = \Config\Database::connect();

    $builder = $db->table('items');
    $builder->select('id, code, name, unit_price');

    if ($term !== '') {
        // 🔍 Filter when user types something
        $builder->groupStart()
            ->like('code', $term)
            ->orLike('name', $term)
        ->groupEnd();
    }

    // ✅ Show all (or top 50) items when term is empty
    $builder->orderBy('name', 'ASC')->limit(50);
    $results = $builder->get()->getResultArray();

    return $this->response->setJSON(['results' => $results]);
}

public function searchSuppliersAjax()
{
    $term = trim($this->request->getGet('q') ?? '');
    $db = \Config\Database::connect();

    $builder = $db->table('suppliers');
    $builder->select('id, name, contact_person, phone');

    if ($term !== '') {
        $builder->groupStart()
            ->like('name', $term)
            ->orLike('contact_person', $term)
            ->orLike('phone', $term)
        ->groupEnd();
    }

    $builder->orderBy('name', 'ASC')->limit(50);
    $results = $builder->get()->getResultArray();

    return $this->response->setJSON(['results' => $results]);
}

public function getItemByCode()
{
    if ($this->request->getMethod() !== 'post') {
        return $this->response->setStatusCode(405)
            ->setJSON(['success' => false, 'message' => 'Invalid method']);
    }

    $code = trim($this->request->getPost('code') ?? '');
    if ($code === '') {
        return $this->response->setJSON(['success' => false, 'message' => 'Missing code']);
    }

    $item = $this->itemModel
        ->select('id, code, name, unit_price')
        ->where('code', $code)
        ->first();

    if (!$item) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Item not found',
            'csrf' => [
                'name' => csrf_token(),
                'hash' => csrf_hash()
            ]
        ]);
    }

    return $this->response->setJSON([
        'success' => true,
        'item' => $item,
        'csrf' => [
            'name' => csrf_token(),
            'hash' => csrf_hash()
        ]
    ]);
}



}
