<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="page-header mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
  <div>
    <h3 class="fw-semibold mb-0 text-dark">
      <i class="bx bx-show text-primary me-2"></i> GRN Item QC Details
    </h3>
    <small class="text-muted">GRN No: <?= esc($grn_info['grn_no']) ?> | Supplier: <?= esc($grn_info['supplier_name']) ?> | Date: <?= date('d M Y', strtotime($grn_info['created_at'])) ?></small>
  </div>
  <a href="<?= base_url('/qc') ?>" class="btn btn-sm btn-outline-secondary">
    <i class="bx bx-left-arrow-alt"></i> Back
  </a>
</div>

<div class="card shadow-sm">
  <div class="card-body table-responsive">
    <table class="table table-bordered align-middle table-hover">
      <thead class="table-light">
        <tr>
          <th>Item</th>
          <th>Batch No</th>
          <th>Expiry</th>
          <th>Qty Received</th>
          <th>Status</th>
          <th>Remarks</th>
          <th>Tested By</th>
          <th>Tested At</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($grn_items)): ?>
          <?php foreach ($grn_items as $row): ?>
            <tr>
              <td><?= esc($row['item_name']) ?></td>
              <td><?= esc($row['batch_no'] ?? '-') ?></td>
              <td><?= !empty($row['expiry_date']) ? date('d M Y', strtotime($row['expiry_date'])) : '-' ?></td>
              <td><?= esc($row['qty_received']) ?></td>
              <td>
                <?php if ($row['qc_status'] == 'Accepted'): ?>
                  <span class="badge bg-success">Accepted</span>
                <?php elseif ($row['qc_status'] == 'Rejected'): ?>
                  <span class="badge bg-danger">Rejected</span>
                <?php elseif ($row['qc_status'] == 'Pending'): ?>
                  <span class="badge bg-warning text-dark">Pending</span>
                <?php else: ?>
                  <span class="badge bg-secondary">Not Tested</span>
                <?php endif; ?>
              </td>
              <td><?= esc($row['remarks'] ?? '-') ?></td>
              <td><?= esc($row['tested_by'] ?? '-') ?></td>
              <td><?= !empty($row['tested_at']) ? date('d M Y h:i A', strtotime($row['tested_at'])) : '-' ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" class="text-center text-muted">No QC records for this GRN.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<?= $this->include('layout/footer') ?>
