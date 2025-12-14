<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
  <div class="content p-1">
    <h3><?= isset($unit) && !empty($unit['id']) ? 'Edit Unit' : 'Add Unit' ?></h3>

    <form method="post" action="<?= base_url('/units/save' . (!empty($unit['id']) ? '/' . $unit['id'] : '')) ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= esc($unit['id'] ?? '') ?>">

      <!-- Name -->
      <div class="mb-3">
        <label for="name" class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" class="form-control"
               value="<?= esc($unit['name'] ?? '') ?>" required>
      </div>

      <!-- Symbol -->
      <div class="mb-3">
        <label for="symbol" class="form-label fw-semibold">Symbol</label>
        <input type="text" id="symbol" name="symbol" class="form-control"
               value="<?= esc($unit['symbol'] ?? '') ?>">
      </div>

      <!-- Description (new) -->
      <div class="mb-3">
        <label for="description" class="form-label fw-semibold">Description</label>
        <textarea id="description" name="description" class="form-control" rows="2"
                  placeholder="Optional short description (e.g. Base unit for weight)">
          <?= esc($unit['description'] ?? '') ?>
        </textarea>
      </div>

      <!-- Status (new) -->
      <div class="mb-3">
        <label for="status" class="form-label fw-semibold">Status</label>
        <select id="status" name="status" class="form-select">
          <option value="active" <?= (isset($unit['status']) && $unit['status'] == 'active') ? 'selected' : '' ?>>Active</option>
          <option value="inactive" <?= (isset($unit['status']) && $unit['status'] == 'inactive') ? 'selected' : '' ?>>Inactive</option>
        </select>
      </div>

      <!-- Buttons -->
      <button type="submit" class="btn btn-success">
        <i class="bx bx-save me-1"></i> Save
      </button>
      <a href="<?= base_url('/units/list') ?>" class="btn btn-secondary">
        <i class="bx bx-arrow-back me-1"></i> Cancel
      </a>
    </form>
  </div>
</div>
<?= $this->include('layout/footer') ?>
