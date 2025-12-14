<?php
namespace App\Controllers;

use App\Models\MrsModel;
use App\Models\MrsDetailModel;
use App\Models\DepartmentModel;
use App\Models\UserModel;
use App\Models\ItemModel;
use App\Models\StockMovementModel;

class Mrs extends BaseController
{
    protected $mrsModel;
    protected $mrsDetailModel;
    protected $departmentModel;
    protected $userModel;
    protected $itemModel;
    protected $stockMovementModel;

    public function __construct()
    {
        helper(['form', 'url', 'activity']); // ✅ includes logging + URL helpers
        $this->mrsModel = new MrsModel();
        $this->mrsDetailModel = new MrsDetailModel();
        $this->departmentModel = new DepartmentModel();
        $this->userModel = new UserModel();
        $this->itemModel = new ItemModel();
        $this->stockMovementModel = new StockMovementModel();
    }

    /** -------------------------------------------------
     *  ✅ Role Authorization Helper
     * ------------------------------------------------- */
    private function authorize($allowedRoles = [])
    {
        $role = strtolower(session()->get('role') ?? session()->get('role_name') ?? '');
        $allowedRoles = array_map('strtolower', $allowedRoles);

        if (!in_array($role, $allowedRoles)) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
        return null;
    }

    /** -------------------------------------------------
     *  Redirect to MRS List
     * ------------------------------------------------- */
    public function index()
    {
        return redirect()->to('/mrs/list');
    }
    
    public function generateMrsNo(): string
{
    // Format: MRS-YYYY-MM-0001
    $year = date('Y');
    $month = date('m');
    $prefix = "MRS-$year-$month";

    $last = $this->where('mrs_no LIKE', "$prefix%")
                 ->orderBy('id', 'DESC')
                 ->first();

    if ($last && preg_match('/(\d+)$/', $last['mrs_no'], $matches)) {
        $next = intval($matches[1]) + 1;
    } else {
        $next = 1;
    }

    return sprintf('%s-%04d', $prefix, $next);
}



