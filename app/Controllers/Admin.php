<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\GrnModel;
use App\Models\QcResultModel;
use App\Models\MrsModel;
use App\Models\StockModel;
use App\Models\SupplierModel;
use App\Models\PurchaseOrderModel;
use App\Models\DepartmentModel;

class Admin extends BaseController
{
    public function __construct()
    {
        helper('activity'); // ✅ ensures log_activity() is always available
    }

    /**
     * ------------------------------
     * Admin Dashboard
     * ------------------------------
     * Displays system-wide metrics.
     */
    public function dashboard()
{
    helper(['number', 'date']);

    // Initialize models
    $userModel       = new UserModel();
    $roleModel       = new RoleModel();
    $grnModel        = new GrnModel();
    $qcModel         = new QcResultModel();
    $mrsModel        = new MrsModel();
    $stockModel      = new StockModel();
    $supplierModel   = new SupplierModel();
    $poModel         = new PurchaseOrderModel();
    $departmentModel = new DepartmentModel();

    // --- KPI COUNTS ---
    $data = [
        'total_users'       => $userModel->countAllResults(),
        'total_pos'         => $poModel->countAllResults(),
        'total_mrs'         => $mrsModel->countAllResults(),
        'total_grn'         => $grnModel->countAllResults(),
        'total_suppliers'   => $supplierModel->countAllResults(),
        'total_stock_items' => $stockModel->countAllResults(),
    ];

    // --- Pending items ---
    $data['pending_po']  = $poModel->where('status', 'Pending')->countAllResults();
    $data['pending_mrs'] = $mrsModel->where('status', 'Pending')->countAllResults();
    $data['qc_pending']  = $qcModel->where('qc_status', 'Pending')->countAllResults();

    // --- Low stock ---
    $data['low_stock_count'] = $stockModel
        ->where('qty_available < reorder_level', null, false)
        ->countAllResults();

    // --- Recent records ---
    $data['recent_pos'] = $poModel
        ->select('po_number, status, created_at')
        ->orderBy('id', 'DESC')
        ->limit(5)
        ->findAll();

    $data['recent_mrs'] = $mrsModel
    ->select('mrs_no, status, created_at')
    ->orderBy('id', 'DESC')
    ->limit(5)
    ->findAll();



    $data['recent_grn'] = $grnModel
    ->select('grn_no, po_id, supplier_id, status, grn_date')
    ->orderBy('id', 'DESC')
    ->limit(5)
    ->findAll();


    // --- Chart placeholders (prevent undefined JSON errors) ---
    $data += [
        'stock_chart'     => [],
        'po_trend'        => [],
        'monthly_stats'   => [],
        'pending_status'  => [],
        'top_suppliers'   => [],
        'stock_value'     => [],
        'dept_mrs'        => [],
        'role_breakdown'  => [],
        'activities'      => [],
    ];

    // --- Breadcrumbs ---
    $data['breadcrumbs'] = [
        ['title' => 'Home', 'url' => '/dashboard'],
        ['title' => 'Admin Dashboard'],
    ];
    $data['title'] = 'Admin Dashboard';

    return view('dashboard/admin', $data);
}



    /**
     * ------------------------------
     * User Management
     * ------------------------------
     */
    public function users()
    {
        $userModel = new UserModel();

        $data['pending'] = $userModel
            ->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.status', 0)
            ->findAll();

        $data['active'] = $userModel
            ->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.status', 1)
            ->findAll();

        $roleModel = new RoleModel();
        $data['roles'] = $roleModel->findAll();

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/admin/dashboard'],
            ['title' => 'User Management']
        ];

        // ❌ Removed incorrect log line here
        return view('admin/users', $data);
    }

    /**
     * ✅ Approve a pending user
     */
    public function approve($id)
    {
        $userModel = new UserModel();
        $userModel->update($id, ['status' => 1]);

        // ✅ Log correct event
        log_activity('Approved user account', 'users', $id);

        return redirect()->back()->with('success', 'User approved successfully');
    }

    /**
     * ✅ Reject and delete a pending user
     */
    public function reject($id)
    {
        $userModel = new UserModel();
        $userModel->delete($id);

        log_activity('Rejected user account', 'users', $id);

        return redirect()->back()->with('success', 'User rejected and removed');
    }

    /**
     * ✅ Change a user’s role
     */
    public function changeRole($id)
    {
        $roleId = $this->request->getPost('role_id');
        $userModel = new UserModel();
        $userModel->update($id, ['role_id' => $roleId]);

        log_activity('Changed user role', 'users', $id);

        return redirect()->back()->with('success', 'Role updated successfully');
    }
    
    public function checkNewSignups()
{
    $db = \Config\Database::connect();
    $query = $db->query("SELECT COUNT(*) as pending FROM users WHERE status = 0");
    $result = $query->getRow();

    return $this->response->setJSON(['pending' => $result->pending]);
}

}
