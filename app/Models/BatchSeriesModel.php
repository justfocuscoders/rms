<?php
namespace App\Models;

use CodeIgniter\Model;

class BatchSeriesModel extends Model
{
    protected $table = 'batch_series';
    protected $primaryKey = 'id';
    protected $allowedFields = ["prefix", "type", "next_number", "format", "status", "created_at"];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
