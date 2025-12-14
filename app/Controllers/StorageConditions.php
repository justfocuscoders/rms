<?php
namespace App\Controllers;

use App\Models\StorageConditionModel;

class StorageConditions extends BaseController
{
    protected $conditionModel;

    public function __construct()
    {
        helper(['form']);
        $this->conditionModel = new StorageConditionModel();
    }

    public function index()
    {
        return redirect()->to('/storage_conditions/list');
    }

    public function list()
    {
        $data['conditions'] = $this->conditionModel
            ->orderBy('id', 'DESC')
            ->findAll();

        return view('storage_conditions/list', $data);
    }

    public function view($id)
{
    $condition = $this->conditionModel->find($id);

    if (!$condition) {
        return redirect()->to('/storage_conditions/list')
            ->with('error', 'Storage condition not found');
    }

    return view('storage_conditions/view', [
        'condition' => $condition
    ]);
}


    public function form($id = null)
    {
        $data['condition'] = $id
            ? $this->conditionModel->find($id)
            : null;

        return view('storage_conditions/form', $data);
    }

    public function save($id = null)
    {
        $data = [
            'condition_name' => $this->request->getPost('condition_name'),
            'description'    => $this->request->getPost('description'),
            'status'         => $this->request->getPost('status') ?? 'Active',
        ];

        if ($id) {
            $this->conditionModel->update($id, $data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->conditionModel->insert($data);
        }

        return redirect()->to('/storage_conditions/list')
            ->with('success', 'Storage condition saved successfully');
    }

    public function delete($id)
    {
        $this->conditionModel->delete($id);

        return redirect()->to('/storage_conditions/list')
            ->with('success', 'Storage condition deleted');
    }

    
}
