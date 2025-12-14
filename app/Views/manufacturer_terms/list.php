<?= $this->include('layout/header') ?>
<div class="container-fluid py-3">
  <h3>Manufacturer Terms List</h3>
  <a href="<?= base_url('/manufacturer-terms/form') ?>" class="btn btn-primary mb-3">Add Term</a>

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th><th>Manufacturer</th><th>Term Name</th><th>Validity (Days)</th><th>Status</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($terms as $t): ?>
      <tr>
        <td><?= $t['id'] ?></td>
        <td><?= esc($t['manufacturer_name'] ?? '—') ?></td>
        <td><?= esc($t['term_name']) ?></td>
        <td><?= esc($t['validity_days']) ?></td>
        <td><?= esc($t['status']) ?></td>
        <td>
          <a href="<?= base_url('/manufacturer-terms/view/'.$t['id']) ?>" class="btn btn-sm btn-info">View</a>
          <a href="<?= base_url('/manufacturer-terms/form/'.$t['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="<?= base_url('/manufacturer-terms/delete/'.$t['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this term?')">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?= $this->include('layout/footer') ?>
