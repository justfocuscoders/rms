<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width: 400px; width: 100%;">
    <div class="text-center mb-4">
      <i class="bx bxs-key text-success" style="font-size: 3rem;"></i>
      <h3 class="mt-2">Reset Password</h3>
      <p class="text-muted">Enter a new password for your account.</p>
    </div>

    <?php if(session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('/reset-password') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="token" value="<?= esc($token) ?>">
      <div class="mb-3">
        <input type="password" name="password" class="form-control form-control-lg rounded-pill" placeholder="New Password" required>
      </div>
      <div class="mb-3">
        <input type="password" name="confirm_password" class="form-control form-control-lg rounded-pill" placeholder="Confirm Password" required>
      </div>
      <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill shadow-sm">Update Password</button>
    </form>
  </div>
</div>
</body>
</html>
