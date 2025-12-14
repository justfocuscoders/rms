<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signup</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Boxicons -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

  <style>
    body { background: #f8f9fa; }
    .card { animation: slideIn 1s ease-in-out; }
    @keyframes slideIn {
      from { opacity: 0; transform: translateX(-30px); }
      to   { opacity: 1; transform: translateX(0); }
    }
    .progress { height: 8px; }
  </style>
</head>
<body>
    
    <?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

    
<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width: 450px; width: 100%;">
    <div class="text-center mb-4">
      <i class="bx bxs-user-plus text-success" style="font-size: 3rem;"></i>
      <h3 class="mt-2">Signup Request</h3>
      <p class="text-muted">Fill in the details to request access.</p>
    </div>

    <?php if(session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('/signup') ?>">
      <?= csrf_field() ?>
      <!-- Full Name -->
      <div class="mb-3">
        <input type="text" name="name" class="form-control form-control-lg rounded-pill" placeholder="Full Name" required>
      </div>

      <!-- Email with suggestion -->
      <div class="mb-3 position-relative">
        <input id="email" type="text" name="email" class="form-control form-control-lg rounded-pill" placeholder="Email Address (must include @ and end with .com)" required>

        <small id="emailSuggestion" class="form-text text-muted d-none" style="cursor:pointer;"></small>
      </div>

      
      <!-- Password with eye toggle + strength bar -->
<div class="mb-3 position-relative">
  <div class="input-group">
    <input id="password" type="password" name="password"
           class="form-control form-control-lg rounded-start-pill"
           placeholder="Password" required>
    <span class="input-group-text bg-white rounded-end-pill" id="togglePassword" style="cursor: pointer;">
      <i class="bx bx-show"></i>
    </span>
  </div>
  
  <div class="mt-2">
    <div class="progress">
      <div id="pwdStrengthBar" class="progress-bar bg-secondary" role="progressbar" style="width:0%"></div>
    </div>
    <small id="pwdStrengthText" class="form-text text-muted">
      Use at least 6 characters with numbers & symbols.
    </small>
  </div>
</div>


      <!-- Role -->
<div class="mb-3">
  <select name="role_id" class="form-control form-control-lg rounded-pill" required>
    <option value="">-- Select Role --</option>
    <option value="2">Warehouse</option>
    <option value="3">Quality Control</option>
    <option value="4">Production</option>
    <option value="5">Procurement</option>
  </select>
</div>


      <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill shadow-sm">Request Signup</button>
    </form>

    <div class="text-center mt-3">
      <p class="mb-0">Already have an account? 
        <a href="<?= base_url('/login') ?>" class="fw-bold text-decoration-none">Login</a>
      </p>
    </div>
  </div>
</div>

<!-- ✅ Validation + Email Suggestion + Password Strength -->
<script>
// ---------- Email Suggestion ----------
const commonDomains = ['gmail.com','yahoo.com','hotmail.com','outlook.com','icloud.com','company.com'];
const emailInput = document.getElementById('email');
const emailSuggestion = document.getElementById('emailSuggestion');

function levenshtein(a, b) {
  if (!a) return b.length;
  if (!b) return a.length;
  const matrix = Array.from({length: b.length + 1}, (_, i) => [i]);
  for (let j = 0; j <= a.length; j++) matrix[0][j] = j;
  for (let i = 1; i <= b.length; i++) {
    for (let j = 1; j <= a.length; j++) {
      if (b.charAt(i-1) === a.charAt(j-1)) matrix[i][j] = matrix[i-1][j-1];
      else matrix[i][j] = Math.min(matrix[i-1][j-1] + 1, matrix[i][j-1] + 1, matrix[i-1][j] + 1);
    }
  }
  return matrix[b.length][a.length];
}

function getDomainSuggestion(email) {
  if (!email || !email.includes('@')) return null;
  const parts = email.split('@');
  const domain = parts[1].toLowerCase();
  let best = {d: null, dist: 999};
  for (const cd of commonDomains) {
    const dist = levenshtein(domain, cd);
    if (dist < best.dist && dist > 0 && dist <= 3) best = {d: cd, dist};
  }
  return best.d;
}

emailInput?.addEventListener('input', () => {
  const val = emailInput.value.trim();
  const suggestion = getDomainSuggestion(val);
  if (suggestion) {
    emailSuggestion.classList.remove('d-none');
    emailSuggestion.innerHTML = `Did you mean <strong>${val.split('@')[0]}@${suggestion}</strong>? Click to use suggestion.`;
    emailSuggestion.onclick = () => {
      emailInput.value = `${val.split('@')[0]}@${suggestion}`;
      emailSuggestion.classList.add('d-none');
    };
  } else {
    emailSuggestion.classList.add('d-none');
  }
});

// ---------- Password Strength ----------
const pwdInput = document.getElementById('password');
const pwdBar = document.getElementById('pwdStrengthBar');
const pwdText = document.getElementById('pwdStrengthText');

function evaluatePassword(pwd) {
  let score = 0;
  if (!pwd) return {pct: 0, label: 'Too short'};
  if (pwd.length >= 8) score += 2; else if (pwd.length >= 6) score += 1;
  if (/[A-Z]/.test(pwd)) score++;
  if (/[a-z]/.test(pwd)) score++;
  if (/\d/.test(pwd)) score++;
  if (/[^A-Za-z0-9]/.test(pwd)) score += 2;
  const pct = Math.min(100, Math.round((score / 8) * 100));
  let label = 'Very Weak';
  if (score >= 6) label = 'Strong';
  else if (score >= 4) label = 'Medium';
  else if (score >= 2) label = 'Weak';
  return {pct, label};
}

pwdInput?.addEventListener('input', () => {
  const res = evaluatePassword(pwdInput.value);
  pwdBar.style.width = res.pct + '%';
  pwdBar.classList.remove('bg-danger','bg-warning','bg-success','bg-secondary');
  pwdText.textContent = res.label + ' — mix upper, lower, numbers & symbols.';
  if (res.pct < 35) pwdBar.classList.add('bg-danger');
  else if (res.pct < 65) pwdBar.classList.add('bg-warning');
  else pwdBar.classList.add('bg-success');
});

// ---------- Form Validation ----------
document.querySelector("form").addEventListener("submit", function (e) {
  const name = document.querySelector("input[name='name']");
  const email = document.querySelector("input[name='email']");
  const password = document.querySelector("input[name='password']");
  const role = document.querySelector("select[name='role_id']");

  let errors = [];

  if (!name.value.trim()) errors.push("Full Name is required.");

  const emailPattern = /^[^@\s]+@[^@\s]+\.com$/i;
if (!email.value.trim()) errors.push("Email is required.");
else if (!emailPattern.test(email.value.trim())) errors.push("Email must contain '@' and end with '.com'.");


  if (!password.value.trim()) errors.push("Password is required.");
  else if (password.value.trim().length < 6) errors.push("Password must be at least 6 characters long.");

  if (!role.value.trim()) errors.push("Please select a role.");

  if (errors.length > 0) {
    e.preventDefault();
    alert(errors.join("\\n"));
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
  color: #198754;
}

</style>
</body>
</html>
