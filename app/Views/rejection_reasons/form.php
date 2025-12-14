<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="content p-1">
  <h3><?= isset($reason) ? 'Edit Rejection Reason' : 'Add Rejection Reason' ?></h3>

  <form method="post" action="<?= isset($reason) ? base_url('/rejection-reasons/save/'.$reason['id']) : base_url('/rejection-reasons/save') ?>">
    <?= csrf_field() ?>

    <div class="mb-3"><label>Reason</label>
      <input type="text" name="reason" value="<?= $reason['reason'] ?? '' ?>" class="form-control" required>
    </div>

    <div class="mb-3"><label>Category</label>
      <input type="text" name="category" value="<?= $reason['category'] ?? '' ?>" class="form-control">
    </div>

    <div class="mb-3"><label>Status</label>
      <select name="status" class="form-select">
        <option value="Active" <?= (isset($reason['status']) && $reason['status']=='Active')?'selected':'' ?>>Active</option>
        <option value="Inactive" <?= (isset($reason['status']) && $reason['status']=='Inactive')?'selected':'' ?>>Inactive</option>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Save</button>
    <a href="<?= base_url('/rejection-reasons/list') ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</div>
<?= $this->include('layout/footer') ?>
