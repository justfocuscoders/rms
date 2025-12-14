<?php
namespace App\Controllers;

use App\Models\StorageConditionModel;

class StorageCondition extends BaseController
{
    public function index()
    {
        return redirect()->to('/storage_conditions/list');
    }

    public function list()
    {
        $model = new StorageConditionModel();
        $search = $this->request->getGet('search') ?? '';
        $query = $model;

        if (!empty($search)) {
            $query = $query->groupStart()
                           ->like('id', $search)
                           ->orLike('id', $search)
                           ->groupEnd();
        }

        $data = [
            'storage_conditions' => $query->paginate(10),
            'pager' => $query->pager,
            'search' => $search,
            'title' => 'StorageCondition List',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'StorageCondition', 'url' => '/storage_conditions/list']
            ]
        ];

        return view('storage_conditions/list', $data);
    }

    public function form($id = null)
    {
        $model = new StorageConditionModel();
        $record = $id ? $model->find($id) : null;

        $data = [
            'storage_condition' => $record,
            'title' => $id ? 'Edit StorageCondition' : 'Add StorageCondition',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'StorageCondition', 'url' => '/storage_conditions/list'],
                ['title' => $id ? 'Edit' : 'Add', 'url' => '']
            ]
        ];

        return view('storage_conditions/form', $data);
    }

    public function save($id = null)
    {
        helper(['form']);

        $rules = [
            'status' => 'permit_empty|in_list[Active,Inactive]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please check the input fields.');
        }

        $model = new StorageConditionModel();
        $post = $this->request->getPost();
        $session = session();
        $userId = $session->get('user_id') ?? null;

        if ($id) {
            $post['updated_by'] = $userId;
            $model->update($id, $post);
            return redirect()->to('/storage_conditions/list')->with('success', 'StorageCondition updated successfully');
        }

        $post['created_by'] = $userId;
        $post['updated_by'] = $userId;
        $model->insert($post);
        return redirect()->to('/storage_conditions/list')->with('success', 'StorageCondition added successfully');
    }

    public function delete($id)
    {
        $model = new StorageConditionModel();
        $model->delete($id);
        return redirect()->to('/storage_conditions/list')->with('success', 'StorageCondition deleted successfully');
    }

    public function view($id)
    {
        $model = new StorageConditionModel();
        $record = $model->find($id);

        if (!$record) {
            return redirect()->to('/storage_conditions/list')->with('error', 'StorageCondition not found');
        }

        $data = [
            'storage_condition' => $record,
            'title' => 'View StorageCondition',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'StorageCondition', 'url' => '/storage_conditions/list'],
                ['title' => 'View']
            ]
        ];

        return view('storage_conditions/view', $data);
    }
}
