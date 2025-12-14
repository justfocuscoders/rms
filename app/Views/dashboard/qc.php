<?= $this->include('layout/header') ?>

<div class="container-fluid pt-1 pb-5">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
    <div>
      <h3 class="fw-semibold mb-0 text-dark">
        <i class="bx bxs-vial text-primary me-2"></i> QC Dashboard / Summary
      </h3>
      <small class="text-muted">Quality control status overview and recent inspections</small>
    </div>
    <span class="text-muted small"><?= date('d M Y, h:i A') ?></span>
  </div>

  <!-- Info Summary -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card border-0 shadow-sm rounded-4 text-center">
        <div class="card-body py-3">
          <div class="text-primary mb-2"><i class="bx bx-test-tube fs-2"></i></div>
          <h6 class="text-muted mb-1">Total QC Tests</h6>
          <h3 class="fw-bold text-primary"><?= esc($total_qc_tests ?? ($pending_samples + $approved_batches + $rejected_batches)) ?></h3>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card border-0 shadow-sm rounded-4 text-center">
        <div class="card-body py-3">
          <div class="text-success mb-2"><i class="bx bx-check-circle fs-2"></i></div>
          <h6 class="text-muted mb-1">Accepted</h6>
          <h3 class="fw-bold text-success"><?= esc($approved_batches ?? 0) ?></h3>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card border-0 shadow-sm rounded-4 text-center">
        <div class="card-body py-3">
          <div class="text-danger mb-2"><i class="bx bx-x-circle fs-2"></i></div>
          <h6 class="text-muted mb-1">Rejected</h6>
          <h3 class="fw-bold text-danger"><?= esc($rejected_batches ?? 0) ?></h3>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card border-0 shadow-sm rounded-4 text-center">
        <div class="card-body py-3">
          <div class="text-warning mb-2"><i class="bx bx-time-five fs-2"></i></div>
          <h6 class="text-muted mb-1">Pending</h6>
          <h3 class="fw-bold text-warning"><?= esc($pending_samples ?? 0) ?></h3>
        </div>
      </div>
    </div>
  </div>

  <!-- QC Status Overview -->
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white fw-semibold border-0 pb-0">
      <i class="bx bx-pie-chart text-primary me-2"></i> QC Status Overview
    </div>
    <div class="card-body">
      <canvas id="qcStatusChart" height="120"></canvas>
    </div>
  </div>

  <!-- Recent QC Table -->
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white fw-semibold border-0 pb-0 d-flex justify-content-between align-items-center">
      <span><i class="bx bx-list-check text-primary me-2"></i> Recent QC Results</span>
      <a href="<?= base_url('qc') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
        <i class="bx bx-show"></i> View All
      </a>
    </div>
    <div class="card-body p-0 mt-2">
      <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
          <thead class="table-light">
            <tr class="text-center">
              <th>#</th>
              <th>GRN No</th>
              <th>Item Name</th>
              <th>Status</th>
              <th>Remarks</th>
              <th>Date Tested</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($recent_qc)): $i=1; foreach($recent_qc as $qc): ?>
              <tr>
                <td class="text-center"><?= $i++ ?></td>
                <td><?= esc($qc['grn_no'] ?? '-') ?></td>
                <td><?= esc($qc['item_name'] ?? '-') ?></td>
                <td class="text-center">
                  <?php
                    $status = strtolower($qc['qc_status'] ?? '');
                    $badge = match($status) {
                      'accepted' => 'bg-success',
                      'rejected' => 'bg-danger',
                      'pending'  => 'bg-warning text-dark',
                      default    => 'bg-secondary',
                    };
                  ?>
                  <span class="badge <?= $badge ?> px-3"><?= ucfirst($qc['qc_status'] ?? '-') ?></span>
                </td>
                <td><?= esc($qc['remarks'] ?? '-') ?></td>
                <td class="text-center"><?= date('d M Y', strtotime($qc['created_at'])) ?></td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="6" class="text-center text-muted py-4"><i class="bx bx-info-circle me-1"></i> No QC results found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- ✅ Chart.js for QC Overview -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const ctx = document.getElementById('qcStatusChart').getContext('2d');
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Accepted', 'Rejected', 'Pending'],
      datasets: [{
        data: [<?= esc($approved_batches ?? 0) ?>, <?= esc($rejected_batches ?? 0) ?>, <?= esc($pending_samples ?? 0) ?>],
        backgroundColor: ['#198754', '#dc3545', '#ffc107'],
        borderWidth: 1
      }]
    },
    options: {
      plugins: { legend: { position: 'bottom' } },
      cutout: '70%'
    }
  });
});
</script>

<style>
  body { background: #fff !important; }
  .card { background: #fff; border-radius: 1rem; }
  .page-header h3 { font-size: 1.4rem; }
  .table th, .table td { vertical-align: middle; }
  .table thead th { font-weight: 600; font-size: 0.9rem; }
</style>

<?= $this->include('layout/footer') ?>
