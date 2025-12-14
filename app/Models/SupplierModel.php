<?php
namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table      = 'suppliers';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'gst_number',    // ? newly added
        'status',        // ? active/inactive
        'remarks',       // ? notes
        'created_by',    // ? user who created
        'updated_by'     // ? user who updated
    ];

    protected $useTimestamps = true; // ? still handles created_at & updated_at
}
