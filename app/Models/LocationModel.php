<?php
namespace App\Models;

use CodeIgniter\Model;

class LocationModel extends Model
{
    protected $table = 'location';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'code',
        'name',
        'type',
        'capacity',
        'remarks',
        'created_at'
    ];

    protected $useTimestamps = false;
}

