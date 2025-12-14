<?php
namespace App\Models;

use CodeIgniter\Model;

class ProductionModel extends Model
{
    protected $table = 'production_batches'; 
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'item_id',
        'batch_no',
        'product_name',
        'planned_qty',
        'uom',
        'start_date',
        'end_date',
        'status',
        'remarks',
        'created_by',
        'created_at'
    ];
}
