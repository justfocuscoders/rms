<?php
namespace App\Models;

use CodeIgniter\Model;

class MrsDetailModel extends Model
{
    protected $table = 'mrs_details';
    protected $primaryKey = 'id';
    protected $allowedFields = [
    'mrs_id','item_id','batch_no','qty_requested','qty_issued','uom','remarks','status','created_at'
];

    protected $useTimestamps = false;
}
