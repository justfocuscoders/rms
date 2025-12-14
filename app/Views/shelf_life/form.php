<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="content p-1">
  <h3><?= isset($record) ? 'Edit Shelf Life' : 'Add Shelf Life' ?></h3>

  <form method="post" action="<?= isset($record) ? base_url('/shelf-life/save/'.$record['id']) : base_url('/shelf-life/save') ?>">
    <?= csrf_field() ?>

    <div class="mb-3">
      <label>Item</label>
      <select name="item_id" class="form-select" required>
        <option value="">Select Item</option>
        <?php foreach ($items as $i): ?>
          <option value="<?= $i['id'] ?>" <?= isset($record['item_id']) && $record['item_id']==$i['id'] ? 'selected' : '' ?>><?= esc($i['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3"><label>Shelf Life (Days)</label>
      <input type="number" name="shelf_life_days" value="<?= $record['shelf_life_days'] ?? '' ?>" class="form-control">
    </div>

    <div class="mb-3"><label>Retest (Days)</label>
      <input type="number" name="retest_days" value="<?= $record['retest_days'] ?? '' ?>" class="form-control">
    </div>

    <div class="mb-3"><label>Status</label>
      <select name="status" class="form-select">
        <option value="Active" <?= (isset($record['status']) && $record['status']=='Active')?'selected':'' ?>>Active</option>
        <option value="Inactive" <?= (isset($record['status']) && $record['status']=='Inactive')?'selected':'' ?>>Inactive</option>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Save</button>
    <a href="<?= base_url('/shelf-life/list') ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</div>
<?= $this->include('layout/footer') ?>
