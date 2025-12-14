<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width: 400px; width: 100%;">
    <div class="text-center mb-4">
      <i class="bx bxs-lock-open text-primary" style="font-size: 3rem;"></i>
      <h3 class="mt-2">Forgot Password</h3>
      <p class="text-muted">Enter your email to receive a reset link.</p>
    </div>

    <?php if(session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('/forgot-password') ?>">
      <?= csrf_field() ?>
      <div class="mb-3">
        <input type="email" name="email" class="form-control form-control-lg rounded-pill" placeholder="Enter your registered email" required>
      </div>
      <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm">Send Reset Link</button>
    </form>

    <div class="text-center mt-3">
      <a href="<?= base_url('/login') ?>" class="text-decoration-none small text-muted">Back to Login</a>
    </div>
  </div>
</div>
</body>
</html>
