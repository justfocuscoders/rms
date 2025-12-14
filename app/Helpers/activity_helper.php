<?php
use App\Models\UserActivityModel;

if (!function_exists('log_activity')) {
    function log_activity(string $action, string $module = null, int $recordId = null): void
    {
        $userId = session('user_id');
        if (!$userId) return; // No session = no log

        $model = new UserActivityModel();
        $model->insert([
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);
    }
}
