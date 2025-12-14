<?php
namespace App\Models;

use CodeIgniter\Model;

class IssueModel extends Model
{
    protected $table = 'issues';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'mrs_detail_id','stock_id','qty_issued','issued_by','issued_at'
    ];
}
