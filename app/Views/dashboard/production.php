<?= $this->include('layout/header') ?>

<div class="container-fluid py-3">
  <h3 class="fw-semibold mb-4">Production Dashboard</h3>

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <h6 class="text-muted">Open MRS</h6>
          <h3 class="fw-bold text-warning"><?= esc($my_open_mrs ?? 0) ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <h6 class="text-muted">Issued MRS</h6>
          <h3 class="fw-bold text-success"><?= esc($issued_mrs ?? 0) ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <h6 class="text-muted">Partially Issued</h6>
          <h3 class="fw-bold text-info"><?= esc($partial_mrs ?? 0) ?></h3>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-header fw-semibold">Recent MRS</div>
    <div class="card-body p-0">
      <table class="table table-striped mb-0">
        <thead><tr><th>MRS No</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
          <?php if (!empty($recent_mrs)): foreach($recent_mrs as $mrs): ?>
          <tr>
            <td><?= esc($mrs['mrs_no'] ?? '-') ?></td>
            <td><span class="badge bg-info"><?= esc($mrs['status']) ?></span></td>
            <td><?= esc($mrs['created_at']) ?></td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="3" class="text-muted text-center">No MRS found</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?= $this->include('layout/footer') ?>
