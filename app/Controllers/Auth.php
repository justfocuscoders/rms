<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserSessionModel;

class Auth extends BaseController
{
    /** ==========================
     *  🧩 LOGIN FORM
     *  ========================== */
    public function login()
    {
        return view('auth/login');
    }

    /** ==========================
     *  🔐 HANDLE LOGIN ATTEMPT
     *  ========================== */
    public function attemptLogin()
{
    $validation = \Config\Services::validation();

    // Step 1: Validate input
    $validation->setRules([
        'email'    => 'required|valid_email',
        'password' => 'required|min_length[6]'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return redirect()->back()->with('error', implode('<br>', $validation->getErrors()));
    }

    // Step 2: Fetch user
    $email    = trim($this->request->getPost('email'));
    $password = trim($this->request->getPost('password'));

    $user = (new UserModel())
        ->select('users.*, roles.name as role_name')
        ->join('roles', 'roles.id = users.role_id', 'left')
        ->where('users.email', $email)
        ->first();

    // Step 3: Validate credentials
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return redirect()->back()->with('error', 'Invalid email or password.');
    }

    // Step 4: Check approval
    if ($user['status'] == 0) {
        return redirect()->back()->with('error', 'Your account is pending approval by Admin.');
    }

    // Step 5: Create user session record
    session()->regenerate();
    $sessionModel = new UserSessionModel();
    $sessionId = $sessionModel->insert([
        'user_id'    => $user['id'],
        'login_time' => date('Y-m-d H:i:s'),
        'ip_address' => $this->request->getIPAddress(),
        'user_agent' => $this->request->getUserAgent()->getAgentString()
    ]);

    // ✅ Step 6: Normalize role names (critical fix)
    // Convert DB role names into consistent short keys
    $normalizedRole = match (strtolower(trim($user['role_name']))) {
        'admin'                 => 'admin',
        'warehouse'             => 'warehouse',
        'quality control', 'qc' => 'qc',
        'production'            => 'production',
        'procurement'           => 'procurement',
        default                 => strtolower(trim($user['role_name'])),
    };

    // Step 7: Store session data
    session()->set([
        'user_id'          => $user['id'],
        'role_id'          => $user['role_id'],
        'role_name'        => $user['role_name'], // original display name
        'role'             => $normalizedRole,    // normalized key (e.g. 'qc')
        'name'             => $user['name'],
        'user_session_id'  => $sessionId,
        'isLoggedIn'       => true
    ]);

    // Step 8: Log activity
    helper('activity');
    log_activity('User logged in', 'auth', $user['id']);

    // ✅ Step 9: Redirect based on normalized role
    return match ($normalizedRole) {
    'admin'      => redirect()->to('/admin/dashboard'),
    'manager'    => redirect()->to('/warehouse/dashboard'), // or your desired dashboard
    'staff'      => redirect()->to('/qc/dashboard'),        // or correct department dashboard
    'qc'         => redirect()->to('/qc/dashboard'),
    'production' => redirect()->to('/production/dashboard'),
    'procurement'=> redirect()->to('/procurement/dashboard'),
    default      => redirect()->to('/dashboard'),
};

}


    /** ==========================
     *  🚪 LOGOUT
     *  ========================== */
    public function logout()
{
    $userSessionId = session()->get('user_session_id');
    $userId = session()->get('user_id');

    if ($userSessionId) {
        $sessionModel = new UserSessionModel();
        $sessionModel->update($userSessionId, [
            'logout_time' => date('Y-m-d H:i:s')
        ]);
    }

    helper('activity');
    log_activity('User logged out', 'auth', $userId);

    session()->destroy();
    return redirect()->to('/login')->with('success', 'You have been logged out successfully.');
}


    /** ==========================
     *  📝 SIGNUP FORM
     *  ========================== */
    public function signup()
    {
        return view('auth/signup');
    }

    /** ==========================
     *  🧩 HANDLE SIGNUP SUBMIT
     *  ========================== */
    public function attemptSignup()
{
    $validation = \Config\Services::validation();

    // ✅ Custom rule: must contain '@' and end with '.com'
    $validation->setRules([
        'name'     => 'required|min_length[3]',
        'email'    => [
            'rules'  => 'required|regex_match[/^[^@\s]+@[^@\s]+\.com$/i]|is_unique[users.email]',
            'errors' => [
                'regex_match' => 'Email must contain "@" and end with ".com".',
                'is_unique'   => 'This email is already registered.',
            ],
        ],
        'password' => 'required|min_length[6]',
        'role_id'  => 'required|integer',
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return redirect()->back()->with('error', implode('<br>', $validation->getErrors()));
    }

    $data = $this->request->getPost();
    $pwd = trim($data['password']);

    // ✅ Password strength check
    $strengthScore = 0;
    if (strlen($pwd) >= 8) $strengthScore += 2;
    if (preg_match('/[A-Z]/', $pwd)) $strengthScore++;
    if (preg_match('/[a-z]/', $pwd)) $strengthScore++;
    if (preg_match('/\d/', $pwd)) $strengthScore++;
    if (preg_match('/[^A-Za-z0-9]/', $pwd)) $strengthScore += 2;

    if ($strengthScore < 4) {
        return redirect()->back()->with('error', 'Password too weak. Use at least 8 characters with a mix of letters, numbers, and symbols.');
    }

    // ✅ Save pending user
    $userModel = new UserModel();
    $newUserId = $userModel->insert([
        'name'          => trim($data['name']),
        'email'         => trim($data['email']),
        'password_hash' => password_hash($pwd, PASSWORD_DEFAULT),
        'role_id'       => $data['role_id'],
        'department_id' => $this->mapRoleToDepartment($data['role_id']),
        'status'        => 0,
    ]);

    helper('activity');
    log_activity('New signup request created', 'auth', $newUserId);

    return redirect()->to('/login')->with('success', 'Signup request submitted successfully. Wait for admin approval.');
}


    /** ==========================
     *  🏢 ROLE → DEPARTMENT MAP
     *  ========================== */
    private function mapRoleToDepartment($roleId)
{
    return match ((int) $roleId) {
        1 => null, // Admin (no department)
        2 => 1,    // Manager        → Warehouse
        3 => 2,    // Staff          → Quality Control
        4 => 3,    // Production     → Production
        5 => 4,    // Procurement    → Procurement
        6 => 5,    // R&D            → R&D
        7 => 6,    // Operator       → Finished Goods
        default => null
    };
}
}
