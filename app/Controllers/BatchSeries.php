<?php
namespace App\Controllers;

use App\Models\BatchSeriesModel;

class BatchSeries extends BaseController
{
    public function index()
    {
        return redirect()->to('/batch_series/list');
    }

    public function list()
    {
        $model = new BatchSeriesModel();
        $search = $this->request->getGet('search') ?? '';
        $query = $model;

        if (!empty($search)) {
            $query = $query->groupStart()
                           ->like('id', $search)
                           ->orLike('id', $search)
                           ->groupEnd();
        }

        $data = [
            'batch_series' => $query->paginate(10),
            'pager' => $query->pager,
            'search' => $search,
            'title' => 'BatchSeries List',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'BatchSeries', 'url' => '/batch_series/list']
            ]
        ];

        return view('batch_series/list', $data);
    }

    public function form($id = null)
    {
        $model = new BatchSeriesModel();
        $record = $id ? $model->find($id) : null;

        $data = [
            'batch_serie' => $record,
            'title' => $id ? 'Edit BatchSeries' : 'Add BatchSeries',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'BatchSeries', 'url' => '/batch_series/list'],
                ['title' => $id ? 'Edit' : 'Add', 'url' => '']
            ]
        ];

        return view('batch_series/form', $data);
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

        $model = new BatchSeriesModel();
        $post = $this->request->getPost();
        $session = session();
        $userId = $session->get('user_id') ?? null;

        if ($id) {
            $post['updated_by'] = $userId;
            $model->update($id, $post);
            return redirect()->to('/batch_series/list')->with('success', 'BatchSeries updated successfully');
        }

        $post['created_by'] = $userId;
        $post['updated_by'] = $userId;
        $model->insert($post);
        return redirect()->to('/batch_series/list')->with('success', 'BatchSeries added successfully');
    }

    public function delete($id)
    {
        $model = new BatchSeriesModel();
        $model->delete($id);
        return redirect()->to('/batch_series/list')->with('success', 'BatchSeries deleted successfully');
    }

    public function view($id)
    {
        $model = new BatchSeriesModel();
        $record = $model->find($id);

        if (!$record) {
            return redirect()->to('/batch_series/list')->with('error', 'BatchSeries not found');
        }

        $data = [
            'batch_serie' => $record,
            'title' => 'View BatchSeries',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'BatchSeries', 'url' => '/batch_series/list'],
                ['title' => 'View']
            ]
        ];

        return view('batch_series/view', $data);
    }
}
