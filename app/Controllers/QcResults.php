<?php
namespace App\Controllers;

use App\Models\QcResultModel;
use App\Models\QcSessionModel;


class QcResults extends BaseController
{
    /** =====================================================
     *  ✅ Stage 1: GRN-wise QC Summary List
     * ===================================================== */
    public function grnList()
    {
        $db = \Config\Database::connect();

        $data['grn_qc_summary'] = $db->table('grn g')
            ->select("
                g.id AS grn_id,
                g.grn_no,
                g.created_at AS grn_date,
                s.name AS supplier_name,
                COUNT(gd.id) AS total_items,
                SUM(qr.id IS NOT NULL) AS tested_items,
                SUM(qr.qc_status = 'Accepted') AS accepted,
                SUM(qr.qc_status = 'Rejected') AS rejected,
                SUM(qr.qc_status = 'Pending' OR qr.qc_status IS NULL) AS pending
            ", false)
            ->join('suppliers s', 's.id = g.supplier_id', 'left')
            ->join('grn_details gd', 'gd.grn_id = g.id', 'left')
            ->join('qc_results qr', 'qr.grn_detail_id = gd.id', 'left')
            ->groupBy('g.id')
            ->orderBy('g.id', 'DESC')
            ->get()
            ->getResultArray();

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Quality Control by GRN']
        ];

        return view('qc/grn_list', $data);
    }

    /** =====================================================
     *  ✅ Stage 2: View Items under a GRN (QC Testing Page)
     * ===================================================== */
    public function grn($grn_id)
    {
        $db = \Config\Database::connect();

        // GRN Info
        $data['grn_info'] = $db->table('grn g')
            ->select('g.*, s.name AS supplier_name')
            ->join('suppliers s', 's.id = g.supplier_id', 'left')
            ->where('g.id', $grn_id)
            ->get()
            ->getRowArray();

        // Items + QC info
        $data['items'] = $db->table('grn_details gd')
            ->select("
                gd.id AS grn_detail_id,
                i.name AS item_name,
                gd.batch_no,
                gd.expiry_date,
                gd.qty_received,
                COALESCE(qr.qc_status, 'Pending') AS qc_status,
                qr.remarks,
                qr.id AS qc_id
            ", false)
            ->join('items i', 'i.id = gd.item_id', 'left')
            ->join('qc_results qr', 'qr.grn_detail_id = gd.id', 'left')
            ->where('gd.grn_id', $grn_id)
            ->orderBy('gd.id', 'ASC')
            ->get()
            ->getResultArray();

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Quality Control', 'url' => '/qc'],
            ['title' => 'GRN QC Test']
        ];

        return view('qc/grn_view', $data);
    }

    /** =====================================================
     *  ✅ QC Dashboard (Summary)
     * ===================================================== */
    public function dashboard()
    {
        $qcModel = new QcResultModel();

        $data['total']    = $qcModel->countAllResults();
        $data['accepted'] = $qcModel->where('qc_status', 'Accepted')->countAllResults();
        $data['rejected'] = $qcModel->where('qc_status', 'Rejected')->countAllResults();
        $data['pending']  = $qcModel->where('qc_status', 'Pending')->countAllResults();
        $qcModel->resetQuery();

        $data['recent'] = $qcModel
            ->select('qc_results.*, grn_details.grn_id, grn_details.qty_received, items.name AS item_name')
            ->join('grn_details', 'grn_details.id = qc_results.grn_detail_id')
            ->join('items', 'items.id = grn_details.item_id', 'left')
            ->orderBy('qc_results.tested_at', 'DESC')
            ->limit(10)
            ->find();

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Quality Control', 'url' => '/qc'],
            ['title' => 'Dashboard']
        ];

