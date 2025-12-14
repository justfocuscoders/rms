<?= $this->include('layout/header') ?>
<div class="container-fluid py-3">
  <h3>Manufacturers List</h3>
  <a href="<?= base_url('/manufacturers/form') ?>" class="btn btn-primary mb-3">Add Manufacturer</a>

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Contact Person</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($manufacturers as $m): ?>
        <tr>
          <td><?= $m['id'] ?></td>
          <td><?= esc($m['name']) ?></td>
          <td><?= esc($m['contact_person']) ?></td>
          <td><?= esc($m['phone']) ?></td>
          <td><?= esc($m['email']) ?></td>
          <td><?= esc($m['status']) ?></td>
          <td>
            <a href="<?= base_url('/manufacturers/view/'.$m['id']) ?>" class="btn btn-sm btn-info">View</a>
            <a href="<?= base_url('/manufacturers/form/'.$m['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="<?= base_url('/manufacturers/delete/'.$m['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this manufacturer?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?= $this->include('layout/footer') ?>
