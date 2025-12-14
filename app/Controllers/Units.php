<?php
namespace App\Controllers;

use App\Models\UnitModel;

class Units extends BaseController
{
    public function index()
    {
        return redirect()->to('/units/list');
    }

    public function list()
    {
        $unitModel = new UnitModel();
        $search = $this->request->getGet('search') ?? '';
        $query = $unitModel;

        if (!empty($search)) {
            $query->like('name', $search)->orLike('symbol', $search);
        }

        $data['units']  = $query->paginate(10);
        $data['pager']  = $query->pager;
        $data['search'] = $search;

        // Breadcrumbs for list page
        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Units']
        ];

        return view('units/list', $data);
    }

    public function form($id = null)
    {
        $unitModel = new UnitModel();
        $data['unit'] = $id ? $unitModel->find($id) : null;

        // Breadcrumbs for form page
        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Units', 'url' => '/units/list'],
            ['title' => $id ? 'Edit Unit' : 'Add Unit']
        ];

        return view('units/form', $data);
    }

    public function save($id = null)
    {
        $unitModel = new UnitModel();
        $data = $this->request->getPost();

        if ($id) {
            $unitModel->update($id, $data);
            return redirect()->to('/units/list')->with('success', 'Unit updated successfully');
        } else {
            $unitModel->insert($data);
            return redirect()->to('/units/list')->with('success', 'Unit added successfully');
        }
    }

    public function delete($id)
    {
        $unitModel = new UnitModel();
        $unitModel->delete($id);
        return redirect()->to('/units/list')->with('success', 'Unit deleted successfully');
    }

    public function view($id)
    {
        $unitModel = new UnitModel(); // ✅ missing in your code
        $data['unit'] = $unitModel->find($id);

        if (!$data['unit']) {
            return redirect()->to('/units/list')->with('error', 'Unit not found');
        }

        // Breadcrumbs for view page
        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Units', 'url' => '/units/list'],
            ['title' => 'View Unit']
        ];

        return view('units/view', $data);
    }
    
    public function save_ajax()
{
    $unitModel = new \App\Models\UnitModel();
    $data = [
        'name'   => $this->request->getPost('name'),
        'symbol' => $this->request->getPost('symbol'),
    ];

    if ($unitModel->insert($data)) {
        $id = $unitModel->getInsertID();
        $unit = $unitModel->find($id);
        return $this->response->setJSON(['success' => true, 'unit' => $unit]);
    } else {
        return $this->response->setJSON(['success' => false, 'message' => 'Error saving unit']);
    }
}

}
