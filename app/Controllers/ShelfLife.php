<?php
namespace App\Controllers;

use App\Models\ShelfLifeModel;

class ShelfLife extends BaseController
{
    public function index()
    {
        return redirect()->to('/shelf_life/list');
    }

    public function list()
    {
        $model = new ShelfLifeModel();
        $search = $this->request->getGet('search') ?? '';
        $query = $model;

        if (!empty($search)) {
            $query = $query->groupStart()
                           ->like('id', $search)
                           ->orLike('id', $search)
                           ->groupEnd();
        }

        $data = [
            'shelf_life' => $query->paginate(10),
            'pager' => $query->pager,
            'search' => $search,
            'title' => 'ShelfLife List',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'ShelfLife', 'url' => '/shelf_life/list']
            ]
        ];

        return view('shelf_life/list', $data);
    }

    public function form($id = null)
    {
        $model = new ShelfLifeModel();
        $record = $id ? $model->find($id) : null;

        $data = [
            'shelf_life' => $record,
            'title' => $id ? 'Edit ShelfLife' : 'Add ShelfLife',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'ShelfLife', 'url' => '/shelf_life/list'],
                ['title' => $id ? 'Edit' : 'Add', 'url' => '']
            ]
        ];

        return view('shelf_life/form', $data);
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

        $model = new ShelfLifeModel();
        $post = $this->request->getPost();
        $session = session();
        $userId = $session->get('user_id') ?? null;

        if ($id) {
            $post['updated_by'] = $userId;
            $model->update($id, $post);
            return redirect()->to('/shelf_life/list')->with('success', 'ShelfLife updated successfully');
        }

        $post['created_by'] = $userId;
        $post['updated_by'] = $userId;
        $model->insert($post);
        return redirect()->to('/shelf_life/list')->with('success', 'ShelfLife added successfully');
    }

    public function delete($id)
    {
        $model = new ShelfLifeModel();
        $model->delete($id);
        return redirect()->to('/shelf_life/list')->with('success', 'ShelfLife deleted successfully');
    }

    public function view($id)
    {
        $model = new ShelfLifeModel();
        $record = $model->find($id);

        if (!$record) {
            return redirect()->to('/shelf_life/list')->with('error', 'ShelfLife not found');
        }

        $data = [
            'shelf_life' => $record,
            'title' => 'View ShelfLife',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'ShelfLife', 'url' => '/shelf_life/list'],
                ['title' => 'View']
            ]
        ];

        return view('shelf_life/view', $data);
    }
}
