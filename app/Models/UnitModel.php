<?php
namespace App\Models;

use CodeIgniter\Model;

class UnitModel extends Model
{
    protected $table            = 'units';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = true; // ✅ Enable soft deletes (optional but now supported)

    // 🔒 Allow new optional fields while keeping old ones intact
    protected $allowedFields    = [
        'name',
        'symbol',
        'description', // optional new column
        'status',      // optional new column
    ];

    // 🕓 Auto-manage timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at'; // ✅ only works if soft delete is enabled

    // ✅ Validation rules — same as before but extended safely
    protected $validationRules = [
        'name'        => 'required|min_length[2]|max_length[50]',
        'symbol'      => 'permit_empty|max_length[10]',
        'description' => 'permit_empty|max_length[100]',
        'status'      => 'permit_empty|in_list[active,inactive]'
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;
}
