<?php
namespace App\Models;

use CodeIgniter\Model;

class StorageLocationModel extends Model
{
    protected $table = 'storage_locations';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'location_id',
        'name',
        'code',
        'type',
        'storage_condition_id',
        'capacity',
        'description',
        'status',
        'created_at'
    ];

    protected $useTimestamps = false;
}
