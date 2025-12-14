<?php
namespace App\Models;

use CodeIgniter\Model;

class RejectionReasonModel extends Model
{
    protected $table = 'rejection_reasons';
    protected $primaryKey = 'id';
    protected $allowedFields = ["reason", "category", "status", "created_at"];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
