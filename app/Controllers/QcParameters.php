<?php
namespace App\Controllers;

use App\Models\QcParametersModel;

class QcParameters extends BaseController
{
    public function index()
    {
        return redirect()->to('/qc_parameters/list');
    }

    public function list()
    {
        $model = new QcParametersModel();
        $search = $this->request->getGet('search') ?? '';
        $query = $model;

        if (!empty($search)) {
            $query = $query->groupStart()
                           ->like('id', $search)
                           ->orLike('id', $search)
                           ->groupEnd();
        }

        $data = [
            'qc_parameters' => $query->paginate(10),
            'pager' => $query->pager,
            'search' => $search,
            'title' => 'QcParameters List',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'QcParameters', 'url' => '/qc_parameters/list']
            ]
        ];

        return view('qc_parameters/list', $data);
    }

    public function form($id = null)
    {
        $model = new QcParametersModel();
        $record = $id ? $model->find($id) : null;

        $data = [
            'qc_parameter' => $record,
            'title' => $id ? 'Edit QcParameters' : 'Add QcParameters',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'QcParameters', 'url' => '/qc_parameters/list'],
                ['title' => $id ? 'Edit' : 'Add', 'url' => '']
            ]
        ];

        return view('qc_parameters/form', $data);
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

        $model = new QcParametersModel();
        $post = $this->request->getPost();
        $session = session();
        $userId = $session->get('user_id') ?? null;

        if ($id) {
            $post['updated_by'] = $userId;
            $model->update($id, $post);
            return redirect()->to('/qc_parameters/list')->with('success', 'QcParameters updated successfully');
        }

        $post['created_by'] = $userId;
        $post['updated_by'] = $userId;
        $model->insert($post);
        return redirect()->to('/qc_parameters/list')->with('success', 'QcParameters added successfully');
    }

    public function delete($id)
    {
        $model = new QcParametersModel();
        $model->delete($id);
        return redirect()->to('/qc_parameters/list')->with('success', 'QcParameters deleted successfully');
    }

    public function view($id)
    {
        $model = new QcParametersModel();
        $record = $model->find($id);

        if (!$record) {
            return redirect()->to('/qc_parameters/list')->with('error', 'QcParameters not found');
        }

        $data = [
            'qc_parameter' => $record,
            'title' => 'View QcParameters',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'QcParameters', 'url' => '/qc_parameters/list'],
                ['title' => 'View']
            ]
        ];

        return view('qc_parameters/view', $data);
    }
}
