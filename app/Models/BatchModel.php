<?php
// app/Models/BatchModel.php
namespace App\Models;

use CodeIgniter\Model;

class BatchModel extends Model
{
    protected $table = 'batches';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;

    protected $allowedFields = [
        'material_id',
        'supplier_id',
        'grn_id',
        'batch_no',
        'qty_total',
        'qty_available',
        'expiry_date',
        'mfg_date',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
