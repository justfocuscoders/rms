<?php
namespace App\Models;

use CodeIgniter\Model;

class PurchaseOrderItemModel extends Model
{
    protected $table = 'purchase_order_items';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'po_id',
        'item_id',
        'qty_ordered',
        'unit_price',
        'status'
    ];

    protected $useTimestamps = false;

    /** ✅ Get items linked to a specific PO */
    public function getItemsByPO($po_id)
    {
        return $this->select('purchase_order_items.*, items.name as item_name, items.code as item_code')
                    ->join('items', 'items.id = purchase_order_items.item_id', 'left')
                    ->where('po_id', $po_id)
                    ->findAll();
    }

    /** ✅ Mark all items in a PO as received */
    public function markAsReceived($po_id)
    {
        return $this->where('po_id', $po_id)->set(['status' => 'Received'])->update();
    }

    /** ✅ Check if all PO items are received */
    public function allItemsReceived($po_id)
    {
        return $this->where('po_id', $po_id)
                    ->where('status !=', 'Received')
                    ->countAllResults() === 0;
    }
}
