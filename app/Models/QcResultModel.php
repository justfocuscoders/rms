<?php
namespace App\Models;

use CodeIgniter\Model;

class QcResultModel extends Model
{
    protected $table = 'qc_results';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'grn_detail_id','qc_status','remarks','tested_by','tested_at','created_at'
    ];
}
