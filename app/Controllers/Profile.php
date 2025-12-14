<?php
namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    // ✅ Show the logged-in user's profile
    public function index()
    {
        $userId = session('user_id'); // Assuming you store logged in user ID as 'user_id'

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        $user = $this->userModel
            ->select('users.*, roles.name AS role_name, departments.name AS department_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->where('users.id', $userId)
            ->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $data = [
            'title' => 'My Profile',
            'user'  => $user,
        ];

        return view('profile/view', $data);
    }

    // ✅ Optional: Update name or password
    public function update()
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $data = [
            'name' => $this->request->getPost('name'),
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->update($userId, $data);

        session()->setFlashdata('success', 'Profile updated successfully!');
        return redirect()->to('/profile');
    }
}
