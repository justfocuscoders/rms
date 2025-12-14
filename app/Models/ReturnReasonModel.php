<?php
namespace App\Models;

use CodeIgniter\Model;

class ReturnReasonModel extends Model
{
    protected $table = 'return_reasons';
    protected $primaryKey = 'id';
    protected $allowedFields = ["reason", "type", "status", "created_at"];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
