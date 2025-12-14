<?= $this->include('layout/header') ?>
<div class="container-fluid py-3">
  <h3>Batch Series List</h3>
  <a href="<?= base_url('/batch-series/form') ?>" class="btn btn-primary mb-3">Add Batch Series</a>

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th><th>Prefix</th><th>Type</th><th>Next Number</th><th>Format</th><th>Status</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($series as $s): ?>
      <tr>
        <td><?= $s['id'] ?></td>
        <td><?= esc($s['prefix']) ?></td>
        <td><?= esc($s['type']) ?></td>
        <td><?= esc($s['next_number']) ?></td>
        <td><?= esc($s['format']) ?></td>
        <td><?= esc($s['status']) ?></td>
        <td>
          <a href="<?= base_url('/batch-series/view/'.$s['id']) ?>" class="btn btn-sm btn-info">View</a>
          <a href="<?= base_url('/batch-series/form/'.$s['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="<?= base_url('/batch-series/delete/'.$s['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this series?')">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?= $this->include('layout/footer') ?>
