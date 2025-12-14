<!-- ✅ Unit Form Fields Partial -->
<div class="mb-3">
  <label for="name" class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
  <input type="text" id="name" name="name" class="form-control"
         value="<?= esc($unit['name'] ?? '') ?>" required>
</div>

<div class="mb-3">
  <label for="symbol" class="form-label fw-semibold">Symbol</label>
  <input type="text" id="symbol" name="symbol" class="form-control"
         value="<?= esc($unit['symbol'] ?? '') ?>">
</div>

<div class="mb-3">
  <label for="description" class="form-label fw-semibold">Description</label>
  <textarea id="description" name="description" class="form-control" rows="2"
            placeholder="Optional short description (e.g. Base unit for weight)">
    <?= esc($unit['description'] ?? '') ?>
  </textarea>
</div>

<div class="mb-3">
  <label for="status" class="form-label fw-semibold">Status</label>
  <select id="status" name="status" class="form-select">
    <option value="active" <?= (isset($unit['status']) && $unit['status'] == 'active') ? 'selected' : '' ?>>Active</option>
    <option value="inactive" <?= (isset($unit['status']) && $unit['status'] == 'inactive') ? 'selected' : '' ?>>Inactive</option>
  </select>
</div>
