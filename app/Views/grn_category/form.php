<?= $this->include('layout/header') ?>

<div class="container-fluid pt-1 pb-5">
  <h3><?= isset($category) ? 'Edit GRN Category' : 'Add GRN Category' ?></h3>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <form method="post" action="<?= base_url('/grn-category/save' . (isset($category['id']) ? '/' . $category['id'] : '')) ?>">
    <div class="mb-3">
      <label class="form-label">Name <span class="text-danger">*</span></label>
      <input type="text" name="name" class="form-control"
             value="<?= esc($category['name'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control"><?= esc($category['description'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="Active" <?= (isset($category['status']) && $category['status'] == 'Active') ? 'selected' : '' ?>>Active</option>
        <option value="Inactive" <?= (isset($category['status']) && $category['status'] == 'Inactive') ? 'selected' : '' ?>>Inactive</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
    <a href="<?= base_url('/grn-category/list') ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?= $this->include('layout/footer') ?>
