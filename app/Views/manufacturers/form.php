<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="content p-1">
  <h3><?= isset($manufacturer) ? 'Edit Manufacturer' : 'Add Manufacturer' ?></h3>

  <form method="post" action="<?= isset($manufacturer) ? base_url('/manufacturers/save/'.$manufacturer['id']) : base_url('/manufacturers/save') ?>">
    <?= csrf_field() ?>

    <div class="mb-3">
      <label>Name</label>
      <input type="text" name="name" value="<?= $manufacturer['name'] ?? '' ?>" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Address</label>
      <textarea name="address" class="form-control"><?= $manufacturer['address'] ?? '' ?></textarea>
    </div>

    <div class="mb-3">
      <label>Contact Person</label>
      <input type="text" name="contact_person" value="<?= $manufacturer['contact_person'] ?? '' ?>" class="form-control">
    </div>

    <div class="mb-3">
      <label>Phone</label>
      <input type="text" name="phone" value="<?= $manufacturer['phone'] ?? '' ?>" class="form-control">
    </div>

    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" value="<?= $manufacturer['email'] ?? '' ?>" class="form-control">
    </div>

    <div class="mb-3">
      <label>Status</label>
      <select name="status" class="form-select">
        <option value="Active" <?= (isset($manufacturer['status']) && $manufacturer['status'] == 'Active') ? 'selected' : '' ?>>Active</option>
        <option value="Inactive" <?= (isset($manufacturer['status']) && $manufacturer['status'] == 'Inactive') ? 'selected' : '' ?>>Inactive</option>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Save</button>
    <a href="<?= base_url('/manufacturers/list') ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</div>
<?= $this->include('layout/footer') ?>
