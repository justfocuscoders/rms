<?php
namespace App\Controllers;

use App\Models\LocationModel;

class Locations extends BaseController
{
    protected $locationModel;

    public function __construct()
    {
        helper(['form', 'activity']);
        $this->locationModel = new LocationModel();
    }

    /* =======================================================
     * ✅ REDIRECT
     * ======================================================= */
    public function index()
    {
        return redirect()->to('/locations/list');
    }

    /* =======================================================
     * ✅ LOCATION LIST
     * ======================================================= */
    public function list()
    {
        $search = $this->request->getGet('search') ?? '';

        $query = $this->locationModel;

        if ($search) {
            $query = $query->groupStart()
                ->like('code', $search)
                ->orLike('name', $search)
                ->orLike('type', $search)
                ->groupEnd();
        }

        $data['locations'] = $query->orderBy('id', 'DESC')->paginate(10);
        $data['pager'] = $query->pager;
        $data['search'] = $search;

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Locations']
        ];

        return view('locations/list', $data);
    }

    /* =======================================================
     * ✅ ADD / EDIT FORM
     * ======================================================= */
    public function form($id = null)
    {
        $data['location'] = $id ? $this->locationModel->find($id) : null;

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Locations', 'url' => '/locations/list'],
            ['title' => $id ? 'Edit Location' : 'Add Location']
        ];

        return view('locations/form', $data);
    }

    /* =======================================================
     * ✅ SAVE LOCATION
     * ======================================================= */
    public function save($id = null)
{
    $data = [
        'code'     => $this->request->getPost('code'),
        'name'     => $this->request->getPost('name'),
        'type'     => $this->request->getPost('type'),
        'capacity' => $this->request->getPost('capacity'),
        'remarks'  => $this->request->getPost('remarks'),
    ];

    if ($id) {
        $this->locationModel->update($id, $data);
        log_activity('Updated Location', 'location', $id);
        return redirect()->to('/locations/list')
            ->with('success', 'Location updated successfully');
    } else {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->locationModel->insert($data);
        $newId = $this->locationModel->getInsertID();
        log_activity('Created Location', 'location', $newId);
        return redirect()->to('/locations/list')
            ->with('success', 'Location added successfully');
    }
}


    /* =======================================================
     * ✅ VIEW LOCATION
     * ======================================================= */
    public function view($id)
    {
        $data['location'] = $this->locationModel->find($id);

        if (!$data['location']) {
            return redirect()->to('/locations/list')->with('error', 'Location not found');
        }

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Locations', 'url' => '/locations/list'],
            ['title' => 'View Location']
        ];

        return view('locations/view', $data);
    }

    /* =======================================================
     * ✅ DELETE LOCATION
     * ======================================================= */
    public function delete($id)
    {
        $this->locationModel->delete($id);
        log_activity('Deleted Location', 'location', $id);
        return redirect()->to('/locations/list')->with('success', 'Location deleted successfully');
    }
}
