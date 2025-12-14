<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="page-header mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
  <h3 class="fw-semibold mb-0 text-dark">
    <i class="bx bx-export text-success me-2"></i>
    Issue Materials for MRS #<?= esc($mrs['mrs_no']) ?>
  </h3>
  <a href="<?= base_url('warehouseissue') ?>" class="btn btn-sm btn-outline-secondary">
    <i class="bx bx-arrow-back"></i> Back
  </a>
</div>

<form action="<?= base_url('warehouseissue/saveIssue') ?>" method="post" class="card shadow-sm p-4">
    <?= csrf_field() ?>" method="post" class="card shadow-sm p-4">
  <input type="hidden" name="mrs_id" value="<?= esc($mrs['id']) ?>">

  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th>Item</th>
          <th>Batch No</th>
          <th>Expiry</th>
          <th>Available</th>
          <th>Requested</th>
          <th>Issue Qty</th>
          <th>Remarks</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($mrs_items as $row): ?>
          <tr>
            <td>
              <?= esc($row['item_name']) ?>
              <input type="hidden" name="item_id[]" value="<?= esc($row['item_id']) ?>">
              <input type="hidden" name="stock_id[]" value="<?= esc($row['stock_id']) ?>">
            </td>
            <td><?= esc($row['batch_no'] ?? '—') ?></td>
            <td><?= $row['expiry_date'] ? date('d-M-Y', strtotime($row['expiry_date'])) : '—' ?></td>
            <td><?= esc($row['qty_available'] ?? 0) ?></td>
            <td><?= esc($row['qty_requested']) ?></td>
            <td>
              <input type="number" step="0.01" name="qty_issued[]" class="form-control form-control-sm" 
                     max="<?= esc($row['qty_available'] ?? 0) ?>">
            </td>
            <td>
              <input type="text" name="remarks[]" class="form-control form-control-sm" placeholder="Optional">
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="text-end mt-3">
    <button type="submit" class="btn btn-success">
      <i class="bx bx-check"></i> Confirm Issue
    </button>
    <a href="<?= base_url('warehouseissue') ?>" class="btn btn-secondary">Cancel</a>
  </div>
</form>
</div>
<?= $this->include('layout/footer') ?>
