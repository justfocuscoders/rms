<?php
namespace App\Controllers;

use App\Models\ManufacturerTermsModel;

class ManufacturerTerms extends BaseController
{
    public function index()
    {
        return redirect()->to('/manufacturer_terms/list');
    }

    public function list()
    {
        $model = new ManufacturerTermsModel();
        $search = $this->request->getGet('search') ?? '';
        $query = $model;

        if (!empty($search)) {
            $query = $query->groupStart()
                           ->like('id', $search)
                           ->orLike('id', $search)
                           ->groupEnd();
        }

        $data = [
            'manufacturer_terms' => $query->paginate(10),
            'pager' => $query->pager,
            'search' => $search,
            'title' => 'ManufacturerTerms List',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'ManufacturerTerms', 'url' => '/manufacturer_terms/list']
            ]
        ];

        return view('manufacturer_terms/list', $data);
    }

    public function form($id = null)
    {
        $model = new ManufacturerTermsModel();
        $record = $id ? $model->find($id) : null;

        $data = [
            'manufacturer_term' => $record,
            'title' => $id ? 'Edit ManufacturerTerms' : 'Add ManufacturerTerms',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'ManufacturerTerms', 'url' => '/manufacturer_terms/list'],
                ['title' => $id ? 'Edit' : 'Add', 'url' => '']
            ]
        ];

        return view('manufacturer_terms/form', $data);
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

        $model = new ManufacturerTermsModel();
        $post = $this->request->getPost();
        $session = session();
        $userId = $session->get('user_id') ?? null;

        if ($id) {
            $post['updated_by'] = $userId;
            $model->update($id, $post);
            return redirect()->to('/manufacturer_terms/list')->with('success', 'ManufacturerTerms updated successfully');
        }

        $post['created_by'] = $userId;
        $post['updated_by'] = $userId;
        $model->insert($post);
        return redirect()->to('/manufacturer_terms/list')->with('success', 'ManufacturerTerms added successfully');
    }

    public function delete($id)
    {
        $model = new ManufacturerTermsModel();
        $model->delete($id);
        return redirect()->to('/manufacturer_terms/list')->with('success', 'ManufacturerTerms deleted successfully');
    }

    public function view($id)
    {
        $model = new ManufacturerTermsModel();
        $record = $model->find($id);

        if (!$record) {
            return redirect()->to('/manufacturer_terms/list')->with('error', 'ManufacturerTerms not found');
        }

        $data = [
            'manufacturer_term' => $record,
            'title' => 'View ManufacturerTerms',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'ManufacturerTerms', 'url' => '/manufacturer_terms/list'],
                ['title' => 'View']
            ]
        ];

        return view('manufacturer_terms/view', $data);
    }
}
