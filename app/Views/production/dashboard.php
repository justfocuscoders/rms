<?= $this->include('layout/header') ?>
<div class="container-fluid pt-3 pb-5">
<div class="content p-4">
  <div class="page-header border-bottom pb-2 mb-3 d-flex justify-content-between align-items-center">
    <h3 class="fw-semibold mb-0 text-dark">
      <i class="bx bxs-factory text-primary me-2"></i> Production Dashboard
    </h3>
    <small class="text-muted">Monitor your MRS and production status</small>
  </div>

  <!-- Tabs -->
  <ul class="nav nav-tabs mb-4" id="prodTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">📊 Overview</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">🕒 Pending MRS</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">📄 All MRS</button>
    </li>
  </ul>

  <div class="tab-content" id="prodTabsContent">

    <!-- Overview Tab -->
    <div class="tab-pane fade show active" id="overview" role="tabpanel">
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="card text-center shadow-sm border-0 bg-light">
            <div class="card-body">
              <h6>Total MRS</h6>
              <h3 class="fw-bold"><?= esc($total_mrs ?? 0) ?></h3>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-center shadow-sm border-0 bg-warning-subtle">
            <div class="card-body">
              <h6>Pending</h6>
              <h3 class="fw-bold text-warning"><?= esc($pending_mrs ?? 0) ?></h3>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-center shadow-sm border-0 bg-success-subtle">
            <div class="card-body">
              <h6>Approved</h6>
              <h3 class="fw-bold text-success"><?= esc($approved_mrs ?? 0) ?></h3>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-center shadow-sm border-0 bg-info-subtle">
            <div class="card-body">
              <h6>Completed</h6>
              <h3 class="fw-bold text-info"><?= esc($completed_mrs ?? 0) ?></h3>
            </div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-light fw-semibold">
          Recent Material Requisition Slips
        </div>
        <div class="card-body p-0">
          <table class="table table-hover table-striped mb-0">
            <thead class="table-light">
              <tr>
                <th>MRS No</th>
                <th>Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($recent_mrs)): ?>
                <?php foreach ($recent_mrs as $mrs): ?>
                  <tr>
                    <td><?= esc($mrs['mrs_no']) ?></td>
                    <td><?= date('d M Y', strtotime($mrs['mrs_date'])) ?></td>
                    <td>
                      <?php
                        $status = strtolower($mrs['status']);
                        $badge = match($status) {
                          'pending' => 'bg-warning text-dark',
                          'approved' => 'bg-success',
                          'completed' => 'bg-info',
                          default => 'bg-secondary'
                        };
                      ?>
                      <span class="badge <?= $badge ?>"><?= esc($mrs['status']) ?></span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="3" class="text-center text-muted py-3">No recent records found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Pending Tab -->
    <div class="tab-pane fade" id="pending" role="tabpanel">
      <div class="card shadow-sm">
        <div class="card-header bg-light fw-semibold">
          Pending MRS List
        </div>
        <div class="card-body p-0">
          <table class="table table-bordered table-striped mb-0">
            <thead class="table-light">
              <tr>
                <th>MRS No</th>
                <th>Date</th>
                <th>Batch</th>
                <th>Requested By</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($pending_mrs_list)): ?>
                <?php foreach ($pending_mrs_list as $mrs): ?>
                  <tr>
                    <td><?= esc($mrs['mrs_no']) ?></td>
                    <td><?= date('d M Y', strtotime($mrs['mrs_date'])) ?></td>
                    <td><?= esc($mrs['batch_name'] ?? '-') ?></td>
                    <td><?= esc($mrs['requested_by'] ?? '-') ?></td>
                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="5" class="text-center text-muted py-3">No pending MRS found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- All MRS Tab -->
    <div class="tab-pane fade" id="all" role="tabpanel">
      <div class="card shadow-sm">
        <div class="card-header bg-light fw-semibold">
          All Material Requisition Slips
        </div>
        <div class="card-body p-0">
          <table class="table table-bordered table-striped mb-0">
            <thead class="table-light">
              <tr>
                <th>MRS No</th>
                <th>Date</th>
                <th>Batch</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($all_mrs)): ?>
                <?php foreach ($all_mrs as $mrs): ?>
                  <tr>
                    <td><?= esc($mrs['mrs_no']) ?></td>
                    <td><?= date('d M Y', strtotime($mrs['mrs_date'])) ?></td>
                    <td><?= esc($mrs['batch_name'] ?? '-') ?></td>
                    <td>
                      <?php
                        $status = strtolower($mrs['status']);
                        $badge = match($status) {
                          'pending' => 'bg-warning text-dark',
                          'approved' => 'bg-success',
                          'completed' => 'bg-info',
                          default => 'bg-secondary'
                        };
                      ?>
                      <span class="badge <?= $badge ?>"><?= esc($mrs['status']) ?></span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="4" class="text-center text-muted py-3">No MRS found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div> <!-- tab content -->
</div>
</div>
<?= $this->include('layout/footer') ?>
