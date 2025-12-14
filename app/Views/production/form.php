<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="page-header mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
  <h3 class="fw-semibold mb-0 text-dark">
    <i class="bx bx-cog text-warning me-2"></i>
    <?= isset($batch) ? 'Edit Production Batch' : 'Add New Production Batch' ?>
  </h3>
  <a href="<?= base_url('production') ?>" class="btn btn-sm btn-outline-secondary">
    <i class="bx bx-arrow-back"></i> Back
  </a>
</div>

<form action="<?= base_url('production/save') ?>" method="post" class="card shadow-sm p-4">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= $batch['id'] ?? '' ?>">

  <!-- ====== Batch Info Section ====== -->
  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label">Batch No <span class="text-danger">*</span></label>
      <input type="text" name="batch_no" class="form-control" value="<?= esc($batch['batch_no'] ?? '') ?>" required>
    </div>

    <div class="col-md-4">
      <label class="form-label">Product Name <span class="text-danger">*</span></label>
      <input type="text" name="product_name" class="form-control" value="<?= esc($batch['product_name'] ?? '') ?>" required>
    </div>

    <div class="col-md-2">
      <label class="form-label">Planned Qty</label>
      <input type="number" name="planned_qty" step="0.01" class="form-control" value="<?= esc($batch['planned_qty'] ?? '') ?>">
    </div>

    <div class="col-md-2">
      <label class="form-label">UOM</label>
      <input type="text" name="uom" class="form-control" value="<?= esc($batch['uom'] ?? '') ?>">
    </div>

    <div class="col-md-3">
      <label class="form-label">Start Date</label>
      <input type="date" name="start_date" class="form-control" value="<?= esc($batch['start_date'] ?? '') ?>">
    </div>

    <div class="col-md-3">
      <label class="form-label">End Date</label>
      <input type="date" name="end_date" class="form-control" value="<?= esc($batch['end_date'] ?? '') ?>">
    </div>

    <div class="col-md-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <?php foreach (['Planned','In Process','Completed','Closed'] as $st): ?>
          <option value="<?= $st ?>" <?= (isset($batch['status']) && $batch['status']==$st) ? 'selected' : '' ?>><?= $st ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-12">
      <label class="form-label">Remarks</label>
      <textarea name="remarks" class="form-control" rows="2"><?= esc($batch['remarks'] ?? '') ?></textarea>
    </div>
  </div>

  <!-- ====== Inline MRS Section ====== -->
  <?php if (isset($batch['id'])): ?>
  <hr class="my-4">
  <h5 class="fw-semibold mb-3"><i class="bx bx-file me-1 text-primary"></i> Material Requisition (MRS)</h5>

  <div class="table-responsive">
    <table class="table table-bordered align-middle" id="mrs-items">
      <thead class="table-light">
        <tr>
          <th>Item Name</th>
          <th>UOM</th>
          <th>Required Qty</th>
          <th>Remarks</th>
          <th width="80">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><input type="text" name="item_name[]" class="form-control" required></td>
          <td><input type="text" name="uom[]" class="form-control" required></td>
          <td><input type="number" step="0.01" name="required_qty[]" class="form-control" required></td>
          <td><input type="text" name="item_remarks[]" class="form-control"></td>
          <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger remove-row"><i class="bx bx-trash"></i></button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="text-end">
    <button type="button" id="add-row" class="btn btn-sm btn-outline-primary">
      <i class="bx bx-plus"></i> Add Item
    </button>
  </div>
  <?php endif; ?>

  <!-- ====== Buttons ====== -->
  <div class="mt-4 text-end">
    <button type="submit" class="btn btn-success">
      <i class="bx bx-save"></i> <?= isset($batch) ? 'Update Batch' : 'Save Batch' ?>
    </button>
    <a href="<?= base_url('production') ?>" class="btn btn-secondary">Cancel</a>
  </div>
</form>
</div>
<style>
  form.card { max-width: 1000px; margin: auto; }
  #mrs-items input { min-width: 120px; }
</style>

<script>
document.getElementById('add-row')?.addEventListener('click', () => {
  const tbody = document.querySelector('#mrs-items tbody');
  const row = document.createElement('tr');
  row.innerHTML = `
    <td><input type="text" name="item_name[]" class="form-control" required></td>
    <td><input type="text" name="uom[]" class="form-control" required></td>
    <td><input type="number" step="0.01" name="required_qty[]" class="form-control" required></td>
    <td><input type="text" name="item_remarks[]" class="form-control"></td>
    <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="bx bx-trash"></i></button></td>
  `;
  tbody.appendChild(row);
});

document.addEventListener('click', (e) => {
  if (e.target.closest('.remove-row')) e.target.closest('tr').remove();
});
</script>

<?= $this->include('layout/footer') ?>
