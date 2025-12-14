<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="content p-1">
  <h3><?= isset($term) ? 'Edit Manufacturer Term' : 'Add Manufacturer Term' ?></h3>

  <form method="post" action="<?= isset($term) ? base_url('/manufacturer-terms/save/'.$term['id']) : base_url('/manufacturer-terms/save') ?>">
    <?= csrf_field() ?>

    <div class="mb-3">
      <label>Manufacturer</label>
      <select name="manufacturer_id" class="form-select" required>
        <option value="">Select Manufacturer</option>
        <?php foreach ($manufacturers as $m): ?>
          <option value="<?= $m['id'] ?>" <?= isset($term['manufacturer_id']) && $term['manufacturer_id']==$m['id'] ? 'selected' : '' ?>>
            <?= esc($m['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label>Term Name</label>
      <input type="text" name="term_name" value="<?= $term['term_name'] ?? '' ?>" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Description</label>
      <textarea name="description" class="form-control"><?= $term['description'] ?? '' ?></textarea>
    </div>

    <div class="mb-3">
      <label>Validity (Days)</label>
      <input type="number" name="validity_days" value="<?= $term['validity_days'] ?? '' ?>" class="form-control">
    </div>

    <div class="mb-3">
      <label>Status</label>
      <select name="status" class="form-select">
        <option value="Active" <?= (isset($term['status']) && $term['status']=='Active')?'selected':'' ?>>Active</option>
        <option value="Inactive" <?= (isset($term['status']) && $term['status']=='Inactive')?'selected':'' ?>>Inactive</option>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Save</button>
    <a href="<?= base_url('/manufacturer-terms/list') ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</div>
<?= $this->include('layout/footer') ?>
