<?= $this->include('layout/header') ?>
<div class="container-fluid pt-3 pb-5">
  <!-- Page Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-semibold mb-0 text-dark">Departments</h3>
    <a href="<?= base_url('/departments/form') ?>" class="btn btn-primary shadow-sm">
      <i class="bx bx-plus me-1"></i> Add Department
    </a>
  </div>

  <!-- Flash Message -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <!-- Search Bar -->
  <form method="get" action="<?= base_url('/departments/list') ?>" class="mb-3 d-flex gap-2">
    <?= csrf_field() ?>
    <input type="text" name="search" class="form-control" placeholder="Search departments..."
           value="<?= esc($search) ?>">
    <button type="submit" class="btn btn-outline-secondary">
      <i class="bx bx-search"></i> Search
    </button>
    <a href="<?= base_url('/departments/list') ?>" class="btn btn-outline-danger">
      <i class="bx bx-reset"></i> Reset
    </a>
  </form>

  <!-- Departments Table -->
  <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="card-body table-responsive">
      <table class="table table-bordered align-middle mb-0 table-hover">
        <thead class="table-light text-center">
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th width="180">Actions</th>
          </tr>
        </thead>
        <tbody class="text-center">
          <?php if (empty($departments)): ?>
            <tr>
              <td colspan="3" class="text-muted py-4">No departments found.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($departments as $d): ?>
              <tr>
                <td><?= $d['id'] ?></td>
                <td class="fw-medium"><?= esc($d['name']) ?></td>
                <td>
                  <div class="btn-group">
                    <!-- View -->
                    <a href="<?= base_url('/departments/view/'.$d['id']) ?>" 
                       class="btn btn-outline-info btn-sm" title="View">
                      <i class="bx bx-show"></i>
                    </a>

                    <!-- Edit -->
                    <a href="<?= base_url('/departments/form/'.$d['id']) ?>" 
                       class="btn btn-outline-warning btn-sm" title="Edit">
                      <i class="bx bx-edit-alt"></i>
                    </a>

                    <!-- Delete -->
                    <a href="<?= base_url('/departments/delete/'.$d['id']) ?>" 
                       class="btn btn-outline-danger btn-sm" title="Delete"
                       onclick="return confirm('Delete this department?')">
                      <i class="bx bx-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagination -->
  <div class="mt-3">
    <?= $pager->links() ?>
  </div>
</div>

<!-- Styling -->
<style>
.page-header h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1e293b;
}
.table th {
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.9rem;
  color: #475569;
  letter-spacing: 0.03em;
}
.table td {
  font-size: 0.92rem;
  color: #1e293b;
  vertical-align: middle;
}
.table-hover tbody tr:hover {
  background-color: #f8fafc;
  transition: 0.2s ease;
}
.btn-group .btn {
  border-radius: 6px !important;
}
.card {
  border-radius: 10px;
}
</style>

<?= $this->include('layout/footer') ?>
