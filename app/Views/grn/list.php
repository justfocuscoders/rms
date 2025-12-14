<?= $this->include('layout/header') ?>
<div class="container-fluid pt-3 pb-5">
<!-- 🔹 Page Header -->
<div class="page-header d-flex justify-content-between align-items-center mb-4">
  <div>
    <h3 class="fw-semibold mb-0 text-dark">
      <i class="bx bxs-truck text-primary me-2"></i> Goods Receipt Notes (GRN)
    </h3>
    <small class="text-muted">Manage and monitor all supplier GRNs</small>
  </div>
  <a href="<?= base_url('/grn/form') ?>" class="btn btn-primary shadow-sm">
    <i class="bx bx-plus me-1"></i> Add GRN
  </a>
</div>

<!-- 🔹 GRN Table -->
<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
  <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap">
    <form class="d-flex align-items-center flex-wrap gap-2" method="get" action="">
        <?= csrf_field() ?>
      <input type="text" name="search" class="form-control form-control-sm"
             placeholder="Search GRN No or Supplier..." value="<?= esc($_GET['search'] ?? '') ?>" style="width: 240px;">
      
      <select name="status" class="form-select form-select-sm" style="width: 200px;"
              onchange="this.form.submit()">
        <option value="">All Status</option>
        <option value="pending" <?= (($_GET['status'] ?? '') === 'pending') ? 'selected' : '' ?>>🟡 Pending</option>
        <option value="approved" <?= (($_GET['status'] ?? '') === 'approved') ? 'selected' : '' ?>>🟢 Approved</option>
        <option value="rejected" <?= (($_GET['status'] ?? '') === 'rejected') ? 'selected' : '' ?>>🔴 Rejected</option>
        <option value="quarantine" <?= (($_GET['status'] ?? '') === 'quarantine') ? 'selected' : '' ?>>🟣 Quarantine</option>
      </select>

      <button type="submit" class="btn btn-sm btn-outline-primary">
        <i class="bx bx-search"></i>
      </button>
      <a href="<?= base_url('/grn/list') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bx bx-reset"></i> Reset
      </a>
    </form>
  </div>

  <div class="table-responsive">
    <table class="table align-middle table-hover mb-0">
      <thead class="bg-light text-secondary">
        <tr class="text-center">
          <th>#</th>
          <th>GRN No</th>
          <th>Supplier</th>
          <th>Date</th>
          <th>Status Progress</th>
          <th>Received By</th>
          <th width="130">Actions</th>
        </tr>
      </thead>
      <tbody class="text-center">
        <?php
        $filterStatus = strtolower($_GET['status'] ?? '');
        $filteredGrns = array_filter($grns, fn($grn) => $filterStatus === '' || strtolower($grn['status']) === $filterStatus);
        ?>
        <?php if (empty($filteredGrns)): ?>
          <tr><td colspan="7" class="text-muted py-4">No GRNs found</td></tr>
        <?php else: ?>
          <?php foreach ($filteredGrns as $index => $grn): ?>
            <?php
              $status = strtolower($grn['status']);
              $badgeClass = match($status) {
                'pending' => 'bg-warning text-dark',
                'approved' => 'bg-success',
                'rejected' => 'bg-danger',
                'quarantine' => 'bg-purple text-white',
                default => 'bg-secondary'
              };
              $icon = match($status) {
                'pending' => 'bx bx-time-five',
                'approved' => 'bx bx-check-circle',
                'rejected' => 'bx bx-x-circle',
                'quarantine' => 'bx bx-error-circle',
                default => 'bx bx-info-circle'
              };
            ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td class="fw-semibold text-primary"><?= esc($grn['grn_no']) ?></td>
              <td><?= esc($grn['supplier_name']) ?></td>
              <td><?= date('d M Y', strtotime($grn['grn_date'])) ?></td>

              <td>
                <!-- 🔸 Mini Timeline with Tooltips -->
                <div class="timeline-mini" data-status="<?= $status ?>">
                  <div class="step" data-bs-toggle="tooltip" data-bs-title="Created on <?= date('d M Y', strtotime($grn['grn_date'])) ?>">
                    <i class='bx bx-file'></i>
                  </div>
                  <div class="step" data-bs-toggle="tooltip" data-bs-title="Moved to Quarantine">
                    <i class='bx bx-error-circle'></i>
                  </div>
                  <div class="step" data-bs-toggle="tooltip" data-bs-title="Approved">
                    <i class='bx bx-check-circle'></i>
                  </div>
                  <div class="step" data-bs-toggle="tooltip" data-bs-title="Rejected">
                    <i class='bx bx-x-circle'></i>
                  </div>
                </div>
              </td>

              <td><?= esc($grn['received_by_name'] ?? '-') ?></td>

              <td>
                <div class="btn-group" role="group">
                  <a href="<?= base_url('/grn/view/'.$grn['id']) ?>" class="btn btn-outline-info btn-sm" title="View">
                    <i class="bx bx-show"></i>
                  </a>
                  <a href="<?= base_url('/grn/form/'.$grn['id']) ?>" class="btn btn-outline-primary btn-sm" title="Edit">
                    <i class="bx bx-edit"></i>
                  </a>
                  <a href="<?= base_url('/grn/delete/'.$grn['id']) ?>" class="btn btn-outline-danger btn-sm"
                     onclick="return confirm('Delete this GRN?')" title="Delete">
                    <i class="bx bx-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<!-- ✅ Embedded Styles -->
