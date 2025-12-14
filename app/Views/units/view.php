<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
  <div class="content p-4">

    <?php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => '/dashboard'],
        ['title' => 'Units', 'url' => '/units/list'],
        ['title' => 'View Unit']
    ];
    echo view('layout/breadcrumb', compact('breadcrumbs'));
    ?>

    <div class="content p-5">
      <h3>Unit Details</h3>

      <table class="table table-bordered">
        <tr><th>ID</th><td><?= esc($unit['id']) ?></td></tr>
        <tr><th>Name</th><td><?= esc($unit['name']) ?></td></tr>
        <tr><th>Symbol</th><td><?= esc($unit['symbol'] ?? '-') ?></td></tr>
        <tr><th>Description</th><td><?= esc($unit['description'] ?? '-') ?></td></tr>
        <tr><th>Status</th>
            <td>
              <?php if (($unit['status'] ?? 'active') === 'inactive'): ?>
                <span class="badge bg-danger">Inactive</span>
              <?php else: ?>
                <span class="badge bg-success">Active</span>
              <?php endif; ?>
            </td>
        </tr>
        <tr><th>Created At</th><td><?= esc($unit['created_at'] ?? '-') ?></td></tr>
        <tr><th>Updated At</th><td><?= esc($unit['updated_at'] ?? '-') ?></td></tr>
      </table>

      <a href="<?= base_url('/units/form/'.$unit['id']) ?>" class="btn btn-warning">
        <i class="bx bx-edit me-1"></i> Edit
      </a>
      <a href="<?= base_url('/units/list') ?>" class="btn btn-secondary">
        <i class="bx bx-arrow-back me-1"></i> Back
      </a>
    </div>
  </div>
</div>
<?= $this->include('layout/footer') ?>
