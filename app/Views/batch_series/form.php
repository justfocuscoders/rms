<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="content p-1">
  <h3><?= isset($series) ? 'Edit Batch Series' : 'Add Batch Series' ?></h3>

  <form method="post" action="<?= isset($series) ? base_url('/batch-series/save/'.$series['id']) : base_url('/batch-series/save') ?>">
    <?= csrf_field() ?>

    <div class="mb-3"><label>Prefix</label>
      <input type="text" name="prefix" value="<?= $series['prefix'] ?? '' ?>" class="form-control">
    </div>

    <div class="mb-3"><label>Type</label>
      <input type="text" name="type" value="<?= $series['type'] ?? '' ?>" class="form-control">
    </div>

    <div class="mb-3"><label>Next Number</label>
      <input type="number" name="next_number" value="<?= $series['next_number'] ?? 1 ?>" class="form-control">
    </div>

    <div class="mb-3"><label>Format</label>
      <input type="text" name="format" value="<?= $series['format'] ?? '' ?>" class="form-control">
    </div>

    <div class="mb-3"><label>Status</label>
      <select name="status" class="form-select">
        <option value="Active" <?= (isset($series['status']) && $series['status']=='Active')?'selected':'' ?>>Active</option>
        <option value="Inactive" <?= (isset($series['status']) && $series['status']=='Inactive')?'selected':'' ?>>Inactive</option>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Save</button>
    <a href="<?= base_url('/batch-series/list') ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</div>
<?= $this->include('layout/footer') ?>