<style>
.page-header h3 { font-size: 1.5rem; font-weight: 600; color: #1e293b; }
.page-header small { font-size: 0.9rem; color: #6b7280; }

.table th {
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.95rem;
  background: #f8fafc;
  color: #475569;
  letter-spacing: 0.02em;
}
.table td { font-size: 0.95rem; color: #1e293b; vertical-align: middle; }
.table-hover tbody tr:hover { background-color: #f1f5ff; transition: 0.2s ease-in-out; }

.badge { border-radius: 6px; font-size: 0.88rem; padding: 0.4em 0.75em; font-weight: 500; }
.bg-purple { background-color: #6f42c1 !important; }

.card { border-radius: 10px; }
.card-header { border-bottom: 1px solid #e5e7eb; }

/* ✅ Mini Timeline Styles */
.timeline-mini {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 6px;
  position: relative;
  margin-top: 2px;
}
.timeline-mini::before {
  content: "";
  position: absolute;
  height: 3px;
  background: #e5e7eb;
  top: 50%;
  left: 0;
  right: 0;
  z-index: 1;
  transform: translateY(-50%);
  border-radius: 3px;
}
.timeline-mini::after {
  content: "";
  position: absolute;
  height: 3px;
  top: 50%;
  left: 0;
  background: linear-gradient(90deg, #2563eb, #60a5fa);
  z-index: 2;
  transform: translateY(-50%);
  width: 0;
  border-radius: 3px;
  transition: width 1.5s ease;
}
.timeline-mini[data-status="created"]::after { width: 10%; }
.timeline-mini[data-status="pending"]::after { width: 25%; }
.timeline-mini[data-status="quarantine"]::after { width: 50%; background: #6f42c1; }
.timeline-mini[data-status="approved"]::after { width: 75%; background: #22c55e; }
.timeline-mini[data-status="rejected"]::after { width: 100%; background: #dc2626; }

/* Icons */
.timeline-mini .step {
  position: relative;
  z-index: 3;
}
.timeline-mini .step i {
  font-size: 0.95rem;
  background: #e5e7eb;
  color: #6b7280;
  border-radius: 50%;
  padding: 4px;
  width: 26px;
  height: 26px;
  line-height: 18px;
  text-align: center;
  transition: all 0.3s ease;
  cursor: pointer;
}
.timeline-mini[data-status="approved"] .step:nth-child(-n+3) i {
  background: #22c55e; color: #fff;
}
.timeline-mini[data-status="pending"] .step:nth-child(1) i {
  background: #facc15; color: #000;
}
.timeline-mini[data-status="quarantine"] .step:nth-child(-n+2) i {
  background: #6f42c1; color: #fff;
}
.timeline-mini[data-status="rejected"] .step:nth-child(-n+4) i {
  background: #dc2626; color: #fff;
}

/* Tooltip style */
.tooltip .tooltip-inner {
  background: #1e293b;
  color: #fff;
  font-size: 0.75rem;
  padding: 5px 8px;
  border-radius: 6px;
}
</style>

<!-- ✅ Tooltip Init -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  tooltips.forEach(el => new bootstrap.Tooltip(el));
});
</script>

<?= $this->include('layout/footer') ?>
