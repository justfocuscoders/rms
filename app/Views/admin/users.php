<?= $this->include('layout/header') ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Management</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Boxicons -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body { background: #f8f9fa; }
    .card { animation: fadeInUp 0.8s ease; border-radius: 15px; }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    table th { background: #f1f3f5; }
    .btn-sm { padding: 4px 10px; }
  </style>
</head>
<body>
    <div class="container-fluid pt-1 pb-5">
<div class="container mt-1">

  <!-- Pending Users -->
  <div class="card shadow-lg mb-4">
    <div class="card-header bg-warning text-white d-flex align-items-center">
      <i class="bx bxs-user-plus me-2"></i>
      <h5 class="mb-0">Pending User Requests</h5>
    </div>
    <div class="card-body">
      <?php if(empty($pending)): ?>
        <p class="text-muted">No pending requests 🎉</p>
      <?php else: ?>
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Role Requested</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
    <?php foreach($pending as $u): ?>
      <tr>
        <td><?= esc($u['name']) ?></td>
        <td><?= esc($u['email']) ?></td>
        <td><span class="badge bg-info"><?= ucfirst($u['role_name'] ?? 'N/A') ?></span></td>
        <td>

          <!-- Hidden Approve/Reject Forms -->
          <form id="approveForm<?= $u['id'] ?>" 
                action="<?= site_url('admin/users/approve/'.$u['id']) ?>" 
                method="post" 
                style="display:none;">
            <?= csrf_field() ?>
          </form>

          <form id="rejectForm<?= $u['id'] ?>" 
                action="<?= site_url('admin/users/reject/'.$u['id']) ?>" 
                method="post" 
                style="display:none;">
            <?= csrf_field() ?>
          </form>

          <button class="btn btn-sm btn-success btn-approve" data-id="<?= $u['id'] ?>">
            <i class="bx bx-check"></i> Approve
          </button>

          <button class="btn btn-sm btn-danger btn-reject" data-id="<?= $u['id'] ?>">
            <i class="bx bx-x"></i> Reject
          </button>

        </td>
      </tr>
    <?php endforeach; ?>
</tbody>

        </table>
      <?php endif; ?>
    </div>
  </div>

  <!-- Active Users -->
  <div class="card shadow-lg">
    <div class="card-header bg-success text-white d-flex align-items-center">
      <i class="bx bxs-user-detail me-2"></i>
      <h5 class="mb-0">Active Users</h5>
    </div>
    <div class="card-body">
      <?php if(empty($active)): ?>
        <p class="text-muted">No active users yet.</p>
      <?php else: ?>
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Change Role</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($active as $u): ?>
              <tr>
                <td><?= esc($u['name']) ?></td>
                <td><?= esc($u['email']) ?></td>
                <td><span class="badge bg-primary"><?= ucfirst($u['role_name']) ?></span></td>
                <td>
                  <form action="/admin/users/role/<?= $u['id'] ?>" method="post" class="role-form d-flex gap-2">
                    <?= csrf_field() ?>
                    <select name="role_id" class="form-select form-select-sm">
                      <?php foreach($roles as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= $u['role_id']==$r['id']?'selected':'' ?>>
                          <?= ucfirst($r['name']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-primary btn-role-update">
                      <i class="bx bx-refresh"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

</div>
</div>

<?= $this->include('layout/footer') ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
  // Approve
  document.querySelectorAll(".btn-approve").forEach(btn => {
    btn.addEventListener("click", () => {
      let userId = btn.getAttribute("data-id");
      Swal.fire({
        title: "Approve User?",
        text: "This will activate the user account.",
        icon: "success",
        showCancelButton: true,
        confirmButtonText: "Yes, Approve",
        cancelButtonText: "Cancel"
      }).then(result => {
        if(result.isConfirmed){
          document.getElementById("approveForm" + userId).submit();
        }
      });
    });
  });

  // Reject
  document.querySelectorAll(".btn-reject").forEach(btn => {
    btn.addEventListener("click", () => {
      let userId = btn.getAttribute("data-id");
      Swal.fire({
        title: "Reject User?",
        text: "This will permanently remove the user request.",
        icon: "error",
        showCancelButton: true,
        confirmButtonText: "Yes, Reject",
        cancelButtonText: "Cancel"
      }).then(result => {
        if(result.isConfirmed){
          document.getElementById("rejectForm" + userId).submit();
        }
      });
    });
  });

  // Role Update Confirmation
  document.querySelectorAll(".role-form").forEach(form => {
    form.addEventListener("submit", e => {
      e.preventDefault();
      Swal.fire({
        title: "Change Role?",
        text: "Are you sure you want to update this user role?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, Update",
        cancelButtonText: "Cancel"
      }).then(result => {
        if(result.isConfirmed){
          form.submit();
        }
      });
    });
  });

  // Toasts (Feedback)
  <?php if(session()->getFlashdata('success')): ?>
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: '<?= session()->getFlashdata('success') ?>',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true
    });
  <?php endif; ?>

  <?php if(session()->getFlashdata('error')): ?>
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: '<?= session()->getFlashdata('error') ?>',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true
    });
  <?php endif; ?>
});
</script>

</body>
</html>
