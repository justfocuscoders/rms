<!-- ✅ Supplier Form Fields Partial -->
<div class="mb-3">
  <label class="form-label fw-semibold">Supplier Name</label>
  <input type="text" name="name" value="<?= esc($supplier['name'] ?? '') ?>" class="form-control" required>
</div>

<div class="mb-3">
  <label class="form-label fw-semibold">Contact Person</label>
  <input type="text" name="contact_person" value="<?= esc($supplier['contact_person'] ?? '') ?>" class="form-control">
</div>

<div class="mb-3">
  <label class="form-label fw-semibold">Phone</label>
  <input type="text" name="phone" value="<?= esc($supplier['phone'] ?? '') ?>" class="form-control">
</div>

<div class="mb-3">
  <label class="form-label fw-semibold">Email</label>
  <input type="email" name="email" value="<?= esc($supplier['email'] ?? '') ?>" class="form-control">
</div>

<div class="mb-3">
  <label class="form-label fw-semibold">Address</label>
  <textarea name="address" class="form-control"><?= esc($supplier['address'] ?? '') ?></textarea>
</div>

<div class="mb-3">
  <label class="form-label fw-semibold">GST / Tax Number</label>
  <input type="text" name="gst_number" value="<?= esc($supplier['gst_number'] ?? '') ?>" class="form-control">
</div>

<div class="mb-3">
  <label class="form-label fw-semibold">Remarks</label>
  <textarea name="remarks" class="form-control"><?= esc($supplier['remarks'] ?? '') ?></textarea>
</div>

<div class="mb-3">
  <label class="form-label fw-semibold">Status</label>
  <select name="status" class="form-select">
    <option value="1" <?= isset($supplier['status']) && $supplier['status'] == 1 ? 'selected' : '' ?>>Active</option>
    <option value="0" <?= isset($supplier['status']) && $supplier['status'] == 0 ? 'selected' : '' ?>>Inactive</option>
  </select>
</div>
