<?php
namespace App\Models;

use CodeIgniter\Model;

class ManufacturerTermsModel extends Model
{
    protected $table = 'manufacturer_terms';
    protected $primaryKey = 'id';
    protected $allowedFields = ["manufacturer_id", "term_name", "description", "validity_days", "status", "created_at"];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
