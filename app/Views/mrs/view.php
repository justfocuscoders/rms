<?= $this->include('layout/header') ?>
<div class="container-fluid pt-3 pb-5">
<!-- 🔹 Page Header -->
<div class="page-header mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
  <h3 class="fw-semibold mb-0 text-dark">
    <i class="bx bx-file text-primary me-2"></i> MRS Details
  </h3>
  <a href="<?= base_url('/mrs/list') ?>" class="btn btn-outline-secondary">
    <i class="bx bx-arrow-back"></i> Back
  </a>
</div>

<!-- 🔹 Breadcrumbs -->
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb mb-0 small">
    <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('/mrs/list') ?>">MRS List</a></li>
    <li class="breadcrumb-item active" aria-current="page">View MRS</li>
  </ol>
</nav>

<!-- 🔹 MRS Header Info -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
  <div class="card-body row g-4">
    <div class="col-md-3">
      <label class="fw-semibold text-muted">MRS No</label>
      <div class="fw-semibold text-dark"><?= esc($mrs['mrs_no']) ?></div>
    </div>

    <div class="col-md-3">
      <label class="fw-semibold text-muted">Department</label>
      <div><?= esc($mrs['department']) ?></div>
    </div>

    <div class="col-md-3">
      <label class="fw-semibold text-muted">Requested By</label>
      <div><?= esc($mrs['requested_by_name']) ?></div>
    </div>

    <div class="col-md-3">
      <label class="fw-semibold text-muted">Date</label>
      <div><?= date('d M Y', strtotime($mrs['mrs_date'])) ?></div>
    </div>

    <div class="col-md-12">
      <label class="fw-semibold text-muted">Remarks / Purpose</label>
      <div class="form-control-plaintext"><?= esc($mrs['remarks'] ?? '-') ?></div>
    </div>

    <div class="col-md-3">
      <label class="fw-semibold text-muted">Status</label><br>
      <?php
        $badgeClass = match($mrs['status']) {
          'Issued' => 'bg-success',
          'Approved' => 'bg-info',
          'Submitted' => 'bg-warning text-dark',
          'Rejected' => 'bg-danger',
          default => 'bg-secondary'
        };
      ?>
      <span class="badge <?= $badgeClass ?>"><?= esc($mrs['status']) ?></span>
    </div>
  </div>
</div>

<!-- 🔹 MRS Item Details -->
<div class="card shadow-sm border-0 rounded-3">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <h6 class="fw-semibold mb-0"><i class="bx bx-package text-primary me-2"></i> Material Details</h6>

    <?php if ($mrs['status'] !== 'Issued'): ?>
      <form action="<?= base_url('mrs/issue/'.$mrs['id']) ?>" method="post" class="d-inline">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-success btn-sm">
          <i class="bx bx-send"></i> Issue Materials
        </button>
    <?php endif; ?>
  </div>

  <div class="card-body table-responsive">
    <table class="table table-bordered align-middle text-center">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Item</th>
          <th>Batch No</th>
          <th>Qty Requested</th>
          <th>UOM</th>
          <th>Qty Issued</th>
          <?php if ($mrs['status'] === 'Issued'): ?>
            <th>Issued By</th>
            <th>Issued At</th>
          <?php endif; ?>
          <th>Remarks (Issue/Approval)</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($mrs['details'])): ?>
          <tr><td colspan="9" class="text-center text-muted">No items found.</td></tr>
        <?php else: ?>
          <?php 
            $role = strtolower(session()->get('role'));
            $canEditRemark = in_array($role, ['store', 'admin']) && $mrs['status'] !== 'Issued';
          ?>
          <?php foreach ($mrs['details'] as $index => $d): ?>
            <?php $issued = $d['qty_issued'] ?? 0; ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td><?= esc($d['item_name']) ?></td>
              <td><?= esc($d['batch_no'] ?? '-') ?></td>
              <td><?= esc($d['qty_requested']) ?></td>
              <td><?= esc($d['uom']) ?></td>

              <td>
                <?php if ($mrs['status'] === 'Issued'): ?>
                  <?= esc($issued) ?>
                <?php else: ?>
                  <input 
                    type="number" 
                    step="0.01" 
                    name="qty_issued[<?= $d['id'] ?>]" 
                    value="<?= $issued ?>" 
                    min="0"
                    max="<?= $d['qty_requested'] ?>" 
                    class="form-control form-control-sm text-center qty-issue-input" 
                    data-max="<?= $d['qty_requested'] ?>"
                    required
                  >
                  <small class="text-muted">Max: <?= $d['qty_requested'] ?></small>
                <?php endif; ?>
              </td>

              <?php if ($mrs['status'] === 'Issued'): ?>
                <td><?= esc($d['remarked_by'] ?? '-') ?></td>
                <td>
                  <?= !empty($d['remarked_at']) 
                        ? date('d M Y, h:i A', strtotime($d['remarked_at'])) 
                        : '-' ?>
                </td>
              <?php endif; ?>

              <td>
                <?php if ($canEditRemark): ?>
                  <input 
                    type="text"
                    name="item_remarks[<?= $d['id'] ?>]" 
                    value="<?= esc($d['remarks']) ?>" 
                    placeholder="Add remark (optional)"
                    class="form-control form-control-sm"
                  >
                <?php else: ?>
                  <?= esc($d['remarks'] ?? '-') ?>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($mrs['status'] !== 'Issued'): ?>
    </form>
  <?php endif; ?>
</div>
</div>
<!-- 🔹 Validation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.qty-issue-input').forEach(input => {
    input.addEventListener('input', () => {
      const max = parseFloat(input.dataset.max);
      const val = parseFloat(input.value);
      if (val > max) input.classList.add('is-invalid');
      else input.classList.remove('is-invalid');
    });
    input.addEventListener('change', () => {
      const max = parseFloat(input.dataset.max);
      const val = parseFloat(input.value);
      if (val > max) {
        alert(`You cannot issue more than ${max}`);
        input.value = max;
        input.classList.remove('is-invalid');
      }
    });
  });
});
</script>

<style>
.badge { font-size: 0.8rem; padding: 0.45em 0.7em; }
.table th, .table td { vertical-align: middle; font-size: 0.93rem; }
.card { border-radius: 10px; }
.card-header { border-bottom: 1px solid #e5e7eb; }
.is-invalid { border: 1px solid #dc3545 !important; background-color: #ffeaea; }
</style>

<?= $this->include('layout/footer') ?>
