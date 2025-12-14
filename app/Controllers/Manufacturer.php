<?php
namespace App\Controllers;

use App\Models\ManufacturerModel;

class Manufacturer extends BaseController
{
    public function index()
    {
        return redirect()->to('/manufacturers/list');
    }

    public function list()
    {
        $model = new ManufacturerModel();
        $search = $this->request->getGet('search') ?? '';
        $query = $model;

        if (!empty($search)) {
            $query = $query->groupStart()
                           ->like('id', $search)
                           ->orLike('id', $search)
                           ->groupEnd();
        }

        $data = [
            'manufacturers' => $query->paginate(10),
            'pager' => $query->pager,
            'search' => $search,
            'title' => 'Manufacturer List',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'Manufacturer', 'url' => '/manufacturers/list']
            ]
        ];

        return view('manufacturers/list', $data);
    }

    public function form($id = null)
    {
        $model = new ManufacturerModel();
        $record = $id ? $model->find($id) : null;

        $data = [
            'manufacturer' => $record,
            'title' => $id ? 'Edit Manufacturer' : 'Add Manufacturer',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'Manufacturer', 'url' => '/manufacturers/list'],
                ['title' => $id ? 'Edit' : 'Add', 'url' => '']
            ]
        ];

        return view('manufacturers/form', $data);
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

        $model = new ManufacturerModel();
        $post = $this->request->getPost();
        $session = session();
        $userId = $session->get('user_id') ?? null;

        if ($id) {
            $post['updated_by'] = $userId;
            $model->update($id, $post);
            return redirect()->to('/manufacturers/list')->with('success', 'Manufacturer updated successfully');
        }

        $post['created_by'] = $userId;
        $post['updated_by'] = $userId;
        $model->insert($post);
        return redirect()->to('/manufacturers/list')->with('success', 'Manufacturer added successfully');
    }

    public function delete($id)
    {
        $model = new ManufacturerModel();
        $model->delete($id);
        return redirect()->to('/manufacturers/list')->with('success', 'Manufacturer deleted successfully');
    }

    public function view($id)
    {
        $model = new ManufacturerModel();
        $record = $model->find($id);

        if (!$record) {
            return redirect()->to('/manufacturers/list')->with('error', 'Manufacturer not found');
        }

        $data = [
            'manufacturer' => $record,
            'title' => 'View Manufacturer',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'Manufacturer', 'url' => '/manufacturers/list'],
                ['title' => 'View']
            ]
        ];

        return view('manufacturers/view', $data);
    }
}
