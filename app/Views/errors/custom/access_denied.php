<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Access Denied - RMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
  <div class="text-center p-4 bg-white rounded-4 shadow-sm" style="max-width:400px;">
    <h1 class="text-danger mb-3"><i class="bx bx-block"></i> Access Denied</h1>
    <p class="text-muted mb-4">You do not have permission to access this page.</p>
    <a href="<?= base_url('/dashboard') ?>" class="btn btn-primary px-4">Return to Dashboard</a>
  </div>
</body>
</html>
