<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name','email','password_hash','role_id','department_id','status','reset_token','reset_expires','created_at','updated_at'
    ];

    // Get all users with role & department names
    public function getUsersWithRoles($status = null)
    {
        $builder = $this->select('users.*, roles.name as role_name, departments.name as department_name')
                        ->join('roles', 'roles.id = users.role_id', 'left')
                        ->join('departments', 'departments.id = users.department_id', 'left');

        if (!is_null($status)) {
            $builder->where('users.status', $status);
        }

        return $builder->findAll();
    }
}
