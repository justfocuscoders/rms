<?php
namespace App\Models;

use CodeIgniter\Model;

class GrnModel extends Model
{
    protected $table = 'grn';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'grn_no',
        'arn_no',
        'po_id',
        'supplier_id',
        'received_by',
        'received_date',
        'status',
        'created_at',
        'updated_at',
        'gate_entry_no',
        'gate_entry_date',
        'challan_no',
        'challan_date',
        'transport_name',
        'lr_no',
        'lr_date',
        'vehicle_no',
        'location',
        'manufacturer',
        'reported_at',
        'unloaded_at',
    ];

    protected $useTimestamps = false;

    public function generateGrnNo() {
        $last = $this->orderBy('id', 'DESC')->first();
        $nextId = $last ? $last['id'] + 1 : 1;
        $year = date('Y');
        return sprintf('GRN-%s-%04d', $year, $nextId);
    }

    public function generateArnNo() {
        $last = $this->orderBy('id', 'DESC')->first();
        $nextId = $last ? $last['id'] + 1 : 1;
        $year = date('Y');
        return sprintf('ARN-%s-%04d', $year, $nextId);
    }
}