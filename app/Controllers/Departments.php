<?php
namespace App\Controllers;

use App\Models\DepartmentModel;

class Departments extends BaseController
{
    public function __construct()
    {
        helper('activity'); // ✅ Load helper globally for this controller
    }

    /** =======================================================
     * ✅ Redirect to list
     * ======================================================= */
    public function index()
    {
        return redirect()->to('/departments/list');
    }

    /** =======================================================
     * ✅ Department List
     * ======================================================= */
    public function list()
    {
        $deptModel = new DepartmentModel();
        $search = $this->request->getGet('search') ?? '';
        $query = $deptModel;

        if (!empty($search)) {
            $query->like('name', $search);
        }

        $data['departments'] = $query->paginate(10);
        $data['pager']       = $query->pager;
        $data['search']      = $search;

        // ✅ Breadcrumbs
        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Departments List']
        ];

        return view('departments/list', $data);
    }

    /** =======================================================
     * ✅ Department Add/Edit Form
     * ======================================================= */
    public function form($id = null)
    {
        $deptModel = new DepartmentModel();
        $data['department'] = $id ? $deptModel->find($id) : null;

        // ✅ Breadcrumbs
        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Departments', 'url' => '/departments/list'],
            ['title' => $id ? 'Edit Department' : 'Add Department']
        ];

        return view('departments/form', $data);
    }

    /** =======================================================
     * ✅ Save Department (Create / Update)
     * ======================================================= */
    public function save($id = null)
    {
        $deptModel = new DepartmentModel();
        $data = $this->request->getPost();

        if ($id) {
            // ✅ Update existing
            $deptModel->update($id, $data);
            log_activity('Updated department', 'departments', $id);
            return redirect()->to('/departments/list')->with('success', 'Department updated successfully');
        } else {
            // ✅ Create new
            $deptModel->insert($data);
            $newId = $deptModel->getInsertID();
            log_activity('Created new department', 'departments', $newId);
            return redirect()->to('/departments/list')->with('success', 'Department added successfully');
        }
    }

    /** =======================================================
     * ✅ Delete Department
     * ======================================================= */
    public function delete($id)
    {
        $deptModel = new DepartmentModel();
        $deptModel->delete($id);

        // ✅ Log deletion
        log_activity('Deleted department', 'departments', $id);

        return redirect()->to('/departments/list')->with('success', 'Department deleted successfully');
    }

    /** =======================================================
     * ✅ View Department Details
     * ======================================================= */
    public function view($id)
    {
        $deptModel = new DepartmentModel();
        $data['department'] = $deptModel->find($id);

        if (!$data['department']) {
            return redirect()->to('/departments/list')->with('error', 'Department not found');
        }

        // ✅ Breadcrumbs
        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Departments', 'url' => '/departments/list'],
            ['title' => 'View Department']
        ];

        return view('departments/view', $data);
    }
}
