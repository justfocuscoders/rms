<?php
namespace App\Controllers;

use App\Models\SupplierModel;
use App\Models\UserModel; // ✅ optional for joins if you want created_by names later

class Supplier extends BaseController
{
    public function index()
    {
        return redirect()->to('/suppliers/list');
    }

    public function list()
    {
        $supplierModel = new SupplierModel();
        $search = $this->request->getGet('search') ?? '';
        $query = $supplierModel;

        if (!empty($search)) {
            $query = $query->groupStart() // ✅ prevent OR confusion in pagination
                           ->like('name', $search)
                           ->orLike('contact_person', $search)
                           ->orLike('email', $search)
                           ->orLike('phone', $search)
                           ->groupEnd();
        }

        $data = [
            'suppliers'   => $query->paginate(10),
            'pager'       => $query->pager,
            'search'      => $search,
            'title'       => 'Suppliers List',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'Suppliers', 'url' => '/suppliers/list']
            ]
        ];

        return view('suppliers/list', $data);
    }

    public function form($id = null)
    {
        $supplierModel = new SupplierModel();
        $supplier = $id ? $supplierModel->find($id) : null;

        $data = [
            'supplier'    => $supplier,
            'title'       => $id ? 'Edit Supplier' : 'Add Supplier',
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'Suppliers', 'url' => '/suppliers/list'],
                ['title' => $id ? 'Edit' : 'Add', 'url' => '']
            ]
        ];

        return view('suppliers/form', $data);
    }

    public function save($id = null)
    {
        helper(['form']);

        $rules = [
            'name'  => 'required|min_length[2]|max_length[100]',
            'email' => 'permit_empty|valid_email',
            'phone' => 'permit_empty|min_length[10]|max_length[15]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please check the input fields.');
        }

        $supplierModel = new SupplierModel();
        $data = $this->request->getPost();

        // ✅ Automatically fill audit fields (only if user_id stored in session)
        $session = session();
        $userId = $session->get('user_id'); // adjust key if needed

        if ($id) {
            $data['updated_by'] = $userId ?? null;
            $supplierModel->update($id, $data);
            return redirect()->to('/suppliers/list')->with('success', 'Supplier updated successfully');
        }

        $data['created_by'] = $userId ?? null;
        $data['updated_by'] = $userId ?? null;
        $supplierModel->insert($data);
        return redirect()->to('/suppliers/list')->with('success', 'Supplier added successfully');
    }

    public function delete($id)
    {
        $supplierModel = new SupplierModel();
        $supplierModel->delete($id);

        return redirect()->to('/suppliers/list')->with('success', 'Supplier deleted successfully');
    }

    public function save_ajax()
    {
        $supplierModel = new SupplierModel();

        $data = [
            'name'           => $this->request->getPost('name'),
            'contact_person' => $this->request->getPost('contact_person'),
            'phone'          => $this->request->getPost('phone'),
            'email'          => $this->request->getPost('email'),
            'address'        => $this->request->getPost('address'),
            // ✅ optional new fields
            'gst_number'     => $this->request->getPost('gst_number'),
            'remarks'        => $this->request->getPost('remarks'),
            'status'         => $this->request->getPost('status') ?? 1,
        ];

        $session = session();
        $userId = $session->get('user_id') ?? null;
        $data['created_by'] = $userId;
        $data['updated_by'] = $userId;

        if ($supplierModel->insert($data)) {
            return $this->response->setJSON([
                'success'  => true,
                'supplier' => [
                    'id'   => $supplierModel->getInsertID(),
                    'name' => $data['name']
                ]
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to add supplier'
        ]);
    }

    public function view($id)
    {
        $supplierModel = new SupplierModel();

        // ✅ Fetch supplier + optional user names (if you want created_by_name)
        $builder = $supplierModel->select('suppliers.*, 
            u1.name AS created_by_name, 
            u2.name AS updated_by_name')
            ->join('users u1', 'u1.id = suppliers.created_by', 'left')
            ->join('users u2', 'u2.id = suppliers.updated_by', 'left')
            ->where('suppliers.id', $id);

        $supplier = $builder->first();

        if (!$supplier) {
            return redirect()->to('/suppliers/list')->with('error', 'Supplier not found');
        }

        $data = [
            'supplier' => $supplier,
            'breadcrumbs' => [
                ['title' => 'Home', 'url' => '/dashboard'],
                ['title' => 'Suppliers', 'url' => '/suppliers/list'],
                ['title' => 'View Supplier']
            ]
        ];

        return view('suppliers/view', $data);
    }
}
