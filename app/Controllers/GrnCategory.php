<?php
namespace App\Controllers;

use App\Models\GrnCategoryModel;

class GrnCategory extends BaseController
{
    public function index()
    {
        return redirect()->to('/grn-category/list');
    }

    public function list()
    {
        $model = new GrnCategoryModel();
        $search = $this->request->getGet('search') ?? '';
        $query = $model;

        if (!empty($search)) {
            $query = $query->groupStart()
                           ->like('name', $search)
                           ->orLike('description', $search)
                           ->groupEnd();
        }

        $data = [
            'categories'  => $query->orderBy('id', 'DESC')->paginate(10),
            'pager'       => $query->pager,
            'search'      => $search,
            'title'       => 'GRN Categories',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'GRN Categories', 'url' => '/grn-category/list']
            ]
        ];

        return view('grn_category/list', $data);
    }

    public function form($id = null)
    {
        $model = new GrnCategoryModel();
        $category = $id ? $model->find($id) : null;

        $data = [
            'category'    => $category,
            'title'       => $id ? 'Edit GRN Category' : 'Add GRN Category',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'GRN Categories', 'url' => '/grn-category/list'],
                ['title' => $id ? 'Edit' : 'Add', 'url' => '']
            ]
        ];

        return view('grn_category/form', $data);
    }

    public function save($id = null)
    {
        helper(['form']);

        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please check the input fields.');
        }

        $model = new GrnCategoryModel();
        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status') ?? 'Active',
        ];

        if ($id) {
            $model->update($id, $data);
            return redirect()->to('/grn-category/list')->with('success', 'Category updated successfully');
        }

        $model->insert($data);
        return redirect()->to('/grn-category/list')->with('success', 'Category added successfully');
    }

    public function delete($id)
    {
        $model = new GrnCategoryModel();
        $model->delete($id);
        return redirect()->to('/grn-category/list')->with('success', 'Category deleted successfully');
    }

    public function view($id)
    {
        $model = new GrnCategoryModel();
        $category = $model->find($id);

        if (!$category) {
            return redirect()->to('/grn-category/list')->with('error', 'Category not found');
        }

        $data = [
            'category'    => $category,
            'title'       => 'View GRN Category',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'GRN Categories', 'url' => '/grn-category/list'],
                ['title' => 'View']
            ]
        ];

        return view('grn_category/view', $data);
    }
}
