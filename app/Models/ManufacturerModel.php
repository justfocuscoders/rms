<?php
namespace App\Models;

use CodeIgniter\Model;

class ManufacturerModel extends Model
{
    protected $table = 'manufacturers';
    protected $primaryKey = 'id';
    protected $allowedFields = ["name", "address", "contact_person", "phone", "email", "status", "created_at"];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
