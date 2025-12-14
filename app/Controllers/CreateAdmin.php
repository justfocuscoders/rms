<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class CreateAdmin extends Controller
{
    public function index()
    {
        $userModel = new UserModel();

        // Check if admin already exists
        $existingAdmin = $userModel->where('username', 'shridhar')->first();
        if ($existingAdmin) {
            return "Admin already exists!";
        }

        $data = [
            'username' => 'shridhar',
            'email'    => 'shri@rms.com',
            'password' => password_hash('shri123', PASSWORD_DEFAULT),
            'role'     => 'admin',
            'status'   => 1
        ];

        $userModel->insert($data);

        return "Admin account created successfully!";
    }
}
