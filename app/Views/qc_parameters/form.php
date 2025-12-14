<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="content p-1">
  <h3><?= isset($parameter) ? 'Edit QC Parameter' : 'Add QC Parameter' ?></h3>

  <form method="post" action="<?= isset($parameter) ? base_url('/qc-parameters/save/'.$parameter['id']) : base_url('/qc-parameters/save') ?>">
    <?= csrf_field() ?>

    <div class="mb-3"><label>Name</label>
      <input type="text" name="name" value="<?= $parameter['name'] ?? '' ?>" class="form-control" required>
    </div>

    <div class="mb-3"><label>Method</label>
      <input type="text" name="method" value="<?= $parameter['method'] ?? '' ?>" class="form-control">
    </div>

    <div class="mb-3"><label>Unit</label>
      <input type="text" name="unit" value="<?= $parameter['unit'] ?? '' ?>" class="form-control">
    </div>

    <div class="mb-3"><label>Minimum Limit</label>
      <input type="number" step="0.01" name="min_limit" value="<?= $parameter['min_limit'] ?? '' ?>" class="form-control">
    </div>

    <div class="mb-3"><label>Maximum Limit</label>
      <input type="number" step="0.01" name="max_limit" value="<?= $parameter['max_limit'] ?? '' ?>" class="form-control">
    </div>

    <div class="mb-3"><label>Status</label>
      <select name="status" class="form-select">
        <option value="Active" <?= (isset($parameter['status']) && $parameter['status']=='Active')?'selected':'' ?>>Active</option>
        <option value="Inactive" <?= (isset($parameter['status']) && $parameter['status']=='Inactive')?'selected':'' ?>>Inactive</option>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Save</button>
    <a href="<?= base_url('/qc-parameters/list') ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</div>
<?= $this->include('layout/footer') ?>
