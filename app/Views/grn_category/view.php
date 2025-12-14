<?= $this->include('layout/header') ?>

<div class="container-fluid pt-1 pb-5">
  <h3>View GRN Category</h3>

  <table class="table table-bordered w-75 mt-3">
    <tr>
      <th style="width:200px;">Name</th>
      <td><?= esc($category['name']) ?></td>
    </tr>
    <tr>
      <th>Description</th>
      <td><?= esc($category['description'] ?? '-') ?></td>
    </tr>
    <tr>
      <th>Status</th>
      <td>
        <?php if ($category['status'] === 'Active'): ?>
          <span class="badge bg-success">Active</span>
        <?php else: ?>
          <span class="badge bg-secondary">Inactive</span>
        <?php endif; ?>
      </td>
    </tr>
    <tr>
      <th>Created At</th>
      <td><?= date('d-M-Y h:i A', strtotime($category['created_at'])) ?></td>
    </tr>
  </table>

  <a href="<?= base_url('/grn-category/list') ?>" class="btn btn-secondary mt-3">Back to List</a>
</div>

<?= $this->include('layout/footer') ?>
