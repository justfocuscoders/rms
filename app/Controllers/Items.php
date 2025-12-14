<?php
namespace App\Controllers;

use App\Models\ItemModel;
use App\Models\UnitModel;
use App\Models\DepartmentModel;
use App\Models\StockModel;
use App\Models\StorageConditionModel;


class Items extends BaseController
{
    protected $itemModel;
    protected $unitModel;
    protected $deptModel;
    protected $stockModel;

    public function __construct()
    {
        helper(['activity', 'form']);
        $this->itemModel = new ItemModel();
        $this->unitModel = new UnitModel();
        $this->deptModel = new DepartmentModel();

        if (class_exists(StockModel::class)) {
            $this->stockModel = new StockModel();
        }
    }

    /* =======================================================
     * ✅ ITEM LIST
     * ======================================================= */
    public function index()
    {
        return redirect()->to('/items/list');
    }

    public function list()
    {
        $search = $this->request->getGet('search') ?? '';
        $query = $this->itemModel
            ->select('items.*, units.name as unit_name, departments.name as dept_name, sc.condition_name as storage_condition_name')
            ->join('units', 'units.id = items.unit_id', 'left')
            ->join('departments', 'departments.id = items.department_id', 'left')
            ->join('storage_conditions sc', 'sc.id = items.storage_condition_id', 'left');


        if (!empty($search)) {
            $query->groupStart()
                ->like('items.code', $search)
                ->orLike('items.name', $search)
                ->orLike('units.name', $search)
                ->orLike('departments.name', $search)
                ->groupEnd();
        }

        $data['items'] = $query->orderBy('items.id', 'DESC')->paginate(10);
        $data['pager'] = $query->pager;
        $data['search'] = $search;

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Items List']
        ];

        return view('items/list', $data);
    }

    /* =======================================================
     * ✅ ADD / EDIT FORM
     * ======================================================= */
    public function form($id = null)
    {
        $data['item'] = $id ? $this->itemModel->find($id) : null;
        $data['units'] = $this->unitModel->findAll();
        $data['departments'] = $this->deptModel->findAll();

        $data['conditions'] = (new StorageConditionModel())
        ->where('status', 'Active')
        ->findAll();

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Items', 'url' => '/items/list'],
            ['title' => $id ? 'Edit Item' : 'Add Item']
        ];

        return view('items/form', $data);
    }

    /* =======================================================
     * ✅ SAVE ITEM (Create / Update)
     * ======================================================= */
    public function save($id = null)
    {
        $post = $this->request->getPost();

        // 🖼️ Handle image upload
        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $uploadPath = FCPATH . 'uploads/items/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $file->move($uploadPath, $newName);
            $post['image'] = $newName;

            // 🧹 Delete old image when updating
            if ($id) {
                $oldItem = $this->itemModel->find($id);
                if (!empty($oldItem['image']) && file_exists($uploadPath . $oldItem['image'])) {
                    unlink($uploadPath . $oldItem['image']);
                }
            }
        }

        // 🧾 Add created_by if logged in
        if (session()->has('user_id')) {
            $post['created_by'] = session()->get('user_id');
        }

        // ✅ Save logic
        if ($id) {
            $this->itemModel->setValidationRules($this->itemModel->validationRulesUpdate);
            if ($this->itemModel->update($id, $post)) {
                log_activity('Updated Item', 'items', $id);
                return redirect()->to('/items/list')->with('success', 'Item updated successfully');
            } else {
                return redirect()->back()->withInput()->with('error', 'Validation failed during update.');
            }
        } else {
            $this->itemModel->setValidationRules($this->itemModel->validationRulesInsert);
            if ($this->itemModel->insert($post)) {
                $newId = $this->itemModel->getInsertID();
                log_activity('Created Item', 'items', $newId);
                return redirect()->to('/items/list')->with('success', 'Item added successfully');
            } else {
                return redirect()->back()->withInput()->with('error', 'Validation failed during insert.');
            }
        }
    }

    /* =======================================================
     * ✅ DELETE ITEM
     * ======================================================= */
    public function delete($id)
    {
        $item = $this->itemModel->find($id);
        if ($item && !empty($item['image'])) {
            $imagePath = FCPATH . 'uploads/items/' . $item['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $this->itemModel->delete($id);
        log_activity('Deleted Item', 'items', $id);
        return redirect()->to('/items/list')->with('success', 'Item deleted successfully');
    }

    /* =======================================================
     * ✅ VIEW ITEM DETAILS
     * ======================================================= */
    public function view($id)
    {
        $data['item'] = $this->itemModel
            ->select('items.*, units.name as unit_name, departments.name as dept_name')
            ->join('units', 'units.id = items.unit_id', 'left')
            ->join('departments', 'departments.id = items.department_id', 'left')
            ->where('items.id', $id)
            ->first();

        if (!$data['item']) {
            return redirect()->to('/items/list')->with('error', 'Item not found');
        }

        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Items', 'url' => '/items/list'],
            ['title' => 'View Item']
        ];

        return view('items/view', $data);
    }

    /* =======================================================
     * ✅ AJAX: Add Item (GRN popup)
     * ======================================================= */
    public function save_ajax()
    {
        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'unit_id'     => $this->request->getPost('unit_id'),
            'code'        => 'ITM-' . strtoupper(substr(md5(uniqid()), 0, 5))
        ];

        if ($this->itemModel->insert($data)) {
            $id = $this->itemModel->getInsertID();
            log_activity('Created Item via AJAX', 'items', $id);
            $item = $this->itemModel->find($id);
            return $this->response->setJSON(['success' => true, 'item' => $item]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Error saving item']);
        }
    }

    /* =======================================================
     * ✅ AJAX: Item Info (used in GRN)
     * ======================================================= */
    public function info($id)
    {
        $item = $this->itemModel->find($id);
        if (!$item) {
            return $this->response->setJSON(['success' => false, 'message' => 'Item not found']);
        }

        $stockQty = 0;
        if ($this->stockModel) {
            $stockQty = $this->stockModel->where('item_id', $id)->selectSum('quantity')->first()['quantity'] ?? 0;
        }

        $unit = $this->unitModel->find($item['unit_id']);

        return $this->response->setJSON([
            'success' => true,
            'item' => [
                'id' => $item['id'],
                'name' => $item['name'],
                'description' => $item['description'] ?? '',
                'unit' => $unit['name'] ?? '',
                'symbol' => $unit['symbol'] ?? '',
                'stock_qty' => $stockQty
            ]
        ]);
    }

    /* =======================================================
     * ✅ AJAX: Get Items by Supplier
     * ======================================================= */
    public function getBySupplier($supplier_id)
    {
        $items = $this->itemModel->where('supplier_id', $supplier_id)->findAll();

        if ($items) {
            return $this->response->setJSON(['success' => true, 'items' => $items]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'No items found']);
        }
    }
}
