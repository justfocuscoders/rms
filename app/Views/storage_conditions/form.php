<?= $this->include('layout/header') ?>

<div class="container py-4">
  <h3><?= isset($condition) ? 'Edit Storage Condition' : 'Add Storage Condition' ?></h3>

  <form method="post"
        action="<?= base_url('/storage_conditions/save' . (!empty($condition['id']) ? '/' . $condition['id'] : '')) ?>">

    <?= csrf_field() ?>

    <div class="mb-3">
      <label class="form-label">Condition Name</label>
      <input type="text"
             name="condition_name"
             class="form-control"
             value="<?= esc($condition['condition_name'] ?? '') ?>"
             placeholder="Dry / Cold / Ventilated"
             required>
    </div>

    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description"
                class="form-control"
                rows="2"><?= esc($condition['description'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="Active" <?= (($condition['status'] ?? '') === 'Active') ? 'selected' : '' ?>>Active</option>
        <option value="Inactive" <?= (($condition['status'] ?? '') === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Save</button>
    <a href="<?= base_url('/storage_conditions/list') ?>" class="btn btn-secondary ms-2">Cancel</a>
  </form>
</div>

<?= $this->include('layout/footer') ?>
