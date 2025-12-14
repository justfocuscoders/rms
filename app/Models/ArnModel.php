<?php
namespace App\Models;

use CodeIgniter\Model;

class ArnModel extends Model
{
    protected $table = 'arn';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'po_id', 'item_id', 'arn_no', 'batch_no', 'supplier_id',
        'received_qty', 'uom', 'received_date', 'expiry_date',
        'status', 'created_by'
    ];

    protected $useTimestamps = true; // Auto-manage created_at & updated_at

    /**
     * 🔢 Generate next ARN number
     * Example: ARN-20251021-0001
     */
    public function generateArnNo()
    {
        $last = $this->orderBy('id', 'DESC')->first();
        $next = $last ? $last['id'] + 1 : 1;
        return 'ARN-' . date('Ymd') . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * 📋 Get all ARNs with related PO, Item & Supplier details
     */
    public function getAllWithDetails()
    {
        return $this->select('
                arn.*,
                purchase_orders.po_number AS po_no,
                items.name AS item_name,
                suppliers.name AS supplier_name
            ')
            ->join('purchase_orders', 'purchase_orders.id = arn.po_id', 'left')
            ->join('items', 'items.id = arn.item_id', 'left')
            ->join('suppliers', 'suppliers.id = arn.supplier_id', 'left')
            ->orderBy('arn.id', 'DESC')
            ->findAll();
    }

    /**
     * 🔍 Get single ARN with relations
     */
    public function getWithRelations($id)
    {
        return $this->select('
                arn.*,
                purchase_orders.po_number AS po_no,
                items.name AS item_name,
                suppliers.name AS supplier_name
            ')
            ->join('purchase_orders', 'purchase_orders.id = arn.po_id', 'left')
            ->join('items', 'items.id = arn.item_id', 'left')
            ->join('suppliers', 'suppliers.id = arn.supplier_id', 'left')
            ->where('arn.id', $id)
            ->first();
    }

    /**
     * ⚙️ Update ARN status (for QC/GRN integration)
     */
    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }
}
