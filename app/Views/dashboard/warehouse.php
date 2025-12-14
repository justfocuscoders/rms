<?= $this->include('layout/header') ?>

<div class="container-fluid pt-1 pb-5">
  <h3 class="fw-semibold mb-4">Warehouse Dashboard</h3>

  <!-- GRN Summary -->
  <div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card text-center shadow-sm border-0"><div class="card-body">
      <h6 class="text-muted">Total GRNs</h6>
      <h3 class="fw-bold"><?= esc($total_grns ?? 0) ?></h3>
    </div></div></div>
    <div class="col-md-3"><div class="card text-center shadow-sm border-0"><div class="card-body">
      <h6 class="text-muted">QC Pending</h6>
      <h3 class="fw-bold text-warning"><?= esc($qc_pending ?? 0) ?></h3>
    </div></div></div>
    <div class="col-md-3"><div class="card text-center shadow-sm border-0"><div class="card-body">
      <h6 class="text-muted">QC Approved</h6>
      <h3 class="fw-bold text-success"><?= esc($qc_approved ?? 0) ?></h3>
    </div></div></div>
    <div class="col-md-3"><div class="card text-center shadow-sm border-0"><div class="card-body">
      <h6 class="text-muted">QC Rejected</h6>
      <h3 class="fw-bold text-danger"><?= esc($qc_rejected ?? 0) ?></h3>
    </div></div></div>
  </div>

  <!-- Store / Inventory -->
  <div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card text-center shadow-sm border-0"><div class="card-body">
      <h6 class="text-muted">Total Stock Items</h6>
      <h3 class="fw-bold"><?= esc($total_items ?? 0) ?></h3>
    </div></div></div>
    <div class="col-md-3"><div class="card text-center shadow-sm border-0"><div class="card-body">
      <h6 class="text-muted">Total Qty in Store</h6>
      <h3 class="fw-bold"><?= esc($stock_total ?? 0) ?></h3>
    </div></div></div>
    <div class="col-md-3"><div class="card text-center shadow-sm border-0"><div class="card-body">
      <h6 class="text-muted">Low Stock Alerts</h6>
      <h3 class="fw-bold text-warning"><?= esc($low_stock ?? 0) ?></h3>
    </div></div></div>
    <div class="col-md-3"><div class="card text-center shadow-sm border-0"><div class="card-body">
      <h6 class="text-muted">Open MRS</h6>
      <h3 class="fw-bold text-primary"><?= esc($open_mrs ?? 0) ?></h3>
    </div></div></div>
  </div>

  <!-- Pending Verification Items -->
  <div class="card shadow-sm border-0">
    <div class="card-header fw-semibold bg-light">
      QC-Approved Items Awaiting Store Verification
    </div>
    <div class="card-body p-0">
      <table class="table table-sm table-bordered mb-0">
        <thead class="bg-light">
          <tr class="text-center">
            <th>#</th>
            <th>GRN No</th>
            <th>Item</th>
            <th>Supplier</th>
            <th>Approved Qty</th>
            <th>QC Remarks</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($pending_verifications)): ?>
            <?php foreach ($pending_verifications as $i => $row): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= esc($row['grn_no']) ?></td>
                <td><?= esc($row['item_name']) ?></td>
                <td><?= esc($row['supplier_name']) ?></td>
                <td><?= esc($row['approved_qty']) ?></td>
                <td><?= esc($row['remarks']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center text-muted">No pending verification items.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?= $this->include('layout/footer') ?>
