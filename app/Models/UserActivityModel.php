<?php
namespace App\Models;

use CodeIgniter\Model;

class UserActivityModel extends Model
{
    protected $table = 'user_activity_log';
    protected $allowedFields = [
        'user_id', 'action', 'module', 'record_id', 'ip_address', 'user_agent', 'created_at'
    ];
    protected $useTimestamps = false;
}
