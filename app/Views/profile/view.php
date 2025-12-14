<?= $this->include('layout/header') ?>

<div class="container mt-5">
  <div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0">My Profile</h5>
      <a href="<?= base_url('/dashboard') ?>" class="btn btn-light btn-sm">Back to Dashboard</a>
    </div>
    <div class="card-body">

      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
      <?php endif; ?>

      <form method="post" action="<?= base_url('profile/update') ?>">
        <?= csrf_field() ?>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="<?= esc($user['name']) ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="<?= esc($user['email']) ?>" readonly>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Role</label>
            <input type="text" class="form-control" value="<?= esc($user['role_name'] ?? '-') ?>" readonly>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Department</label>
            <input type="text" class="form-control" value="<?= esc($user['department_name'] ?? '-') ?>" readonly>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">New Password (optional)</label>
            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Status</label>
            <?php $status = $user['status'] ? 'Active' : 'Inactive'; ?>
            <input type="text" class="form-control" value="<?= esc($status) ?>" readonly>
          </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
      </form>
    </div>
  </div>
</div>

<?= $this->include('layout/footer') ?>
