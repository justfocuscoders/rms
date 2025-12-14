<?= $this->include('layout/header') ?>
<div class="container-fluid py-3">
  <h3>QC Parameters List</h3>
  <a href="<?= base_url('/qc-parameters/form') ?>" class="btn btn-primary mb-3">Add Parameter</a>

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th><th>Name</th><th>Method</th><th>Unit</th><th>Min</th><th>Max</th><th>Status</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($parameters as $p): ?>
      <tr>
        <td><?= $p['id'] ?></td>
        <td><?= esc($p['name']) ?></td>
        <td><?= esc($p['method']) ?></td>
        <td><?= esc($p['unit']) ?></td>
        <td><?= esc($p['min_limit']) ?></td>
        <td><?= esc($p['max_limit']) ?></td>
        <td><?= esc($p['status']) ?></td>
        <td>
          <a href="<?= base_url('/qc-parameters/view/'.$p['id']) ?>" class="btn btn-sm btn-info">View</a>
          <a href="<?= base_url('/qc-parameters/form/'.$p['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="<?= base_url('/qc-parameters/delete/'.$p['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this parameter?')">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?= $this->include('layout/footer') ?>
