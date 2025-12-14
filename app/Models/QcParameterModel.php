<?php
namespace App\Models;

use CodeIgniter\Model;

class QcParametersModel extends Model
{
    protected $table = 'qc_parameters';
    protected $primaryKey = 'id';
    protected $allowedFields = ["name", "method", "unit", "min_limit", "max_limit", "status", "created_at"];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
