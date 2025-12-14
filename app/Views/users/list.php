<?= $this->include('layout/header') ?>

<div class="container mt-4">
  <h3 class="mb-3"><?= esc($title) ?></h3>

  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Department</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($users)): ?>
        <?php foreach ($users as $user): ?>
          <tr>
            <td><?= esc($user['id']) ?></td>
            <td><?= esc($user['name']) ?></td>
            <td><?= esc($user['email']) ?></td>
            <td><?= esc($user['role_name']) ?></td>
            <td><?= esc($user['department_name']) ?></td>
            <td><?= $user['status'] ? 'Active' : 'Inactive' ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6" class="text-center">No users found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?= $this->include('layout/footer') ?>
