<?php
namespace App\Models;

use CodeIgniter\Model;

class GrnCategoryModel extends Model
{
    protected $table = 'grn_categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description', 'status', 'created_at'];
    protected $useTimestamps = false;
}
