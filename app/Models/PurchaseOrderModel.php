<?php
namespace App\Models;

use CodeIgniter\Model;

class PurchaseOrderModel extends Model
{
    protected $table = 'purchase_orders';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'po_number',
        'supplier_id',
        'order_date',
        'expected_date',
        'status',
        'remarks',
        'created_by',
        'created_at'
    ];

    protected $useTimestamps = false;

    /** ✅ Auto-generate PO number */
    public function generatePONumber()
    {
        $year = date('Y');
        $last = $this->orderBy('id', 'DESC')->first();

        if ($last && preg_match('/PO-' . $year . '-(\d+)/', $last['po_number'], $m)) {
            $next = intval($m[1]) + 1;
        } else {
            $next = 1;
        }

        return 'PO-' . $year . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    /** ✅ Get PO with supplier & items */
    public function getPOWithItems($id)
    {
        $po = $this->select('purchase_orders.*, suppliers.name as supplier_name')
                   ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
                   ->where('purchase_orders.id', $id)
                   ->first();

        if (!$po) return null;

        $itemModel = new \App\Models\PurchaseOrderItemModel();
        $po['items'] = $itemModel->getItemsByPO($id);

        return $po;
    }

    /** ✅ Update status */
    public function setStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }

    /** ✅ Create PO + items (transaction safe) */
    public function createPOWithItems(array $poData, array $items)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $po_id = $this->insert($poData);
        if (!$po_id) {
            throw new \Exception('Failed to create Purchase Order header.');
        }

        $itemModel = new \App\Models\PurchaseOrderItemModel();

        foreach ($items as $item) {
            $itemModel->insert([
                'po_id'       => $po_id,
                'item_id'     => $item['item_id'],
                'qty_ordered' => $item['qty_ordered'],
                'unit_price'  => $item['unit_price'],
                'status'      => 'Pending'
            ]);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            throw new \Exception('Purchase Order transaction failed.');
        }

        return $po_id;
    }
}
