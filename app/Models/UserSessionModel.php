<?php
namespace App\Models;

use CodeIgniter\Model;

class UserSessionModel extends Model
{
    protected $table = 'user_sessions';   // ✅ table from your SQL
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'login_time',
        'logout_time',
        'ip_address',
        'user_agent'
    ];
}
