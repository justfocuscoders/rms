<?= $this->include('layout/header') ?>

<div class="container-fluid pt-1 pb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Suppliers</h3>
    <a href="<?= base_url('/suppliers/form') ?>" class="btn btn-primary">Add Supplier</a>
  </div>

  <!-- Flash message -->
  <?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>

  <!-- Search bar -->
  <form method="get" action="<?= base_url('/suppliers/list') ?>" class="mb-3 d-flex gap-2">
    <input type="text" name="search" class="form-control" placeholder="Search suppliers..." value="<?= esc($search) ?>">
    <button type="submit" class="btn btn-outline-secondary">Search</button>
    <a href="<?= base_url('/suppliers/list') ?>" class="btn btn-outline-danger">Reset</a>
  </form>

  <!-- Table -->
  <table class="table table-bordered table-striped align-middle">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Contact</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Address</th>
        <th>GST No</th> <!-- 🆕 -->
        <th>Status</th> <!-- 🆕 -->
        <th width="180">Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php if(empty($suppliers)): ?>
      <tr>
        <td colspan="9" class="text-center text-muted">No suppliers found</td>
      </tr>
    <?php else: ?>
      <?php foreach($suppliers as $s): ?>
        <tr>
          <td><?= $s['id'] ?></td>
          <td><?= esc($s['name']) ?></td>
          <td><?= esc($s['contact_person']) ?></td>
          <td><?= esc($s['phone']) ?></td>
          <td><?= esc($s['email']) ?></td>
          <td><?= esc($s['address']) ?></td>

          <!-- 🆕 Added GST and Status -->
          <td><?= esc($s['gst_number'] ?? '-') ?></td>
          <td>
            <?php if(isset($s['status']) && $s['status'] == 1): ?>
              <span class="badge bg-success">Active</span>
            <?php else: ?>
              <span class="badge bg-secondary">Inactive</span>
            <?php endif; ?>
          </td>

          <td>
            <div class="btn-group">
              <a href="<?= base_url('/suppliers/view/'.$s['id']) ?>" class="btn btn-sm btn-info">View</a>
              <a href="<?= base_url('/suppliers/form/'.$s['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
              <a href="<?= base_url('/suppliers/delete/'.$s['id']) ?>" class="btn btn-sm btn-danger"
                 onclick="return confirm('Delete this supplier?')">Delete</a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <div class="mt-3 d-flex justify-content-end">
  <?= $pager->links('default','bootstrap_full') ?>
</div>

<?= $this->include('layout/footer') ?>
