<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserActivityModel;

class ActivityLog extends BaseController
{
    public function index()
    {
        // Restrict to admin
        if (session('role_name') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $model = new UserActivityModel();
        $data['logs'] = $model->select('user_activity_log.*, users.name')
                              ->join('users', 'users.id = user_activity_log.user_id')
                              ->orderBy('user_activity_log.created_at', 'DESC')
                              ->paginate(50);
        $data['pager'] = $model->pager;

        return view('admin/activity_log', $data);
    }
}
