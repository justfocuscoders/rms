<?php
namespace App\Controllers;

use App\Models\GrnModel;
use App\Models\QcResultModel;
use App\Models\StockModel;
use App\Models\MrsModel;
use App\Models\UserModel;
use App\Models\UserSessionModel;
use App\Models\SupplierModel;
use App\Models\DashboardModel;
use CodeIgniter\Controller;

class Dashboard extends BaseController
{
    protected $db;
    protected $dashboardModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->dashboardModel = new DashboardModel();
    }

    public function index()
    {
        // ✅ Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $role   = strtolower(session('role_name'));
        $userId = session('user_id');
        $db     = $this->db;

        $data = ['title' => ucfirst($role) . ' Dashboard'];

        switch ($role) {
            // =====================================================
            // 🧭 ADMIN DASHBOARD
            // =====================================================
            case 'admin':

                // 🔹 Departments for filter dropdown
                $data['departments'] = $db->table('departments')
                    ->select('id, name')
                    ->orderBy('name', 'ASC')
                    ->get()
                    ->getResultArray();

                // 🔹 Summary Cards
                $data['total_pos'] = $db->table('purchase_orders')->countAllResults();
                $data['total_mrs'] = $db->table('mrs')->countAllResults();
                $data['total_grn'] = $db->table('grn')->countAllResults();
                $data['total_users'] = $db->table('users')->countAllResults();
                $data['total_suppliers'] = $db->table('suppliers')->countAllResults();
                $data['total_stock_items'] = $db->table('stock')->countAllResults();

                // 🔹 Alerts
                $data['low_stock_count'] = $db->table('stock')->where('qty_available <', 10)->countAllResults();
                $data['pending_mrs'] = $db->table('mrs')->where('status', 'Pending')->countAllResults();
                $data['pending_po'] = $db->table('purchase_orders')->where('status', 'Pending')->countAllResults();
                $data['qc_pending'] = $db->table('qc_results')->where('qc_status', 'Pending')->countAllResults();

                // 🔹 Charts
                $data['stock_chart'] = $db->query("
                    SELECT i.name AS item_name, s.qty_available 
                    FROM stock s
                    JOIN items i ON s.item_id = i.id
                    ORDER BY s.qty_available DESC
                    LIMIT 8
                ")->getResultArray();

                $data['po_trend'] = $db->query("
                    SELECT DATE_FORMAT(created_at, '%b %Y') AS month, COUNT(*) AS total
                    FROM purchase_orders
                    GROUP BY month
                    ORDER BY created_at DESC
                    LIMIT 6
                ")->getResultArray();

                // 🔹 Recent Tables
                $data['recent_pos'] = $db->table('purchase_orders')
                    ->select('po_number, status, created_at')
                    ->orderBy('created_at', 'DESC')
                    ->limit(5)
                    ->get()
                    ->getResultArray();

                $data['recent_mrs'] = $db->table('mrs')
    ->select('mrs_no, status, created_at') // ✅ Remove alias
    ->orderBy('created_at', 'DESC')
    ->limit(5)
    ->get()
    ->getResultArray();


                $data['recent_grn'] = $db->table('grn')
                    ->select('grn_no, po_id, supplier_id, grn_date, status')
                    ->orderBy('created_at', 'DESC')
                    ->limit(5)
                    ->get()
                    ->getResultArray();

                // 🔹 Analytics & Insights
                $data['dept_mrs'] = $db->query("
                    SELECT d.name AS department, COUNT(m.id) AS total
                    FROM mrs m
                    JOIN departments d ON m.department_id = d.id
                    GROUP BY d.name
                    ORDER BY total DESC
                    LIMIT 6
                ")->getResultArray();

                $data['role_breakdown'] = $db->query("
                    SELECT r.name AS role, COUNT(u.id) AS total
                    FROM users u
                    JOIN roles r ON u.role_id = r.id
                    GROUP BY r.name
                ")->getResultArray();

                $data['activities'] = $db->query("
                    SELECT 
                        CONCAT(u.name, ' ', a.action) AS action, 
                        DATE_FORMAT(a.created_at, '%b %d, %H:%i') AS time,
                        u.name AS user
                    FROM activity_log a
                    JOIN users u ON a.user_id = u.id
                    ORDER BY a.created_at DESC
                    LIMIT 6
                ")->getResultArray();

                $data['monthly_stats'] = $db->query("
                    SELECT DATE_FORMAT(created_at, '%b') AS month,
                        COUNT(CASE WHEN type = 'MRS' THEN 1 END) AS mrs,
                        COUNT(CASE WHEN type = 'PO' THEN 1 END) AS po,
                        COUNT(CASE WHEN type = 'GRN' THEN 1 END) AS grn
                    FROM (
                        SELECT 'MRS' AS type, created_at FROM mrs
                        UNION ALL
                        SELECT 'PO' AS type, created_at FROM purchase_orders
                        UNION ALL
                        SELECT 'GRN' AS type, created_at FROM grn
                    ) combined
                    GROUP BY month
                    ORDER BY STR_TO_DATE(month, '%b')
                ")->getResultArray();

                $data['pending_status'] = [
                    'PO Pending' => $db->table('purchase_orders')->where('status', 'Pending')->countAllResults(),
                    'PO Approved' => $db->table('purchase_orders')->where('status', 'Approved')->countAllResults(),
                    'GRN Pending' => $db->table('grn')->where('status', 'Pending')->countAllResults(),
                    'GRN Closed' => $db->table('grn')->where('status', 'Closed')->countAllResults(),
                ];

                $data['top_suppliers'] = $db->query("
                    SELECT s.name AS supplier, COUNT(g.id) AS total_grn
                    FROM grn g
                    JOIN suppliers s ON s.id = g.supplier_id
                    GROUP BY s.name
                    ORDER BY total_grn DESC
                    LIMIT 5
                ")->getResultArray();

                $data['stock_value'] = $db->query("
                    SELECT c.name AS category, SUM(s.qty_available * i.unit_price) AS value
                    FROM stock s
                    JOIN items i ON s.item_id = i.id
                    JOIN categories c ON i.category_id = c.id
                    GROUP BY c.name
                ")->getResultArray();

                return view('dashboard/admin', $data);

            // =====================================================
            // 🧪 QC DASHBOARD
            // =====================================================
            case 'qc':
                $data['pending_samples']  = $db->table('qc_results')->where('qc_status', 'Pending')->countAllResults();
                $data['approved_batches'] = $db->table('qc_results')->where('qc_status', 'Accepted')->countAllResults();
                $data['rejected_batches'] = $db->table('qc_results')->where('qc_status', 'Rejected')->countAllResults();
                $data['recent_qc'] = $db->table('qc_results')->select('id, qc_status, created_at')->orderBy('created_at', 'DESC')->limit(5)->get()->getResultArray();

                return view('dashboard/qc', $data);

            // =====================================================
            // 🏭 PRODUCTION DASHBOARD
            // =====================================================
            case 'production':
                $data['my_open_mrs'] = $db->table('mrs')->where('requested_by', $userId)->where('status', 'Pending')->countAllResults();
                $data['issued_mrs']  = $db->table('mrs')->where('requested_by', $userId)->where('status', 'Issued')->countAllResults();
                $data['partial_mrs'] = $db->table('mrs')->where('requested_by', $userId)->where('status', 'Partially Issued')->countAllResults();
                $data['recent_mrs']  = $db->table('mrs')->where('requested_by', $userId)->orderBy('created_at', 'DESC')->limit(5)->get()->getResultArray();

                return view('dashboard/production', $data);

            // =====================================================
            // 🏬 STORE DASHBOARD
            // =====================================================
            case 'store':
                $data['total_items']  = $db->table('stock')->countAllResults();
                $data['total_qty']    = $db->table('stock')->selectSum('qty_available')->get()->getRow()->qty_available ?? 0;
                $data['movements']    = $db->table('stock_movements')->orderBy('moved_at', 'DESC')->limit(5)->get()->getResultArray();
                $data['expiry_alerts'] = $db->table('stock')
                    ->select('batch_no, expiry_date, qty_available')
                    ->where('expiry_date IS NOT NULL')
                    ->where('expiry_date <=', date('Y-m-d', strtotime('+60 days')))
                    ->where('qty_available >', 0)
                    ->orderBy('expiry_date', 'ASC')
                    ->limit(5)
                    ->get()
                    ->getResultArray();

                return view('dashboard/store', $data);

            // =====================================================
            // 🛒 PROCUREMENT DASHBOARD
            // =====================================================
            case 'procurement':
                $data['total_pos'] = $db->table('purchase_orders')->countAllResults();
                $data['pending_pos'] = $db->table('purchase_orders')->where('status', 'Pending')->countAllResults();
                $data['approved_pos'] = $db->table('purchase_orders')->where('status', 'Approved')->countAllResults();
                $data['received_pos'] = $db->table('purchase_orders')->where('status', 'Received')->countAllResults();
                $data['total_suppliers'] = $db->table('suppliers')->countAllResults();
                $data['total_grn'] = $db->table('grn')->countAllResults();
                $data['recent_pos'] = $db->table('purchase_orders')->select('po_number, supplier_id, status, created_at')->orderBy('created_at', 'DESC')->limit(5)->get()->getResultArray();
                $data['recent_grn'] = $db->table('grn')->select('grn_number, po_id, received_date, created_at')->orderBy('created_at', 'DESC')->limit(5)->get()->getResultArray();

                return view('dashboard/procurement', $data);

            // =====================================================
            // 🏗️ WAREHOUSE DASHBOARD
            // =====================================================
            case 'warehouse':
    // ✅ GRN Summary
    $data['total_grns']   = $db->table('grn')->countAllResults();
    $data['qc_pending']   = $db->table('grn')->where('status', 'QC Pending')->countAllResults();
    $data['qc_approved']  = $db->table('grn')->where('status', 'QC Approved')->countAllResults();
    $data['qc_rejected']  = $db->table('grn')->where('status', 'QC Rejected')->countAllResults();

    // ✅ Store / Inventory Summary
    $data['total_items']  = $db->table('stock')->countAllResults();
    $data['stock_total']  = $db->table('stock')->selectSum('qty_available')->get()->getRow()->qty_available ?? 0;
    $data['low_stock']    = $db->table('stock')->where('qty_available <', 10)->countAllResults();
    $data['open_mrs']     = $db->table('mrs')->where('status', 'Pending')->countAllResults();

    // ✅ Pending Verification (QC approved but not verified)
    $data['pending_verifications'] = $db->query("
    SELECT g.id, g.grn_no, i.name AS item_name, g.supplier_id, s.name AS supplier_name, 
           gr.qc_status, gr.remarks
    FROM grn g
    JOIN grn_details gd ON g.id = gd.grn_id
    JOIN qc_results gr ON gd.id = gr.grn_detail_id
    JOIN items i ON gd.item_id = i.id
    JOIN suppliers s ON s.id = g.supplier_id
    WHERE gr.qc_status = 'Accepted'
    ORDER BY g.created_at DESC
    LIMIT 10
")->getResultArray();



    $data['title'] = 'Warehouse Dashboard';

    return view('dashboard/warehouse', $data);


            // =====================================================
            // 🔒 DEFAULT
            // =====================================================
            default:
                return redirect()->to('/login');
        }
    }

    // =====================================================
    // 🕒 SERVER TIME ENDPOINT
    // =====================================================
    public function getServerTime()
    {
        return $this->response->setJSON([
            'time' => date('d M Y, h:i A')
        ]);
    }

    // =====================================================
    // ⚙️ FILTER ENDPOINT (AJAX)
    // =====================================================
    public function filter()
    {
        $period = $this->request->getGet('period');
        $dept   = $this->request->getGet('dept');
        $data   = $this->dashboardModel->getFilteredDashboardData($period, $dept);

        return $this->response->setJSON($data);
    }
}
