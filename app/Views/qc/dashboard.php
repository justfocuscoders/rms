<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="fw-semibold mb-0 text-dark">
    <i class="bx bxs-flask text-primary me-2"></i> QC Dashboard / Summary
  </h3>
  <a href="<?= base_url('/qc') ?>" class="btn btn-sm btn-outline-secondary">
    <i class="bx bx-left-arrow-alt"></i> Back to List
  </a>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card text-center shadow-sm border-0 bg-info text-white">
      <div class="card-body">
        <h6 class="fw-semibold mb-1">Total QC Tests</h6>
        <h3><?= $total ?></h3>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card text-center shadow-sm border-0 bg-success text-white">
      <div class="card-body">
        <h6 class="fw-semibold mb-1">Accepted</h6>
        <h3><?= $accepted ?></h3>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card text-center shadow-sm border-0 bg-danger text-white">
      <div class="card-body">
        <h6 class="fw-semibold mb-1">Rejected</h6>
        <h3><?= $rejected ?></h3>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card text-center shadow-sm border-0 bg-warning text-dark">
      <div class="card-body">
        <h6 class="fw-semibold mb-1">Pending</h6>
        <h3><?= $pending ?></h3>
      </div>
    </div>
  </div>
</div>

<!-- Charts -->
<div class="card shadow-sm mb-4">
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <h5 class="mb-0 text-primary"><i class="bx bx-pie-chart-alt me-2"></i> QC Status Overview</h5>
  </div>
  <div class="card-body">
    <canvas id="qcChart" height="100"></canvas>
  </div>
</div>

<!-- Recent QC Results -->
<div class="card shadow-sm">
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <h5 class="mb-0 text-primary"><i class="bx bx-history me-2"></i> Recent QC Results</h5>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>GRN</th>
            <th>Item</th>
            <th>Qty Received</th>
            <th>Status</th>
            <th>Tested By</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($recent)): ?>
            <?php foreach ($recent as $r): ?>
              <tr>
                <td><?= $r['id'] ?></td>
                <td>GRN-<?= $r['grn_id'] ?></td>
                <td><?= esc($r['item_name'] ?? 'Unknown Item') ?></td>
                <td><?= esc($r['qty_received']) ?></td>
                <td>
                  <span class="badge 
                    <?= $r['qc_status'] == 'Accepted' ? 'bg-success' : 
                        ($r['qc_status'] == 'Rejected' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                    <?= esc($r['qc_status']) ?>
                  </span>
                </td>
                <td><?= esc($r['tested_by']) ?></td>
                <td><?= date('d M Y', strtotime($r['tested_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="7" class="text-center text-muted">No QC records found</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('qcChart');
new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ['Accepted', 'Rejected', 'Pending'],
    datasets: [{
      data: [<?= $accepted ?>, <?= $rejected ?>, <?= $pending ?>],
      backgroundColor: ['#28a745', '#dc3545', '#ffc107']
    }]
  },
  options: {
    plugins: {
      legend: { position: 'bottom' }
    }
  }
});
</script>

<?= $this->include('layout/footer') ?>