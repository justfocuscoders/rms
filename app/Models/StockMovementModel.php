<?php
namespace App\Models;

use CodeIgniter\Model;

class StockMovementModel extends Model
{
    protected $table = 'stock_movements';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'stock_id',
        'movement_type',
        'reference_table',
        'reference_id',
        'qty',
        'balance_after',
        'remarks',
        'moved_by',
        'moved_at'
    ];

    protected $returnType = 'array';
    public $useTimestamps = false;
}
