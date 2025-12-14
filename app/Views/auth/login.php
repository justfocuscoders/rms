<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Boxicons -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

  <style>
    body { background: #f8f9fa; }
    .card { animation: fadeIn 1s ease-in-out; }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
<?php
// 🧠 Prevent browser caching for login page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width: 400px; width: 100%;">
    <div class="text-center mb-4">
      <i class="bx bxs-lock-alt text-primary" style="font-size: 3rem;"></i>
      <h3 class="mt-2">Login</h3>
      <p class="text-muted">Welcome back! Please login to your account.</p>
    </div>

    <?php if(session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('login') ?>">
  <?= csrf_field() ?>
      <div class="mb-3">
        <input type="email" name="email" class="form-control form-control-lg rounded-pill" placeholder="Email" required>
      </div>
      <!-- Password with eye toggle -->
<div class="mb-3 position-relative">
  <div class="input-group">
    <input id="password" type="password" name="password"
           class="form-control form-control-lg rounded-start-pill"
           placeholder="Password" required>
    <span class="input-group-text bg-white rounded-end-pill" id="togglePassword" style="cursor: pointer;">
      <i class="bx bx-show"></i>
    </span>
  </div>
</div>

      <div class="text-end mb-3">
  <a href="<?= base_url('/forgot-password') ?>" class="text-decoration-none small text-primary">Forgot Password?</a>
</div>

      <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm">Login</button>
    </form>

    <div class="text-center mt-3">
      <p class="mb-0">Don’t have an account? 
        <a href="<?= base_url('/signup') ?>" class="fw-bold text-decoration-none">Sign Up</a>
      </p>
    </div>
  </div>
</div>

<!-- ✅ Validation + Clear fields on back/refresh -->
<script>
document.querySelector("form").addEventListener("submit", function (e) {
  const email = document.querySelector("input[name='email']");
  const password = document.querySelector("input[name='password']");
  let errors = [];

  // Email validation
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!email.value.trim()) {
    errors.push("Email is required.");
  } else if (!emailPattern.test(email.value.trim())) {
    errors.push("Enter a valid email address.");
  }

  // Password validation
  if (!password.value.trim()) {
    errors.push("Password is required.");
  } else if (password.value.trim().length < 6) {
    errors.push("Password must be at least 6 characters long.");
  }

  // Show errors
  if (errors.length > 0) {
    e.preventDefault();
    alert(errors.join("\n"));
  }
});

// ✅ Clear input fields when user revisits via back/refresh
window.addEventListener('pageshow', function(event) {
  // If page was restored from browser cache, reload it
  if (event.persisted) {
    window.location.reload();
  } else {
    // Otherwise, clear form fields
    document.querySelectorAll('input').forEach(input => input.value = '');
  }
});

// ---------- Show/Hide Password ----------
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');

togglePassword.addEventListener('click', () => {
  const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
  passwordInput.setAttribute('type', type);
  
  const icon = togglePassword.querySelector('i');
  icon.classList.toggle('bx-show');
  icon.classList.toggle('bx-hide');
});

</script>

<style>
    #togglePassword i {
  font-size: 1.2rem;
  color: #6c757d;
}
#togglePassword:hover i {
  color: #0d6efd; /* Bootstrap blue on hover */
}

</style>
</body>
</html>
