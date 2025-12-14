<?= $this->include('layout/header') ?>

<div class="container-fluid pt-1 pb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>GRN Categories</h3>
    <a href="<?= base_url('/grn-category/form') ?>" class="btn btn-primary">Add Category</a>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>

  <form method="get" action="<?= base_url('/grn-category/list') ?>" class="mb-3 d-flex gap-2">
    <input type="text" name="search" class="form-control" placeholder="Search categories..."
           value="<?= esc($search) ?>">
    <button type="submit" class="btn btn-outline-secondary">Search</button>
    <a href="<?= base_url('/grn-category/list') ?>" class="btn btn-outline-danger">Reset</a>
  </form>

  <table class="table table-bordered table-striped align-middle">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Description</th>
        <th>Status</th>
        <th>Created At</th>
        <th width="180">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($categories)): ?>
        <tr><td colspan="6" class="text-center text-muted">No categories found</td></tr>
      <?php else: ?>
        <?php foreach ($categories as $c): ?>
          <tr>
            <td><?= $c['id'] ?></td>
            <td><?= esc($c['name']) ?></td>
            <td><?= esc($c['description'] ?? '-') ?></td>
            <td>
              <?php if ($c['status'] === 'Active'): ?>
                <span class="badge bg-success">Active</span>
              <?php else: ?>
                <span class="badge bg-secondary">Inactive</span>
              <?php endif; ?>
            </td>
            <td><?= date('d-M-Y', strtotime($c['created_at'])) ?></td>
            <td>
              <div class="btn-group">
                <a href="<?= base_url('/grn-category/view/'.$c['id']) ?>" class="btn btn-sm btn-info">View</a>
                <a href="<?= base_url('/grn-category/form/'.$c['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="<?= base_url('/grn-category/delete/'.$c['id']) ?>" class="btn btn-sm btn-danger"
                   onclick="return confirm('Delete this category?')">Delete</a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="mt-3"><?= $pager->links() ?></div>
</div>

<?= $this->include('layout/footer') ?>
