<?php
namespace App\Models;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $table = 'stock';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'item_id', 'supplier_id', 'batch_no', 'quantity', 'uom', 'location_id', 'created_at', 'updated_at'
    ];
}
