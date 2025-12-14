<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="content p-1">
  <h3><?= isset($supplier) ? 'Edit Supplier' : 'Add Supplier' ?></h3>

  <form method="post" action="<?= isset($supplier) ? base_url('/suppliers/save/'.$supplier['id']) : base_url('/suppliers/save') ?>">
    <?= csrf_field() ?>
    
    <div class="mb-3">
      <label>Name</label>
      <input type="text" name="name" value="<?= $supplier['name'] ?? '' ?>" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Contact Person</label>
      <input type="text" name="contact_person" value="<?= $supplier['contact_person'] ?? '' ?>" class="form-control">
    </div>

    <div class="mb-3">
      <label>Phone</label>
      <input type="text" name="phone" value="<?= $supplier['phone'] ?? '' ?>" class="form-control">
    </div>

    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" value="<?= $supplier['email'] ?? '' ?>" class="form-control">
    </div>

    <div class="mb-3">
      <label>Address</label>
      <textarea name="address" class="form-control"><?= $supplier['address'] ?? '' ?></textarea>
    </div>

    <!-- 🆕 Optional Fields -->
    <div class="mb-3">
      <label>GST / Tax Number</label>
      <input type="text" name="gst_number" value="<?= $supplier['gst_number'] ?? '' ?>" class="form-control">
    </div>

    <div class="mb-3">
      <label>Remarks</label>
      <textarea name="remarks" class="form-control"><?= $supplier['remarks'] ?? '' ?></textarea>
    </div>

    <div class="mb-3">
      <label>Status</label>
      <select name="status" class="form-select">
        <option value="1" <?= isset($supplier['status']) && $supplier['status'] == 1 ? 'selected' : '' ?>>Active</option>
        <option value="0" <?= isset($supplier['status']) && $supplier['status'] == 0 ? 'selected' : '' ?>>Inactive</option>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Save</button>
    <a href="<?= base_url('/suppliers/list') ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</div>
<?= $this->include('layout/footer') ?>
