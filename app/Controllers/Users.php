<?php
namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // URL: /users
    public function index()
    {
        $data['users'] = $this->userModel->getUsersWithRoles();
        $data['title'] = 'User List';

        // 👇 load list.php instead of index.php
        return view('users/list', $data);
    }
}
