<?= $this->include('layout/header') ?>

<div class="container py-4">
  <div class="d-flex justify-content-between mb-3">
    <h3>Storage Conditions</h3>
    <a href="<?= base_url('/storage_conditions/add') ?>" class="btn btn-primary">
      + Add Condition
    </a>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
      <?= session()->getFlashdata('success') ?>
    </div>
  <?php endif; ?>

  <table class="table table-bordered table-striped">
    <thead class="table-light">
      <tr>
        <th>#</th>
        <th>Condition</th>
        <th>Description</th>
        <th>Status</th>
        <th width="20%">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($conditions)): ?>
        <?php foreach ($conditions as $i => $c): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= esc($c['condition_name']) ?></td>
            <td><?= esc($c['description'] ?? '-') ?></td>
            <td>
              <?= $c['status'] === 'Active'
                  ? '<span class="badge bg-success">Active</span>'
                  : '<span class="badge bg-secondary">Inactive</span>' ?>
            </td>
            <td>
  <a href="<?= base_url('/storage_conditions/view/' . $c['id']) ?>"
     class="btn btn-sm btn-outline-info">View</a>

  <a href="<?= base_url('/storage_conditions/edit/' . $c['id']) ?>"
     class="btn btn-sm btn-outline-primary">Edit</a>

  <a href="<?= base_url('/storage_conditions/delete/' . $c['id']) ?>"
     class="btn btn-sm btn-outline-danger"
     onclick="return confirm('Delete this condition?')">
     Delete
  </a>
</td>

          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" class="text-center text-muted">No conditions found</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?= $this->include('layout/footer') ?>