        return view('qc/dashboard', $data);
    }

    /** =====================================================
     *  ✅ QC Single Record View
     * ===================================================== */
    public function view($grnId)
    {
        $db = \Config\Database::connect();

        // Header Info
        $data['grn_info'] = $db->table('grn g')
            ->select('g.id, g.grn_no, g.created_at, s.name AS supplier_name')
            ->join('suppliers s', 's.id = g.supplier_id', 'left')
            ->where('g.id', $grnId)
            ->get()
            ->getRowArray();

        // Item Details
        $data['grn_items'] = $db->table('grn_details gd')
            ->select("
                i.name AS item_name, gd.batch_no, gd.expiry_date, gd.qty_received,
                qr.qc_status, qr.remarks,
                u.name AS tested_by_name, r.name AS tester_role, qr.tested_at
            ", false)
            ->join('items i', 'i.id = gd.item_id', 'left')
            ->join('qc_results qr', 'qr.grn_detail_id = gd.id', 'left')
            ->join('users u', 'u.id = qr.tested_by', 'left')
            ->join('roles r', 'r.id = u.role_id', 'left')
            ->where('gd.grn_id', $grnId)
            ->orderBy('gd.id', 'ASC')
            ->get()
            ->getResultArray();

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Quality Control', 'url' => '/qc'],
            ['title' => 'View Record']
        ];

        return view('qc/view', $data);
    }

    /** =====================================================
     *  ✅ QC Status Update (Form Submit)
     * ===================================================== */
    public function updateQc()
    {
        $qcModel = new QcResultModel();
        $qcData  = $this->request->getPost('qc');

        if (!empty($qcData) && is_array($qcData)) {
            $db = \Config\Database::connect();
            $builder = $db->table('qc_results');
            $testerId = session('user_id') ?? null;

            foreach ($qcData as $detail_id => $data) {
                $detail_id = (int)$detail_id;

                $updateData = [
                    'qc_status'  => $data['status'] ?? 'Pending',
                    'remarks'    => $data['remarks'] ?? '',
                    'tested_by'  => $testerId,
                    'tested_at'  => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $exists = $builder->where('grn_detail_id', $detail_id)->get()->getRowArray();
                if ($exists) {
                    $builder->where('grn_detail_id', $detail_id)->update($updateData);
                } else {
                    $updateData['grn_detail_id'] = $detail_id;
                    $updateData['created_at'] = date('Y-m-d H:i:s');
                    $builder->insert($updateData);
                }
            }
        }

        // Update GRN status if all items tested
        $grn_id = $this->request->getPost('grn_id');
        if ($grn_id) {
            $db = \Config\Database::connect();
            $total = (int) $db->table('grn_details')->where('grn_id', $grn_id)->countAllResults();

            $tested = (int) $db->table('qc_results qr')
                ->join('grn_details gd', 'gd.id = qr.grn_detail_id')
                ->where('gd.grn_id', $grn_id)
                ->whereIn('qr.qc_status', ['Accepted', 'Rejected'])
                ->countAllResults();

            $status = ($total == $tested) ? 'QC Completed' : 'QC In Progress';
            $db->table('grn')->where('id', $grn_id)->update(['status' => $status]);
        }

        return redirect()->to('/qc')
                 ->with('success', 'QC results updated successfully!');

    }

    /** =====================================================
     *  ✅ Refresh single GRN summary / data (AJAX)
     * ===================================================== */
    public function refreshData($grn_no)
    {
        $db = \Config\Database::connect();

        // Fetch items by GRN number
        $items = $db->table('grn_details gd')
            ->select('gd.*, i.name AS item_name, qr.qc_status, qr.remarks, qr.tested_by, qr.tested_at')
            ->join('items i', 'i.id = gd.item_id', 'left')
            ->join('grn g', 'g.id = gd.grn_id', 'left')
            ->join('qc_results qr', 'qr.grn_detail_id = gd.id', 'left')
            ->where('g.grn_no', $grn_no)
            ->get()
            ->getResultArray();

        $total = count($items);
        $accepted = count(array_filter($items, fn($i) => ($i['qc_status'] ?? '') === 'Accepted'));
        $rejected = count(array_filter($items, fn($i) => ($i['qc_status'] ?? '') === 'Rejected'));
        $pending = count(array_filter($items, fn($i) => ($i['qc_status'] ?? '') === 'Pending' || empty($i['qc_status'])));

        $tested = $accepted + $rejected;
        $completion = $total > 0 ? round(($tested / $total) * 100) : 0;
        $acceptedPercent = $total > 0 ? round(($accepted / $total) * 100) : 0;
        $rejectedPercent = $total > 0 ? round(($rejected / $total) * 100) : 0;
        $pendingPercent = $total > 0 ? round(($pending / $total) * 100) : 0;

        // Render table rows fresh (maintain existing HTML structure)
        $html = '';
        foreach ($items as $row) {
            $status = $row['qc_status'] ?? 'Pending';
            $badge = match($status) {
                'Accepted' => '<span class="badge bg-success">Accepted</span>',
                'Rejected' => '<span class="badge bg-danger">Rejected</span>',
                'Pending' => '<span class="badge bg-warning text-dark">Pending</span>',
                default => '<span class="badge bg-secondary">Not Tested</span>',
            };
            $html .= "<tr data-status='".esc($status)."'>".
                     "<td>".esc($row['item_name'])."</td>".
                     "<td>".esc($row['batch_no'])."</td>".
                     "<td>".esc($row['expiry_date'])."</td>".
                     "<td>".esc($row['qty_received'])."</td>".
                     "<td>{$badge}</td>".
                     "<td>".esc($row['remarks'])."</td>".
                     "<td>".esc($row['tested_by'])."</td>".
                     "<td>".esc($row['tested_at'])."</td>".
                     "</tr>";
        }

        return $this->response->setJSON([
            'total' => $total,
            'accepted' => $accepted,
            'rejected' => $rejected,
            'pending' => $pending,
            'completion' => $completion,
            'acceptedPercent' => $acceptedPercent,
            'rejectedPercent' => $rejectedPercent,
            'pendingPercent' => $pendingPercent,
            'html' => $html
        ]);
    }

    /** =====================================================
     *  ✅ Refresh GRN list (AJAX)
     * ===================================================== */
    public function refreshGrnList()
    {
        $db = \Config\Database::connect();

        $rows = $db->table('grn g')
            ->select("
                g.id AS grn_id,
                g.grn_no,
                g.created_at AS grn_date,
                s.name AS supplier_name,
                COUNT(gd.id) AS total_items,
                SUM(qr.id IS NOT NULL) AS tested_items,
                SUM(qr.qc_status = 'Accepted') AS accepted,
                SUM(qr.qc_status = 'Rejected') AS rejected,
                SUM(qr.qc_status = 'Pending' OR qr.qc_status IS NULL) AS pending
            ", false)
            ->join('suppliers s', 's.id = g.supplier_id', 'left')
            ->join('grn_details gd', 'gd.grn_id = g.id', 'left')
            ->join('qc_results qr', 'qr.grn_detail_id = gd.id', 'left')
            ->groupBy('g.id')
            ->orderBy('g.id', 'DESC')
            ->get()
            ->getResultArray();

        $html = '';
        $pendingList = [];
        $grnIds = [];

        foreach ($rows as $row) {
            $grn_id = (int)$row['grn_id'];
            $grn_no = $row['grn_no'];
            $grn_date = $row['grn_date'];

            $total = (int)$row['total_items'];
            $accepted = (int)$row['accepted'];
            $rejected = (int)$row['rejected'];
            $pending = (int)$row['pending'];
            $tested = $accepted + $rejected;
            $completion = $total > 0 ? round(($tested / $total) * 100) : 0;
            $accPct = $total > 0 ? round(($accepted / $total) * 100) : 0;
            $rejPct = $total > 0 ? round(($rejected / $total) * 100) : 0;
            $pendPct = $total > 0 ? round(($pending / $total) * 100) : 0;

            $html .= '<tr data-grn-id="'.esc($grn_id).'" data-pending="'. $pending .'" data-completion="'. $completion .'">';
            $html .= '<td><strong>'.esc($grn_no).'</strong></td>';
            $html .= '<td>'.esc($row['supplier_name']).'</td>';
            $html .= '<td>'.date('d M Y', strtotime($grn_date)).'</td>';
            $html .= '<td class="text-center">'.$total.'</td>';
            $html .= '<td class="text-center">'.$tested.'</td>';
            $html .= '<td class="text-center text-success">'.$accepted.'</td>';
            $html .= '<td class="text-center text-danger">'.$rejected.'</td>';
            $html .= '<td class="text-center text-warning">'.$pending.'</td>';
            $html .= '<td style="min-width:220px;">
                        <div class="progress mb-1" style="height:12px;">
                          <div class="progress-bar bg-success" role="progressbar" style="width:'.$accPct.'%"></div>
                          <div class="progress-bar bg-danger" role="progressbar" style="width:'.$rejPct.'%"></div>
                          <div class="progress-bar bg-secondary" role="progressbar" style="width:'.$pendPct.'%"></div>
                        </div>
                        <small class="text-muted">'.$completion.'% tested</small>
                      </td>';
            $html .= '<td class="text-center">';
            if ($pending > 0) {
                $html .= '<a href="'.base_url('/qc/test/'.$grn_id).'" class="btn btn-sm btn-outline-warning me-1"><i class="bx bxs-flask"></i> Test Now</a>';
            }
            $html .= '<a href="'.base_url('/qc/view/'.$grn_id).'" class="btn btn-sm btn-outline-info"><i class="bx bx-show"></i> View</a>';
            $html .= '</td>';
            $html .= '</tr>';

            if ($pending > 0) {
                $pendingList[] = ['grn_no' => $grn_no, 'grn_id' => $grn_id, 'created_at' => $grn_date];
            }

            $grnIds[] = ['grn_id' => $grn_id, 'grn_no' => $grn_no, 'created_at' => $grn_date];
        }

        return $this->response->setJSON([
            'html' => $html,
            'pendingList' => $pendingList,
            'grnIds' => $grnIds
        ]);
    }

    /** =====================================================
     *  ✅ Test page (shows items and existing QC for a GRN)
     * ===================================================== */
    public function test($grn_id)
    {
        $db = \Config\Database::connect();

        // Get GRN main info
        $grn_info = $db->table('grn g')
            ->select('g.id, g.grn_no, g.created_at, s.name AS supplier_name')
            ->join('suppliers s', 's.id = g.supplier_id', 'left')
            ->where('g.id', $grn_id)
            ->get()
            ->getRowArray();

        if (!$grn_info) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('GRN not found');
        }

        $sessionModel = new QcSessionModel();
$userId = session('user_id');

// Fetch existing QC session for this GRN
$qcSession = $sessionModel
    ->where('grn_id', $grn_id)
    ->first();

if ($qcSession) {

    // QC already completed → read-only allowed (future enhancement)
    if ($qcSession['status'] === 'COMPLETED') {
        // Allow view; do not block
    }

    // Someone else is testing
    if ($qcSession['qc_user_id'] != $userId && $qcSession['status'] === 'IN_PROGRESS') {
        return redirect()->to('/qc')
            ->with('error', 'QC is currently in progress by another user.');
    }

    // Same user resumes
    $sessionModel->update($qcSession['id'], [
        'last_activity_at' => date('Y-m-d H:i:s')
    ]);

} else {

    // First time QC start
    $sessionModel->insert([
        'grn_id' => $grn_id,
        'qc_user_id' => $userId,
        'status' => 'IN_PROGRESS',
        'started_at' => date('Y-m-d H:i:s'),
        'last_activity_at' => date('Y-m-d H:i:s')
    ]);
}


        // Get GRN item details and existing QC data
        $grn_items = $db->table('grn_details gd')
            ->select('gd.id AS grn_detail_id, i.name AS item_name, gd.batch_no, gd.expiry_date, gd.qty_received, qr.qc_status, qr.remarks')
            ->join('items i', 'i.id = gd.item_id', 'left')
            ->join('qc_results qr', 'qr.grn_detail_id = gd.id', 'left')
            ->where('gd.grn_id', $grn_id)
            ->orderBy('gd.id', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'grn_info' => $grn_info,
            'grn_items' => $grn_items,
        ];

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Quality Control', 'url' => '/qc'],
            ['title' => 'Test Now']
        ];

        return view('qc/test', $data);
    }

    /** =====================================================
     *  ✅ Update QC via AJAX (safe, builder-only)
     * ===================================================== */
    public function updateQcAjax()
{
    // Enforce POST
    if ($this->request->getMethod() !== 'post') {
        return $this->response
            ->setStatusCode(405)
            ->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
    }

    if (!$this->request->isAJAX() && !$this->request->getPost()) {
        return $this->response
            ->setStatusCode(400)
            ->setJSON(['success' => false, 'message' => 'Invalid request']);
    }

    $grn_id = (int) $this->request->getPost('grn_id');
    $qcData = $this->request->getPost('qc');
    $testerId = session('user_id');

    if (!$grn_id || empty($qcData) || !is_array($qcData)) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'No QC data received.'
        ]);
    }

    $db = \Config\Database::connect();

    /* =====================================================
       ✅ STEP 1: Validate QC SESSION (ownership check)
    ===================================================== */
    $sessionModel = new \App\Models\QcSessionModel();

    $qcSession = $sessionModel
        ->where('grn_id', $grn_id)
        ->first();

    if (!$qcSession) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'QC session not found. Please reopen QC.'
        ]);
    }

    if ($qcSession['qc_user_id'] != $testerId || $qcSession['status'] !== 'IN_PROGRESS') {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'You are not allowed to update this QC.'
        ]);
    }

    /* =====================================================
       ✅ STEP 2: SAVE ITEM-LEVEL QC (your existing logic)
    ===================================================== */
    $builder = $db->table('qc_results');

    foreach ($qcData as $detail_id => $data) {
        $detail_id = (int) $detail_id;

        $update = [
            'qc_status'  => $data['status'] ?? 'Pending',
            'remarks'    => $data['remarks'] ?? '',
            'tested_at'  => date('Y-m-d H:i:s'),
            'tested_by'  => $testerId,
            'updated_at'=> date('Y-m-d H:i:s')
        ];

        $exists = $builder
            ->where('grn_detail_id', $detail_id)
            ->get()
            ->getRowArray();

        if ($exists) {
            $builder
                ->where('grn_detail_id', $detail_id)
                ->update($update);
        } else {
            $update['grn_detail_id'] = $detail_id;
            $update['created_at'] = date('Y-m-d H:i:s');
            $builder->insert($update);
        }
    }

    /* =====================================================
       ✅ STEP 3: Recompute QC Summary
    ===================================================== */
    $summaryRow = $db->table('qc_results qr')
        ->select("
            COUNT(qr.id) AS total,
            SUM(CASE WHEN qr.qc_status IN ('Accepted','Rejected') THEN 1 ELSE 0 END) AS tested,
            SUM(CASE WHEN qr.qc_status = 'Accepted' THEN 1 ELSE 0 END) AS accepted,
            SUM(CASE WHEN qr.qc_status = 'Rejected' THEN 1 ELSE 0 END) AS rejected,
            SUM(CASE WHEN qr.qc_status = 'Pending' THEN 1 ELSE 0 END) AS pending
        ", false)
        ->join('grn_details gd', 'gd.id = qr.grn_detail_id')
        ->where('gd.grn_id', $grn_id)
        ->get()
        ->getRowArray();

    $total      = (int) ($summaryRow['total'] ?? 0);
    $tested     = (int) ($summaryRow['tested'] ?? 0);
    $accepted   = (int) ($summaryRow['accepted'] ?? 0);
    $rejected   = (int) ($summaryRow['rejected'] ?? 0);
    $pending    = (int) ($summaryRow['pending'] ?? 0);
    $completion = $total > 0 ? round(($tested / $total) * 100) : 0;

    /* =====================================================
       ✅ STEP 4: Update QC SESSION + GRN STATUS
    ===================================================== */
    $sessionUpdate = [
        'last_activity_at' => date('Y-m-d H:i:s')
    ];

    if ($completion === 100) {
        $sessionUpdate['status'] = 'COMPLETED';
        $sessionUpdate['completed_at'] = date('Y-m-d H:i:s');
    }

    $sessionModel
        ->where('grn_id', $grn_id)
        ->update($sessionUpdate);

    $db->table('grn')
        ->where('id', $grn_id)
        ->update([
            'status' => $completion === 100 ? 'QC Completed' : 'QC In Progress'
        ]);

    /* =====================================================
       ✅ STEP 5: RESPONSE
    ===================================================== */
    return $this->response->setJSON([
        'success' => true,
        'message' => 'QC results saved successfully!',
        'completion' => $completion,
        'summary' => [
            'total' => $total,
            'tested' => $tested,
            'accepted' => $accepted,
            'rejected' => $rejected,
            'pending' => $pending,
            'status' => $completion === 100 ? 'QC Completed' : 'QC In Progress'
        ]
    ]);
}


    /** =====================================================
     *  ✅ Edit single QC record (form)
     * ===================================================== */
    public function edit($qc_id)
    {
        $db = \Config\Database::connect();

        $qc = $db->table('qc_results')->where('id', $qc_id)->get()->getRowArray();
        if (!$qc) throw new \CodeIgniter\Exceptions\PageNotFoundException('QC record not found');

        $grnDetail = $db->table('grn_details gd')
            ->select('gd.*, g.grn_no, i.name AS item_name')
            ->join('grn g', 'g.id = gd.grn_id')
            ->join('items i', 'i.id = gd.item_id')
            ->where('gd.id', $qc['grn_detail_id'])
            ->get()
            ->getRowArray();

        $users = $db->table('users')->select('id, name')->get()->getResultArray();

        $data = [
            'qc' => $qc,
            'grnDetail' => $grnDetail,
            'users' => $users,
            'action' => base_url('/qc/updateSingle/' . $qc_id)
        ];

        return view('qc/form', $data);
    }
}
