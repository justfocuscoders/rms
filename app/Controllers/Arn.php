<?php
namespace App\Controllers;

use App\Models\ArnModel;
use App\Models\PurchaseOrderModel;
use App\Models\ItemModel;
use App\Models\SupplierModel;
use CodeIgniter\Controller;

class Arn extends BaseController
{
    protected $arnModel;
    protected $poModel;
    protected $itemModel;
    protected $supplierModel;
    protected $data = [];

    public function __construct()
    {
        helper(['activity', 'form']);

        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->send();
        }

        $role = session()->get('role');
        if (!in_array($role, ['admin', 'warehouse', 'qc', 'procurement'])) {
            return redirect()->to('dashboard')->send();
        }

        $this->arnModel = new ArnModel();
        $this->poModel = new PurchaseOrderModel();
        $this->itemModel = new ItemModel();
        $this->supplierModel = new SupplierModel();
    }

    /** ✅ Default redirect */
    public function index()
    {
        return redirect()->to('/arn/list');
    }

    /** ✅ List */
    public function list()
    {
        $this->data['title'] = 'ARN List';
        $this->data['breadcrumbs'] = [
            ['title' => 'Dashboard', 'link' => site_url('dashboard')],
            ['title' => 'ARN List']
        ];

        $this->data['arns'] = $this->arnModel->getAllWithDetails();

        return view('arn/list', $this->data);
    }

    /** ✅ Create form */
    public function create()
    {
        $this->data['title'] = 'Create ARN';
        $this->data['breadcrumbs'] = [
            ['title' => 'Dashboard', 'link' => site_url('dashboard')],
            ['title' => 'ARN', 'link' => site_url('arn/list')],
            ['title' => 'Create ARN']
        ];

        $this->data['arn_no'] = $this->arnModel->generateArnNo();
        $this->data['pos'] = $this->poModel->findAll();
        $this->data['items'] = $this->itemModel->findAll();
        $this->data['suppliers'] = $this->supplierModel->findAll();
        $this->data['arn'] = null;

        return view('arn/form', $this->data);
    }

    /** ✅ Edit form */
    public function edit($id)
    {
        $arn = $this->arnModel->find($id);
        if (!$arn) {
            return redirect()->to('/arn/list')->with('error', 'ARN not found');
        }

        $this->data['title'] = 'Edit ARN';
        $this->data['breadcrumbs'] = [
            ['title' => 'Dashboard', 'link' => site_url('dashboard')],
            ['title' => 'ARN', 'link' => site_url('arn/list')],
            ['title' => 'Edit ARN']
        ];

        $this->data['arn'] = $arn;
        $this->data['arn_no'] = $arn['arn_no'];
        $this->data['pos'] = $this->poModel->findAll();
        $this->data['items'] = $this->itemModel->findAll();
        $this->data['suppliers'] = $this->supplierModel->findAll();

        return view('arn/form', $this->data);
    }

    /** ✅ Save (Create / Update single ARN) */
    public function save()
    {
        $id = $this->request->getPost('id');

        $rules = [
            'po_id'         => 'required|integer',
            'item_id'       => 'required|integer',
            'supplier_id'   => 'required|integer',
            'received_qty'  => 'required|numeric',
            'batch_no'      => 'required',
            'received_date' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'po_id'         => $this->request->getPost('po_id'),
            'item_id'       => $this->request->getPost('item_id'),
            'arn_no'        => $this->request->getPost('arn_no'),
            'batch_no'      => $this->request->getPost('batch_no'),
            'supplier_id'   => $this->request->getPost('supplier_id'),
            'received_qty'  => $this->request->getPost('received_qty'),
            'uom'           => $this->request->getPost('uom'),
            'received_date' => $this->request->getPost('received_date'),
            'expiry_date'   => $this->request->getPost('expiry_date'),
            'status'        => 'Pending',
            'created_by'    => session()->get('user_id'),
        ];

        if ($id) {
            $this->arnModel->update($id, $data);
            log_activity('Updated ARN ' . $data['arn_no']);
            return redirect()->to('/arn/list')->with('success', 'ARN updated successfully.');
        } else {
            $this->arnModel->save($data);
            log_activity('Created ARN ' . $data['arn_no']);
            return redirect()->to('/arn/list')->with('success', 'ARN created successfully.');
        }
    }

    /** ✅ View ARN Details */
    public function view($id)
    {
        $arn = $this->arnModel->getWithRelations($id);
        if (!$arn) {
            return redirect()->to('/arn/list')->with('error', 'ARN not found');
        }

        $this->data['title'] = 'View ARN';
        $this->data['breadcrumbs'] = [
            ['title' => 'Dashboard', 'link' => site_url('dashboard')],
            ['title' => 'ARN', 'link' => site_url('arn/list')],
            ['title' => 'View ARN']
        ];
        $this->data['arn'] = $arn;

        return view('arn/view', $this->data);
    }

    /** ✅ Delete ARN */
    public function delete($id)
    {
        $arn = $this->arnModel->find($id);
        if (!$arn) {
            return redirect()->to('/arn/list')->with('error', 'ARN not found');
        }

        $this->arnModel->delete($id);
        log_activity('Deleted ARN ID ' . $id);

        return redirect()->to('/arn/list')->with('success', 'ARN deleted successfully.');
    }

    /** ✅ Status update (optional external use) */
    public function updateStatus($id, $status)
    {
        $allowed = ['Pending', 'Converted', 'Rejected'];
        if (!in_array($status, $allowed)) {
            return redirect()->back()->with('error', 'Invalid status.');
        }

        $this->arnModel->update($id, ['status' => $status]);
        log_activity("ARN ID {$id} status updated to {$status}");

        return redirect()->back()->with('success', "ARN status updated to {$status}");
    }

    // -----------------------------------------------------------------
    // 🧩 NEW: Bulk AJAX Save for ARN Creation
    // -----------------------------------------------------------------
    public function save_ajax()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $data = $this->request->getJSON(true);
        if (!$data) {
            return $this->response->setJSON(['success' => false, 'message' => 'No data received']);
        }

        // ✅ Validate required keys
        $required = ['po_id', 'supplier_id', 'item_id', 'received_qty'];
        foreach ($required as $field) {
            if (empty($data[$field]) && $data[$field] !== '0') {
                return $this->response->setJSON(['success' => false, 'message' => "$field is required"]);
            }
        }

        // ✅ Build record
        $record = [
            'po_id'         => $data['po_id'],
            'item_id'       => $data['item_id'],
            'supplier_id'   => $data['supplier_id'],
            'batch_no'      => $data['batch_no'] ?? null,
            'mfg_date'      => $data['mfg_date'] ?? null,
            'expiry_date'   => $data['expiry_date'] ?? null,
            'received_qty'  => $data['received_qty'],
            'uom'           => $data['uom'] ?? null,
            'received_date' => date('Y-m-d'),
            'status'        => 'Pending',
            'arn_no'        => $this->generateArnNo(),
            'created_by'    => session()->get('user_id'),
        ];

        try {
            $id = $this->arnModel->insert($record);
            if ($id) {
                log_activity('Bulk Created ARN ' . $record['arn_no']);
                return $this->response->setJSON(['success' => true, 'arn_id' => $id, 'arn_no' => $record['arn_no']]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to insert']);
            }
        } catch (\Exception $e) {
            log_message('error', 'ARN save_ajax error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Server error']);
        }
    }

    /** 🔢 Helper: Generate unique ARN number */
    protected function generateArnNo()
    {
        $prefix = 'ARN';
        $date = date('Ymd');
        $db = \Config\Database::connect();
        $row = $db->query("SELECT MAX(id) as max_id FROM arn")->getRowArray();
        $next = ($row['max_id'] ?? 0) + 1;
        return sprintf('%s-%s-%04d', $prefix, $date, $next);
    }
}
