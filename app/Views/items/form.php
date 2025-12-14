<?= $this->include('layout/header') ?>
<div class="container-fluid pt-3 pb-5">
<div class="container py-4 mb-5">
  <h3><?= isset($item['id']) ? 'Edit Item' : 'Add Item' ?></h3>

  <form method="post" action="<?= base_url('/items/save'.(isset($item['id']) ? '/' . $item['id'] : '')) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $item['id'] ?? '' ?>">

    <!-- Existing Fields -->
    <div class="mb-3">
      <label>Code</label>
      <input type="text" name="code" class="form-control" value="<?= esc($item['code'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label>Name</label>
      <input type="text" name="name" class="form-control" value="<?= esc($item['name'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label>Unit</label>
      <select name="unit_id" class="form-control" required>
        <option value="">-- Select Unit --</option>
        <?php foreach($units as $u): ?>
        <option value="<?= $u['id'] ?>" <?= isset($item['unit_id']) && $item['unit_id']==$u['id'] ? 'selected' : '' ?>>
          <?= esc($u['name']) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
  <label>Storage Condition</label>
  <select name="storage_condition_id" class="form-control" required>
    <option value="">-- Select Storage Condition --</option>
    <?php foreach ($conditions as $c): ?>
      <option value="<?= $c['id'] ?>"
        <?= isset($item['storage_condition_id']) && $item['storage_condition_id'] == $c['id'] ? 'selected' : '' ?>>
        <?= esc($c['condition_name']) ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>


    <div class="mb-3">
      <label>Department</label>
      <select name="department_id" class="form-control">
        <option value="">-- Select Department --</option>
        <?php foreach($departments as $d): ?>
        <option value="<?= $d['id'] ?>" <?= isset($item['department_id']) && $item['department_id']==$d['id'] ? 'selected' : '' ?>>
          <?= esc($d['name']) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label>Description</label>
      <textarea name="description" class="form-control"><?= esc($item['description'] ?? '') ?></textarea>
    </div>

    <!-- ✅ New Fields (Optional Enhancements) -->
    <div class="mb-3">
      <label>Unit Price</label>
      <input type="number" step="0.01" name="unit_price" class="form-control" value="<?= esc($item['unit_price'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label>Reorder Level</label>
      <input type="number" name="reorder_level" class="form-control" value="<?= esc($item['reorder_level'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label>HSN Code</label>
      <input type="text" name="hsn_code" class="form-control" value="<?= esc($item['hsn_code'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label>Status</label>
      <select name="is_active" class="form-control">
        <option value="1" <?= (isset($item['is_active']) && $item['is_active']==1) ? 'selected' : '' ?>>Active</option>
        <option value="0" <?= (isset($item['is_active']) && $item['is_active']==0) ? 'selected' : '' ?>>Inactive</option>
      </select>
    </div>

    <div class="mb-3">
      <label>Item Image</label>
      <input type="file" name="image" class="form-control">
      <?php if (!empty($item['image'])): ?>
        <small class="text-muted">Current: <?= esc($item['image']) ?></small>
      <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-success">Save</button>
    <a href="<?= base_url('/items/list') ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</div>
<?= $this->include('layout/footer') ?>
