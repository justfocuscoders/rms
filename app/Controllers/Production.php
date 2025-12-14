<?php
namespace App\Controllers;

use App\Models\ProductionModel;
use App\Models\MrsModel;
use Config\Database;
use CodeIgniter\Exceptions\PageNotFoundException;

class Production extends BaseController
{
    public function __construct()
    {
        helper('activity'); // ✅ ensures log_activity() always available
    }

    /** =====================================================
     *  🏭 Production Dashboard
     * ===================================================== */
    public function dashboard()
    {
        $db = \Config\Database::connect();

        // Quick stats
        $data['total_batches']    = $db->table('batches')->countAllResults();
        $data['pending_batches']  = $db->table('batches')->where('status', 'Pending')->countAllResults();
        $data['completed_batches'] = $db->table('batches')->where('status', 'Completed')->countAllResults();

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Production Dashboard']
        ];

        return view('dashboard/production', $data);
    }

    /** =====================================================
     *  🏭 List All Production Batches
     * ===================================================== */
    public function index()
    {
        $db = Database::connect();

        $data['batches'] = $db->table('production_batches pb')
            ->select("
                pb.id AS batch_id,
                pb.batch_no,
                pb.product_name,
                pb.planned_qty,
                pb.uom,
                pb.start_date,
                pb.end_date,
                pb.status,
                COUNT(m.id) AS total_mrs,
                SUM(m.status = 'Issued') AS issued_mrs,
                SUM(m.status = 'Pending') AS pending_mrs
            ")
            ->join('mrs m', 'm.batch_id = pb.id', 'left')
            ->groupBy('pb.id')
            ->orderBy('pb.id', 'DESC')
            ->get()
            ->getResultArray();

        $data['breadcrumbs'] = [
            ['title' => 'Dashboard', 'url' => '/dashboard'],
            ['title' => 'Production Batches']
        ];

        return view('production/list', $data);
    }

    /** =====================================================
     *  ➕ Add / ✏️ Edit Form
     * ===================================================== */
    public function form($id = null)
    {
        $model = new ProductionModel();
        $data['batch'] = $id ? $model->find($id) : null;

        $data['breadcrumbs'] = [
            ['title' => 'Production', 'url' => '/production'],
            ['title' => $id ? 'Edit Batch' : 'Add New Batch']
        ];

        return view('production/form', $data);
    }

    /** =====================================================
     *  💾 Save Batch (Create or Update)
     * ===================================================== */
    public function save()
    {
        $model = new ProductionModel();
        $id = $this->request->getPost('id');

        // ✅ Basic validation
        $validation = $this->validate([
            'batch_no'     => 'required',
            'product_name' => 'required',
            'planned_qty'  => 'required|numeric',
            'uom'          => 'required'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('error', 'Please fill all required fields.');
        }

        $userId = session()->get('user_id') ?? 0;

        $data = [
            'batch_no'     => $this->request->getPost('batch_no'),
            'product_name' => $this->request->getPost('product_name'),
            'planned_qty'  => $this->request->getPost('planned_qty'),
            'uom'          => $this->request->getPost('uom'),
            'start_date'   => $this->request->getPost('start_date'),
            'end_date'     => $this->request->getPost('end_date'),
            'status'       => $this->request->getPost('status'),
            'remarks'      => $this->request->getPost('remarks'),
            'created_by'   => $userId,
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        if ($id) {
            // ✅ Update existing
            $model->update($id, $data);
            log_activity('Updated production batch', 'production', $id);
            session()->setFlashdata('success', 'Batch updated successfully.');
        } else {
            // ✅ Create new
            $data['created_at'] = date('Y-m-d H:i:s');
            $newId = $model->insert($data);
            log_activity('Created new production batch', 'production', $newId);
            session()->setFlashdata('success', 'New production batch created.');
        }

        return redirect()->to('/production');
    }

    /** =====================================================
     *  🔍 View Batch Details
     * ===================================================== */
    public function view($id)
    {
        $db = Database::connect();
        $model = new ProductionModel();

        $data['batch'] = $model->find($id);
        if (!$data['batch']) {
            throw new PageNotFoundException('Batch not found');
        }

        $data['mrs_list'] = $db->table('mrs')
            ->select('id, mrs_no, mrs_date, status')
            ->where('batch_id', $id)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        $data['breadcrumbs'] = [
            ['title' => 'Production', 'url' => '/production'],
            ['title' => 'Batch Details']
        ];

        return view('production/view', $data);
    }
}