    /** -------------------------------------------------
     *  MRS List Page
     * ------------------------------------------------- */
    public function list()
    {
        if ($redirect = $this->authorize(['admin', 'store', 'production'])) return $redirect;

        $role = session()->get('role');
        $userId = session()->get('id');

        $query = $this->mrsModel
            ->select('mrs.*, d.name as department, u.name as requested_by_name')
            ->join('departments d', 'd.id = mrs.department_id', 'left')
            ->join('users u', 'u.id = mrs.requested_by', 'left');

        if ($role === 'production') {
            $query->where('mrs.requested_by', $userId);
        }

        $data['mrs'] = $query->orderBy('mrs.id', 'DESC')->findAll();
        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'MRS List']
        ];

        return view('mrs/list', $data);
    }

    /** -------------------------------------------------
     *  MRS Form (Create/Edit)
     * ------------------------------------------------- */
    public function form($id = null)
    {
        if ($redirect = $this->authorize(['admin', 'production'])) return $redirect;

        $batchId = $this->request->getGet('batch_id');
        $batch = null;

        if ($id) {
            $data['mrs'] = $this->mrsModel->find($id);
            $data['details'] = $this->mrsDetailModel->where('mrs_id', $id)->findAll();
        } else {
            // ✅ Generate new MRS number in standard format
            $data['mrs'] = ['mrs_no' => $this->mrsModel->generateMrsNo()];
            $data['details'] = [];
        }

        if ($batchId) {
            $batchModel = new \App\Models\BatchModel();
            $batch = $batchModel->find($batchId);
        }

        // 🔹 Load all selectable items
        $data['items'] = $this->itemModel
            ->select('items.id, items.name, units.name AS uom')
            ->join('units', 'units.id = items.unit_id', 'left')
            ->orderBy('items.name', 'ASC')
            ->findAll();

        // 🔹 Available batches (optional)
        $db = \Config\Database::connect();
        $batches = $db->table('stock')
            ->select('DISTINCT(stock.batch_no), stock.item_id, items.name AS item_name')
            ->join('items', 'items.id = stock.item_id', 'left')
            ->where('stock.batch_no IS NOT NULL')
            ->orderBy('items.name', 'ASC')
            ->get()
            ->getResultArray();

        $batchMap = [];
        foreach ($batches as $b) {
            $batchMap[$b['item_id']][] = [
                'batch_no' => $b['batch_no'],
                'item_name' => $b['item_name']
            ];
        }

        $data['batchMap'] = $batchMap ?? [];
        $data['departments'] = $this->departmentModel->findAll();
        $data['users'] = $this->userModel->findAll();
        $data['batch'] = $batch;

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'MRS', 'url' => '/mrs/list'],
            ['title' => $id ? 'Edit MRS' : 'New MRS']
        ];

        return view('mrs/form', $data);
    }

    /** -------------------------------------------------
     *  Save (Create / Update) MRS
     * ------------------------------------------------- */
    public function save()
    {
        if ($redirect = $this->authorize(['admin', 'production'])) return $redirect;

        $post = $this->request->getPost();
        $id = $post['id'] ?? null;

        if (!$this->validate([
            'department_id' => 'required|integer',
            'requested_by'  => 'required|integer',
            'mrs_date'      => 'required|valid_date'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Please fill all required fields.');
        }

        // 🔹 Create or update MRS header
        if ($id) {
            $this->mrsModel->update($id, [
                'department_id' => $post['department_id'],
                'batch_id'      => $post['batch_id'] ?? null,
                'requested_by'  => $post['requested_by'],
                'mrs_date'      => $post['mrs_date'],
                'remarks'       => $post['remarks'] ?? null,
                'status'        => 'Submitted'
            ]);
            $mrsId = $id;
            $this->mrsDetailModel->where('mrs_id', $id)->delete();
            log_activity('Updated MRS', 'mrs', $mrsId);
        } else {
            $mrsId = $this->mrsModel->insert([
                'mrs_no'        => $this->mrsModel->generateMrsNo(), // ✅ clean format here
                'department_id' => $post['department_id'],
                'batch_id'      => $post['batch_id'] ?? null,
                'requested_by'  => $post['requested_by'],
                'mrs_date'      => $post['mrs_date'],
                'remarks'       => $post['remarks'] ?? null,
                'status'        => 'Submitted'
            ]);
            log_activity('Created MRS', 'mrs', $mrsId);
        }

        // 🔹 Insert MRS details
        if (!empty($post['item_id'])) {
            foreach ($post['item_id'] as $i => $itemId) {
                $qtyReq = (float)($post['qty_requested'][$i] ?? 0);
                if ($qtyReq <= 0) continue;

                $this->mrsDetailModel->insert([
                    'mrs_id'        => $mrsId,
                    'item_id'       => $itemId,
                    'batch_no'      => $post['batch_no'][$i] ?? null,
                    'qty_requested' => $qtyReq,
                    'uom'           => $post['uom'][$i] ?? null,
                    'remarks'       => $post['item_remarks'][$i] ?? null,
                    'status'        => 'Pending'
                ]);
            }
        }

        return redirect()->to('/mrs/list')->with('success', 'MRS submitted successfully.');
    }

    /** -------------------------------------------------
     *  View / Approve / Reject / Issue
     * ------------------------------------------------- */
    public function view($id)
    {
        if ($redirect = $this->authorize(['admin', 'store', 'production'])) return $redirect;

        $data['mrs'] = $this->mrsModel->getMrsWithDetails($id);
        if (!$data['mrs']) {
            return redirect()->to('/mrs/list')->with('error', 'MRS not found.');
        }

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'MRS', 'url' => '/mrs/list'],
            ['title' => 'View MRS']
        ];

        return view('mrs/view', $data);
    }

    public function approve($id)
    {
        if ($redirect = $this->authorize(['admin', 'store'])) return $redirect;
        $this->mrsModel->update($id, ['status' => 'Approved']);
        log_activity('Approved MRS', 'mrs', $id);
        return redirect()->back()->with('success', 'MRS approved successfully.');
    }

    public function reject($id)
    {
        if ($redirect = $this->authorize(['admin', 'store'])) return $redirect;
        $this->mrsModel->update($id, ['status' => 'Rejected']);
        log_activity('Rejected MRS', 'mrs', $id);
        return redirect()->back()->with('success', 'MRS rejected successfully.');
    }

    public function issue($id)
    {
        if ($redirect = $this->authorize(['admin', 'store'])) return $redirect;

        $post = $this->request->getPost();
        $mrs = $this->mrsModel->getMrsWithDetails($id);
        if (!$mrs) return redirect()->back()->with('error', 'MRS not found.');

        foreach ($mrs['details'] as $detail) {
            $issued = (float)($post['qty_issued'][$detail['id']] ?? 0);
            $itemRemark = trim($post['item_remarks'][$detail['id']] ?? '');
            if ($issued <= 0) continue;

            $requested = (float)$detail['qty_requested'];
            if ($issued > $requested) {
                return redirect()->back()->with('error', 'Cannot issue more than requested for item: ' . esc($detail['item_name']));
            }

            if ($issued < $requested && empty($itemRemark)) {
                return redirect()->back()->with('error', 'Please provide a remark for partial issue of item: ' . esc($detail['item_name']));
            }

            $this->mrsDetailModel->update($detail['id'], [
                'qty_issued' => $issued,
                'status'     => 'Issued',
                'remarks'    => $itemRemark ?: $detail['remarks'],
                'remarked_by'=> session()->get('name'),
                'remarked_at'=> date('Y-m-d H:i:s')
            ]);
        }

        $this->mrsModel->update($id, ['status' => 'Issued']);
        log_activity('Issued MRS', 'mrs', $id);
        return redirect()->back()->with('success', 'Items issued successfully.');
    }
}
