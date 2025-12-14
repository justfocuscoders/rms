<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserRoleModel;
use App\Models\UserModel;

/**
 * AuthRole filter
 * - Ensures user is logged in
 * - Loads user's role(s) from user_roles (if exists) or users.role_id as fallback
 */
class AuthRole implements FilterInterface
{
    /**
     * Run before the request is processed.
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return ResponseInterface|null
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // ensure user is logged in
        $userId = session()->get('user_id');
        if (! $userId) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        $db = \Config\Database::connect();
        $userRoleIds = [];

        if ($db->tableExists('user_roles')) {
            // Many-to-many: use user_roles table if present
            $userRoleModel = new UserRoleModel();
            $rows = $userRoleModel->where('user_id', $userId)->findAll();
            $userRoleIds = array_column($rows, 'role_id');
        } else {
            // Fallback: single role stored on users.role_id
            $userModel = new UserModel();
            $user = $userModel->find($userId);
            if ($user) {
                $userRoleIds = [(int) $user['role_id']];
            }
        }

        if (empty($userRoleIds)) {
            return redirect()->to('/access-denied');
        }

        // You can attach roles to request/session here if needed:
        // session()->set('user_role_ids', $userRoleIds);

        return null; // allow request to continue
    }

    /**
     * Run after the request is processed.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing required
    }
}
