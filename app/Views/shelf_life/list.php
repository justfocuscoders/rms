<?= $this->include('layout/header') ?>
<div class="container-fluid py-3">
  <h3>Shelf Life List</h3>
  <a href="<?= base_url('/shelf-life/form') ?>" class="btn btn-primary mb-3">Add Shelf Life</a>

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th><th>Item</th><th>Shelf Life (Days)</th><th>Retest (Days)</th><th>Status</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($records as $r): ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td><?= esc($r['item_name'] ?? '—') ?></td>
        <td><?= esc($r['shelf_life_days']) ?></td>
        <td><?= esc($r['retest_days']) ?></td>
        <td><?= esc($r['status']) ?></td>
        <td>
          <a href="<?= base_url('/shelf-life/view/'.$r['id']) ?>" class="btn btn-sm btn-info">View</a>
          <a href="<?= base_url('/shelf-life/form/'.$r['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="<?= base_url('/shelf-life/delete/'.$r['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?= $this->include('layout/footer') ?>
