<?= $this->include('layout/header') ?>
<div class="container-fluid py-3">
  <h3>Return Reasons List</h3>
  <a href="<?= base_url('/return-reasons/form') ?>" class="btn btn-primary mb-3">Add Return Reason</a>

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th><th>Reason</th><th>Type</th><th>Status</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($reasons as $r): ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td><?= esc($r['reason']) ?></td>
        <td><?= esc($r['type']) ?></td>
        <td><?= esc($r['status']) ?></td>
        <td>
          <a href="<?= base_url('/return-reasons/view/'.$r['id']) ?>" class="btn btn-sm btn-info">View</a>
          <a href="<?= base_url('/return-reasons/form/'.$r['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="<?= base_url('/return-reasons/delete/'.$r['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this reason?')">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?= $this->include('layout/footer') ?>
