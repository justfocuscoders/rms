<!-- app/Views/items/_form_fields.php -->
<div class="mb-3">
  <label class="form-label fw-semibold">Item Code</label>
  <input type="text" name="code" class="form-control" placeholder="e.g., ITM-001" required>
</div>

<div class="mb-3">
  <label class="form-label fw-semibold">Item Name</label>
  <input type="text" name="name" class="form-control" placeholder="Enter item name" required>
</div>

<div class="mb-3">
  <label class="form-label fw-semibold">Description</label>
  <textarea name="description" class="form-control" placeholder="Optional description"></textarea>
</div>

<div class="mb-3">
  <label class="form-label fw-semibold">Unit</label>
  <div class="input-group">
    <select name="unit_id" id="itemUnitSelect" class="form-select" required>
      <option value="">Select Unit</option>
      <?php foreach($units as $u): ?>
        <option value="<?= $u['id'] ?>"><?= esc($u['name']) ?> (<?= esc($u['symbol']) ?>)</option>
      <?php endforeach; ?>
    </select>
    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#addUnitModal">
      <i class="bx bx-plus"></i>
    </button>
  </div>
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
  <label class="form-label fw-semibold">Unit Price (₹)</label>
  <input type="number" name="unit_price" class="form-control" step="0.01" placeholder="0.00" required>
</div>

<div class="mb-3">
  <label class="form-label fw-semibold">Reorder Level</label>
  <input type="number" name="reorder_level" class="form-control" placeholder="e.g., 10">
</div>

<div class="mb-3">
  <label class="form-label fw-semibold">HSN Code</label>
  <input type="text" name="hsn_code" class="form-control" placeholder="Optional HSN code">
</div>

<div class="mb-3">
  <label class="form-label fw-semibold">Status</label>
  <select name="is_active" class="form-select">
    <option value="1" selected>Active</option>
    <option value="0">Inactive</option>
  </select>
</div>
