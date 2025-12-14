<?php
namespace App\Controllers;

use App\Models\StorageLocationModel;
use App\Models\LocationModel;
use App\Models\StorageConditionModel;

class StorageLocations extends BaseController
{
    protected $storageLocationModel;

    public function __construct()
    {
        helper(['form']);
        $this->storageLocationModel = new StorageLocationModel();
    }

    public function index()
    {
        return redirect()->to('/storage-locations/list');
    }

    public function list()
{
    $db = \Config\Database::connect();

    $data['storageLocations'] = $db->table('storage_locations sl')
        ->select('
            sl.*,
            l.name AS location_name,
            sc.condition_name
        ')
        ->join('location l', 'l.id = sl.location_id')
        ->join('storage_conditions sc', 'sc.id = sl.storage_condition_id')
        ->orderBy('sl.id', 'DESC')
        ->get()
        ->getResultArray();

    return view('storage_locations/list', $data);
}


    public function form($id = null)
    {
        $data['storageLocation'] = $id
            ? $this->storageLocationModel->find($id)
            : null;

        $data['locations']  = (new LocationModel())->findAll();
        $data['conditions'] = (new StorageConditionModel())->where('status', 'Active')->findAll();

        return view('storage_locations/form', $data);
    }

    public function save($id = null)
    {
        $data = [
            'location_id'          => $this->request->getPost('location_id'),
            'name'                 => $this->request->getPost('name'),
            'code'                 => $this->request->getPost('code'),
            'type'                 => $this->request->getPost('type'),
            'storage_condition_id' => $this->request->getPost('storage_condition_id'),
            'capacity'             => $this->request->getPost('capacity'),
            'description'          => $this->request->getPost('description'),
            'status'               => 1,
        ];

        if ($id) {
            $this->storageLocationModel->update($id, $data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->storageLocationModel->insert($data);
        }

        return redirect()->to('/storage-locations/list')
            ->with('success', 'Storage location saved successfully');
    }

    public function delete($id)
    {
        $this->storageLocationModel->delete($id);
        return redirect()->to('/storage-locations/list')
            ->with('success', 'Storage location deleted');
    }

    public function view($id)
{
    $db = \Config\Database::connect();

    $storageLocation = $db->table('storage_locations sl')
        ->select('
            sl.*,
            l.name AS location_name,
            sc.condition_name
        ')
        ->join('location l', 'l.id = sl.location_id')
        ->join('storage_conditions sc', 'sc.id = sl.storage_condition_id')
        ->where('sl.id', $id)
        ->get()
        ->getRowArray();

    if (!$storageLocation) {
        return redirect()->to('/storage-locations/list')
            ->with('error', 'Storage location not found');
    }

    return view('storage_locations/view', [
        'storageLocation' => $storageLocation
    ]);
}

}
