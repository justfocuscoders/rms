<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Pending Signups</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <h3>Pending Signup Requests</h3>

  <?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>
  <?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <table class="table table-striped">
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Requested Role</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($pending as $u): ?>
        <tr>
          <td><?= esc($u['id']) ?></td>
          <td><?= esc($u['name']) ?></td>
          <td><?= esc($u['email']) ?></td>
          <td>
            <?php
              $roleName = '—';
              if (!empty($u['requested_role_id']) && isset($roles)) {
                foreach ($roles as $r) if ($r['id'] == $u['requested_role_id']) { $roleName = $r['name']; break; }
              }
              echo esc($roleName);
            ?>
          </td>
          <td>
            <form method="post" action="<?= site_url('admin/signup-requests/approve/'.$u['id']) ?>" style="display:inline">
              <?= csrf_field() ?>
              <button class="btn btn-sm btn-success" type="submit">Approve</button>
            </form>
            <form method="post" action="<?= site_url('admin/signup-requests/reject/'.$u['id']) ?>" style="display:inline" onsubmit="return confirm('Reject request?');">
              <?= csrf_field() ?>
              <button class="btn btn-sm btn-danger" type="submit">Reject</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(empty($pending)): ?>
        <tr><td colspan="5" class="text-center">No pending requests</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

</div>
</body>
</html>
