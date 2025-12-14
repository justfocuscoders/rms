<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<?php
$totalItems   = count($grn_items ?? []);
$accepted     = count(array_filter($grn_items ?? [], fn($i) => $i['qc_status'] === 'Accepted'));
$rejected     = count(array_filter($grn_items ?? [], fn($i) => $i['qc_status'] === 'Rejected'));
$pending      = count(array_filter($grn_items ?? [], fn($i) => $i['qc_status'] === 'Pending'));
$tested       = $accepted + $rejected;

$completionPercent = $totalItems > 0 ? round(($tested / $totalItems) * 100) : 0;
$acceptedPercent   = $totalItems > 0 ? round(($accepted / $totalItems) * 100) : 0;
$rejectedPercent   = $totalItems > 0 ? round(($rejected / $totalItems) * 100) : 0;
$pendingPercent    = $totalItems > 0 ? round(($pending / $totalItems) * 100) : 0;

$statusColor = $completionPercent === 100 ? 'bg-success' : ($completionPercent > 0 ? 'bg-warning text-dark' : 'bg-secondary');
?>

<div class="page-header mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
  <div>
    <h3 class="fw-semibold mb-0 text-dark">
      <i class="bx bx-show text-primary me-2"></i> GRN QC Summary
      <span class="badge <?= $statusColor ?> ms-2 px-3 py-2" id="completion-badge"><?= $completionPercent ?>% Completed</span>
    </h3>
    <small class="text-muted">
      GRN No: <?= esc($grn_info['grn_no']) ?> |
      Supplier: <?= esc($grn_info['supplier_name']) ?> |
      Date: <?= date('d M Y', strtotime($grn_info['created_at'])) ?>
    </small>
  </div>
  <a href="<?= base_url('/qc') ?>" class="btn btn-sm btn-outline-secondary">
    <i class="bx bx-left-arrow-alt"></i> Back
  </a>
</div>

<!-- 🌈 QC Progress Section -->
<div class="qc-progress-card shadow-sm p-4 mb-4 rounded-3 border bg-white">
  <h6 class="fw-semibold mb-3">
    <i class="bx bx-line-chart text-primary"></i> QC Progress Overview
  </h6>

  <!-- 🔵 Overall Completion -->
  <div class="progress mb-3" style="height: 20px;">
    <div id="overall-progress" class="progress-bar bg-gradient"
         style="width: <?= $completionPercent ?>%; background: linear-gradient(90deg, #00b4d8, #0077b6);">
    </div>
  </div>
  <div class="text-muted small mb-2">Overall Testing Completion</div>

  <!-- 🟩 Breakdown Bar -->
  <div class="progress" style="height: 12px;">
    <div id="bar-accepted" class="progress-bar bg-success" style="width: <?= $acceptedPercent ?>%;"></div>
    <div id="bar-rejected" class="progress-bar bg-danger" style="width: <?= $rejectedPercent ?>%;"></div>
    <div id="bar-pending" class="progress-bar bg-secondary" style="width: <?= $pendingPercent ?>%;"></div>
  </div>

  <!-- 📊 Summary Row (Clickable Filters) -->
  <div class="d-flex justify-content-between mt-3 small fw-semibold text-muted text-center flex-wrap">
    <span class="filter-btn text-dark" data-status="all"><i class="bx bx-package"></i> Total: <span id="count-total"><?= $totalItems ?></span></span>
    <span class="filter-btn text-success" data-status="Accepted"><i class="bx bx-check-circle"></i> Accepted: <span id="count-accepted"><?= $accepted ?></span></span>
    <span class="filter-btn text-danger" data-status="Rejected"><i class="bx bx-x-circle"></i> Rejected: <span id="count-rejected"><?= $rejected ?></span></span>
    <span class="filter-btn text-secondary" data-status="Pending"><i class="bx bx-time"></i> Pending: <span id="count-pending"><?= $pending ?></span></span>
  </div>
</div>

<!-- 🧾 QC Items Table -->
<div class="card shadow-sm border-0 rounded-3">
  <div class="card-header bg-white">
    <h6 class="fw-semibold mb-0"><i class="bx bx-list-ul text-primary me-2"></i> Item-wise QC Results</h6>
  </div>
  <div class="card-body table-responsive">
    <table class="table table-bordered align-middle table-hover" id="qcTable">
      <thead class="table-light">
        <tr>
          <th>Item</th>
          <th>Batch No</th>
          <th>Expiry</th>
          <th>Qty</th>
          <th>Status</th>
          <th>Remarks</th>
          <th>Tested By</th>
          <th>Tested At</th>
        </tr>
      </thead>
      <tbody id="qcTableBody">
        <?php foreach ($grn_items as $row): ?>
          <tr data-status="<?= esc($row['qc_status']) ?>">
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
            <td>
  <?= esc($row['tested_by_name'] ?? '?') ?>
  <?php if (!empty($row['tester_role'])): ?>
    <small class="text-muted">(<?= esc($row['tester_role']) ?>)</small>
  <?php endif; ?>
</td>
            <td><?= !empty($row['tested_at']) ? date('d M Y h:i A', strtotime($row['tested_at'])) : '-' ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<!-- 🌟 Styles -->
<style>
.progress { border-radius: 10px; overflow: hidden; }
.progress-bar { transition: width 1s ease-in-out; }
.filter-btn { cursor: pointer; transition: 0.2s; }
.filter-btn:hover { text-decoration: underline; transform: scale(1.05); }
.filter-btn.active { font-weight: bold; text-decoration: underline; }
.qc-progress-card { transition: 0.3s ease; }
.qc-progress-card:hover { box-shadow: 0 6px 16px rgba(0,0,0,0.1); transform: translateY(-3px); }
</style>

<!-- ⚡ JS: Filter + Auto-refresh -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const buttons = document.querySelectorAll('.filter-btn');
  const rows = document.querySelectorAll('#qcTable tbody tr');

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      const status = btn.getAttribute('data-status');
      buttons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        row.style.display =
          status === 'all' || status === rowStatus ? '' : 'none';
      });
    });
  });

  // 🔄 Auto-refresh QC data every 10 seconds
  setInterval(() => {
    fetch("<?= base_url('/qc/refreshData/' . $grn_info['grn_no']) ?>")
      .then(response => response.json())
      .then(data => updateDashboard(data))
      .catch(err => console.error("QC Refresh Error:", err));
  }, 10000);
});

function updateDashboard(data) {
  document.getElementById('count-total').textContent = data.total;
  document.getElementById('count-accepted').textContent = data.accepted;
  document.getElementById('count-rejected').textContent = data.rejected;
  document.getElementById('count-pending').textContent = data.pending;

  document.getElementById('overall-progress').style.width = data.completion + '%';
  document.getElementById('bar-accepted').style.width = data.acceptedPercent + '%';
  document.getElementById('bar-rejected').style.width = data.rejectedPercent + '%';
  document.getElementById('bar-pending').style.width = data.pendingPercent + '%';
  document.getElementById('completion-badge').textContent = data.completion + '% Completed';

  const tbody = document.getElementById('qcTableBody');
  tbody.innerHTML = data.html; // server sends refreshed table rows
}
</script>

<?= $this->include('layout/footer') ?>
