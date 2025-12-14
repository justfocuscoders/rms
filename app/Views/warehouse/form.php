<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="page-header mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
  <h3 class="fw-semibold mb-0 text-dark">
    <i class="bx bx-package text-success me-2"></i>
    <?= isset($stock) ? 'Edit Stock' : 'Add New Stock' ?>
  </h3>
  <a href="<?= base_url('warehouse') ?>" class="btn btn-sm btn-outline-secondary">
    <i class="bx bx-arrow-back"></i> Back
  </a>
</div>

<form action="<?= base_url('warehouse/save') ?>" method="post" class="card shadow-sm p-4">
    <?= csrf_field() ?>" method="post" class="card shadow-sm p-4">
  <input type="hidden" name="id" value="<?= $stock['id'] ?? '' ?>">

  <div class="row">
    <!-- Item -->
    <div class="col-md-6 mb-3">
      <label class="form-label">Item <span class="text-danger">*</span></label>
      <select name="item_id" class="form-select" required>
        <option value="">Select Item</option>
        <?php foreach ($items as $item): ?>
          <option value="<?= $item['id'] ?>" <?= isset($stock) && $stock['item_id'] == $item['id'] ? 'selected' : '' ?>>
            <?= esc($item['name']) ?> (<?= esc($item['unit_name']) ?>)

          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Batch No -->
    <div class="col-md-6 mb-3">
      <label class="form-label">Batch No</label>
      <input type="text" name="batch_no" value="<?= $stock['batch_no'] ?? '' ?>" class="form-control" placeholder="Enter batch number">
    </div>

    <!-- Expiry Date -->
    <div class="col-md-6 mb-3">
      <label class="form-label">Expiry Date</label>
      <input type="date" name="expiry_date" value="<?= $stock['expiry_date'] ?? '' ?>" class="form-control">
    </div>

    <!-- Quantity -->
    <div class="col-md-6 mb-3">
      <label class="form-label">Quantity Available <span class="text-danger">*</span></label>
      <input type="number" step="0.01" name="qty_available" value="<?= $stock['qty_available'] ?? '' ?>" class="form-control" required>
    </div>

    <!-- Location -->
    <div class="col-md-6 mb-3">
      <label class="form-label">Storage Location <span class="text-danger">*</span></label>
      <select name="location_id" class="form-select" required>
        <option value="">Select Location</option>
        <?php foreach ($locations as $loc): ?>
          <option value="<?= $loc['id'] ?>" <?= isset($stock) && $stock['location_id'] == $loc['id'] ? 'selected' : '' ?>>
            <?= esc($loc['code']) ?> — <?= esc($loc['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="mt-3">
    <button type="submit" class="btn btn-success">
      <i class="bx bx-save"></i> <?= isset($stock) ? 'Update Stock' : 'Add Stock' ?>
    </button>
    <a href="<?= base_url('warehouse') ?>" class="btn btn-secondary">Cancel</a>
  </div>
</form>
</div>
<?= $this->include('layout/footer') ?>

<style>
form.card {
  max-width: 800px;
  margin: auto;
}
</style>

<?= $this->include('layout/footer') ?>
