<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'code', 'name', 'description', 'uom', 'created_at', 'updated_at'
    ];
}
