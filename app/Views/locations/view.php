<?= $this->include('layout/header') ?>

<div class="container-fluid pt-1 pb-5">
<div class="content p-4">

  <h3>Location Details</h3>

  <table class="table table-bordered">
    <tr>
      <th>ID</th>
      <td><?= esc($location['id']) ?></td>
    </tr>

    <tr>
      <th>Code</th>
      <td><?= esc($location['code']) ?></td>
    </tr>

    <tr>
      <th>Name</th>
      <td><?= esc($location['name']) ?></td>
    </tr>

    <tr>
      <th>Type</th>
      <td><?= esc($location['type'] ?? '-') ?></td>
    </tr>

    <tr>
      <th>Capacity</th>
      <td><?= esc($location['capacity'] ?? '-') ?></td>
    </tr>

    <tr>
      <th>Storage Conditions</th>
      <td><?= esc($location['conditions'] ?? '-') ?></td>
    </tr>

    <tr>
      <th>Status</th>
      <td>
        <?php if (($location['status'] ?? 1) == 1): ?>
          <span class="badge bg-success">Active</span>
        <?php else: ?>
          <span class="badge bg-secondary">Inactive</span>
        <?php endif; ?>
      </td>
    </tr>

    <tr>
      <th>Created At</th>
      <td><?= date('d M Y, h:i A', strtotime($location['created_at'])) ?></td>
    </tr>
  </table>

  <a href="<?= base_url('/locations/edit/'.$location['id']) ?>" class="btn btn-warning">
    Edit
  </a>

  <a href="<?= base_url('/locations') ?>" class="btn btn-secondary">
    Back
  </a>

</div>
</div>

<?= $this->include('layout/footer') ?>
