<?= $this->include('layout/header') ?>
<div class="container-fluid pt-3 pb-5">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Units</h3>
    <a href="<?= base_url('/units/form') ?>" class="btn btn-primary">
      <i class="bx bx-plus me-1"></i> Add Unit
    </a>
  </div>

  <!-- Flash message -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>

  <!-- Search bar -->
  <form method="get" action="<?= base_url('/units/list') ?>" class="mb-3 d-flex gap-2">
    <?= csrf_field() ?>
    <input type="text" name="search" class="form-control" placeholder="Search units..."
           value="<?= esc($search) ?>">
    <button type="submit" class="btn btn-outline-secondary">Search</button>
    <a href="<?= base_url('/units/list') ?>" class="btn btn-outline-danger">Reset</a>
  </form>

  <!-- Table -->
  <table class="table table-bordered table-striped align-middle table-hover">
    <thead class="table-light text-center">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Symbol</th>
        <th>Description</th>
        <th>Status</th>
        <th width="200">Actions</th>
      </tr>
    </thead>
    <tbody class="text-center">
      <?php if (empty($units)): ?>
        <tr>
          <td colspan="6" class="text-center text-muted">No units found</td>
        </tr>
      <?php else: ?>
        <?php foreach ($units as $u): ?>
          <tr>
            <td><?= esc($u['id']) ?></td>
            <td><?= esc($u['name']) ?></td>
            <td><?= esc($u['symbol'] ?? '-') ?></td>
            <td><?= esc($u['description'] ?? '-') ?></td>
            <td>
              <?php if (($u['status'] ?? '') === 'inactive'): ?>
                <span class="badge bg-danger">Inactive</span>
              <?php else: ?>
                <span class="badge bg-success">Active</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="btn-group">
                <a href="<?= base_url('/units/view/'.$u['id']) ?>" 
                   class="btn btn-outline-info btn-sm" title="View">
                  <i class="bx bx-show"></i>
                </a>
                <a href="<?= base_url('/units/form/'.$u['id']) ?>" 
                   class="btn btn-outline-warning btn-sm" title="Edit">
                  <i class="bx bx-edit-alt"></i>
                </a>
                <a href="<?= base_url('/units/delete/'.$u['id']) ?>" 
                   class="btn btn-outline-danger btn-sm" title="Delete"
                   onclick="return confirm('Delete this unit?')">
                  <i class="bx bx-trash"></i>
                </a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <div class="mt-3">
    <?= $pager->links() ?>
  </div>

</div>

<style>
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
.badge {
  font-size: 0.8rem;
  padding: 0.45em 0.65em;
}
.btn-group .btn {
  border-radius: 5px !important;
}
</style>

<?= $this->include('layout/footer') ?>
