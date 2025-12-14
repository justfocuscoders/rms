<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="content p-4">
  <h3>Item Details</h3>

  <table class="table table-bordered">
    <tr>
      <th>ID</th>
      <td><?= esc($item['id']) ?></td>
    </tr>
    <tr>
      <th>Code</th>
      <td><?= esc($item['code']) ?></td>
    </tr>
    <tr>
      <th>Name</th>
      <td><?= esc($item['name']) ?></td>
    </tr>
    <tr>
      <th>Unit</th>
      <td><?= esc($item['unit_name']) ?></td>
    </tr>
    <tr>
      <th>Unit Price</th>
      <td><?= esc(number_format($item['unit_price'], 2)) ?></td>
    </tr>
    <tr>
      <th>Reorder Level</th>
      <td><?= esc($item['reorder_level']) ?></td>
    </tr>
    <tr>
      <th>HSN Code</th>
      <td><?= esc($item['hsn_code']) ?></td>
    </tr>
    <tr>
      <th>Status</th>
      <td>
        <?php if($item['is_active'] == 1): ?>
          <span class="badge bg-success">Active</span>
        <?php else: ?>
          <span class="badge bg-secondary">Inactive</span>
        <?php endif; ?>
      </td>
    </tr>
    <tr>
      <th>Department</th>
      <td><?= esc($item['dept_name']) ?></td>
    </tr>
    <tr>
      <th>Description</th>
      <td><?= nl2br(esc($item['description'])) ?></td>
    </tr>
    <tr>
      <th>Item Image</th>
      <td>
        <?php if (!empty($item['image'])): ?>
          <img src="<?= base_url('uploads/items/'.$item['image']) ?>" alt="<?= esc($item['name']) ?>" style="max-width:150px;">
        <?php else: ?>
          <span class="text-muted">No image uploaded</span>
        <?php endif; ?>
      </td>
    </tr>
    <tr>
      <th>Created At</th>
      <td><?= esc($item['created_at']) ?></td>
    </tr>
    <tr>
      <th>Updated At</th>
      <td><?= esc($item['updated_at']) ?></td>
    </tr>
  </table>

  <a href="<?= base_url('/items/form/'.$item['id']) ?>" class="btn btn-warning">Edit</a>
  <a href="<?= base_url('/items/list') ?>" class="btn btn-secondary">Back</a>
</div>
</div>
<?= $this->include('layout/footer') ?>
