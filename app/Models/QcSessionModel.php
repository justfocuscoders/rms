<?php
namespace App\Models;

use CodeIgniter\Model;

class QcSessionModel extends Model
{
    protected $table = 'qc_sessions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'grn_id',
        'qc_user_id',
        'status',
        'started_at',
        'last_activity_at',
        'completed_at'
    ];

    protected $useTimestamps = false;
}
