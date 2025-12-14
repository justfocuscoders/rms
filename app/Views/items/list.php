<?= $this->include('layout/header') ?>

<div class="container-fluid pt-3 pb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Items</h3>
    <a href="<?= base_url('/items/form') ?>" class="btn btn-primary">Add Item</a>
  </div>

  <!-- Flash message -->
  <?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>

  <!-- Search bar -->
  <form method="get" action="<?= base_url('/items/list') ?>" class="mb-3 d-flex gap-2">
    <?= csrf_field() ?>
    <input type="text" name="search" class="form-control" placeholder="Search items..."
           value="<?= esc($search) ?>">
    <button type="submit" class="btn btn-outline-secondary">Search</button>
    <a href="<?= base_url('/items/list') ?>" class="btn btn-outline-danger">Reset</a>
  </form>

  <!-- Table -->
  <table class="table table-bordered table-striped align-middle">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Code</th>
        <th>Name</th>
        <th>Unit</th>
        <th>Unit Price</th> <!-- ✅ new -->
        <th>Reorder Level</th> <!-- ✅ new -->
        <th>HSN Code</th> <!-- ✅ new -->
        <th>Status</th> <!-- ✅ new -->
        <th>Department</th>
        <th width="220">Actions</th>
      </tr>
    </thead>

    <tbody>
    <?php if(empty($items)): ?>
      <tr>
        <td colspan="10" class="text-center text-muted">No items found</td>
      </tr>
    <?php else: ?>
      <?php foreach($items as $i): ?>
        <tr>
          <td><?= $i['id'] ?></td>
          <td><?= esc($i['code']) ?></td>
          <td><?= esc($i['name']) ?></td>
          <td><?= esc($i['unit_name']) ?></td>
          <td><?= esc(number_format($i['unit_price'], 2)) ?></td>
          <td><?= esc($i['reorder_level']) ?></td>
          <td><?= esc($i['hsn_code']) ?></td>
          <td>
            <?php if($i['is_active'] == 1): ?>
              <span class="badge bg-success">Active</span>
            <?php else: ?>
              <span class="badge bg-secondary">Inactive</span>
            <?php endif; ?>
          </td>
          <td><?= esc($i['dept_name']) ?></td>
          <td>
            <div class="btn-group">
              <a href="<?= base_url('/items/view/'.$i['id']) ?>" class="btn btn-sm btn-info">View</a>
              <a href="<?= base_url('/items/form/'.$i['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
              <a href="<?= base_url('/items/delete/'.$i['id']) ?>" class="btn btn-sm btn-danger"
                 onclick="return confirm('Delete this item?')">Delete</a>
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
