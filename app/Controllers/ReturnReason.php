<?php
namespace App\Controllers;

use App\Models\ReturnReasonModel;

class ReturnReason extends BaseController
{
    public function index()
    {
        return redirect()->to('/return_reasons/list');
    }

    public function list()
    {
        $model = new ReturnReasonModel();
        $search = $this->request->getGet('search') ?? '';
        $query = $model;

        if (!empty($search)) {
            $query = $query->groupStart()
                           ->like('id', $search)
                           ->orLike('id', $search)
                           ->groupEnd();
        }

        $data = [
            'return_reasons' => $query->paginate(10),
            'pager' => $query->pager,
            'search' => $search,
            'title' => 'ReturnReason List',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'ReturnReason', 'url' => '/return_reasons/list']
            ]
        ];

        return view('return_reasons/list', $data);
    }

    public function form($id = null)
    {
        $model = new ReturnReasonModel();
        $record = $id ? $model->find($id) : null;

        $data = [
            'return_reason' => $record,
            'title' => $id ? 'Edit ReturnReason' : 'Add ReturnReason',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'ReturnReason', 'url' => '/return_reasons/list'],
                ['title' => $id ? 'Edit' : 'Add', 'url' => '']
            ]
        ];

        return view('return_reasons/form', $data);
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

        $model = new ReturnReasonModel();
        $post = $this->request->getPost();
        $session = session();
        $userId = $session->get('user_id') ?? null;

        if ($id) {
            $post['updated_by'] = $userId;
            $model->update($id, $post);
            return redirect()->to('/return_reasons/list')->with('success', 'ReturnReason updated successfully');
        }

        $post['created_by'] = $userId;
        $post['updated_by'] = $userId;
        $model->insert($post);
        return redirect()->to('/return_reasons/list')->with('success', 'ReturnReason added successfully');
    }

    public function delete($id)
    {
        $model = new ReturnReasonModel();
        $model->delete($id);
        return redirect()->to('/return_reasons/list')->with('success', 'ReturnReason deleted successfully');
    }

    public function view($id)
    {
        $model = new ReturnReasonModel();
        $record = $model->find($id);

        if (!$record) {
            return redirect()->to('/return_reasons/list')->with('error', 'ReturnReason not found');
        }

        $data = [
            'return_reason' => $record,
            'title' => 'View ReturnReason',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'ReturnReason', 'url' => '/return_reasons/list'],
                ['title' => 'View']
            ]
        ];

        return view('return_reasons/view', $data);
    }
}
