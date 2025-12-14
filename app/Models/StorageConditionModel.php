<?php
namespace App\Models;

use CodeIgniter\Model;

class StorageConditionModel extends Model
{
    protected $table = 'storage_conditions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'condition_name',
        'description',
        'status',
        'created_at'
    ];

    protected $useTimestamps = false;
}
