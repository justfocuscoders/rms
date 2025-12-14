<?php
namespace App\Models;

use CodeIgniter\Model;

class ShelfLifeModel extends Model
{
    protected $table = 'shelf_life_master';
    protected $primaryKey = 'id';
    protected $allowedFields = ["item_id", "shelf_life_days", "retest_days", "status", "created_at"];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
