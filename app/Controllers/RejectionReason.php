<?php
namespace App\Controllers;

use App\Models\RejectionReasonModel;

class RejectionReason extends BaseController
{
    public function index()
    {
        return redirect()->to('/rejection_reasons/list');
    }

    public function list()
    {
        $model = new RejectionReasonModel();
        $search = $this->request->getGet('search') ?? '';
        $query = $model;

        if (!empty($search)) {
            $query = $query->groupStart()
                           ->like('id', $search)
                           ->orLike('id', $search)
                           ->groupEnd();
        }

        $data = [
            'rejection_reasons' => $query->paginate(10),
            'pager' => $query->pager,
            'search' => $search,
            'title' => 'RejectionReason List',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'RejectionReason', 'url' => '/rejection_reasons/list']
            ]
        ];

        return view('rejection_reasons/list', $data);
    }

    public function form($id = null)
    {
        $model = new RejectionReasonModel();
        $record = $id ? $model->find($id) : null;

        $data = [
            'rejection_reason' => $record,
            'title' => $id ? 'Edit RejectionReason' : 'Add RejectionReason',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'RejectionReason', 'url' => '/rejection_reasons/list'],
                ['title' => $id ? 'Edit' : 'Add', 'url' => '']
            ]
        ];

        return view('rejection_reasons/form', $data);
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

        $model = new RejectionReasonModel();
        $post = $this->request->getPost();
        $session = session();
        $userId = $session->get('user_id') ?? null;

        if ($id) {
            $post['updated_by'] = $userId;
            $model->update($id, $post);
            return redirect()->to('/rejection_reasons/list')->with('success', 'RejectionReason updated successfully');
        }

        $post['created_by'] = $userId;
        $post['updated_by'] = $userId;
        $model->insert($post);
        return redirect()->to('/rejection_reasons/list')->with('success', 'RejectionReason added successfully');
    }

    public function delete($id)
    {
        $model = new RejectionReasonModel();
        $model->delete($id);
        return redirect()->to('/rejection_reasons/list')->with('success', 'RejectionReason deleted successfully');
    }

    public function view($id)
    {
        $model = new RejectionReasonModel();
        $record = $model->find($id);

        if (!$record) {
            return redirect()->to('/rejection_reasons/list')->with('error', 'RejectionReason not found');
        }

        $data = [
            'rejection_reason' => $record,
            'title' => 'View RejectionReason',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'RejectionReason', 'url' => '/rejection_reasons/list'],
                ['title' => 'View']
            ]
        ];

        return view('rejection_reasons/view', $data);
    }
}
