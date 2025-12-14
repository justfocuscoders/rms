<?= $this->include('layout/header') ?>

<div class="container mt-5">
  <div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">My Profile</h5>
    </div>
    <div class="card-body">
      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
      <?php elseif (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
      <?php endif; ?>

      <form method="post" action="<?= base_url('profile/update') ?>">
        <?= csrf_field() ?>

        <div class="mb-3">
          <label class="form-label">Name</label>
          <input type="text" name="name" class="form-control" value="<?= esc($user['name']) ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Email (readonly)</label>
          <input type="email" class="form-control" value="<?= esc($user['email']) ?>" readonly>
        </div>

        <div class="mb-3">
          <label class="form-label">New Password (leave blank to keep current)</label>
          <input type="password" name="password" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
      </form>
    </div>
  </div>
</div>

<?= $this->include('layout/footer') ?>
