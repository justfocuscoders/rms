<?= $this->include('layout/header') ?>

<div class="container-fluid py-3">
  <h3 class="fw-semibold mb-4">
    <?= ucfirst(session()->get('role_name') ?? 'Procurement') ?> Dashboard
  </h3>

  <?php $role = session()->get('role'); ?>

  <!-- 🔹 Show top metrics only for procurement and admin -->
  <?php if (in_array($role, ['admin', 'procurement'])): ?>
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <h6 class="text-muted">Total POs</h6>
          <h3 class="fw-bold"><?= esc($total_pos ?? 0) ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <h6 class="text-muted">Pending POs</h6>
          <h3 class="fw-bold text-warning"><?= esc($pending_pos ?? 0) ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <h6 class="text-muted">Approved POs</h6>
          <h3 class="fw-bold text-success"><?= esc($approved_pos ?? 0) ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <h6 class="text-muted">Suppliers</h6>
          <h3 class="fw-bold"><?= esc($total_suppliers ?? 0) ?></h3>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- 🔹 Dashboard Content per Role -->
  <div class="row g-3">
    <?php if (in_array($role, ['admin', 'procurement'])): ?>
      <!-- Procurement Dashboard Section -->
      <div class="col-md-6">
        <div class="card shadow-sm border-0">
          <div class="card-header fw-semibold">Recent Purchase Orders</div>
          <div class="card-body p-0">
            <table class="table table-striped mb-0">
              <thead>
                <tr><th>PO Number</th><th>Status</th><th>Date</th></tr>
              </thead>
              <tbody>
                <?php if (!empty($recent_pos)): foreach($recent_pos as $po): ?>
                <tr>
                  <td><?= esc($po['po_number']) ?></td>
                  <td>
                    <span class="badge 
                      <?= $po['status'] == 'Approved' ? 'bg-success' : 
                          ($po['status'] == 'Pending' ? 'bg-warning text-dark' : 'bg-secondary') ?>">
                      <?= esc($po['status']) ?>
                    </span>
                  </td>
                  <td><?= esc(date('d M Y', strtotime($po['created_at']))) ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="3" class="text-muted text-center">No records found</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card shadow-sm border-0">
          <div class="card-header fw-semibold">Recent GRNs</div>
          <div class="card-body p-0">
            <table class="table table-striped mb-0">
              <thead><tr><th>GRN No</th><th>PO</th><th>Date</th></tr></thead>
              <tbody>
                <?php if (!empty($recent_grn)): foreach($recent_grn as $g): ?>
                <tr>
                  <td><?= esc($g['grn_number'] ?? '-') ?></td>
                  <td><?= esc($g['po_number'] ?? '-') ?></td>
                  <td><?= esc(date('d M Y', strtotime($g['created_at'] ?? '-'))) ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="3" class="text-muted text-center">No GRNs found</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    <?php elseif ($role === 'qc'): ?>
      <!-- Quality Control Dashboard -->
      <div class="col-md-12">
        <div class="card shadow-sm border-0">
          <div class="card-header fw-semibold">Pending QC Inspections</div>
          <div class="card-body text-center py-4 text-muted">
            Coming soon: QC inspection data overview.
          </div>
        </div>
      </div>

    <?php elseif ($role === 'warehouse'): ?>
      <!-- Warehouse Dashboard -->
      <div class="col-md-12">
        <div class="card shadow-sm border-0">
          <div class="card-header fw-semibold">Warehouse Overview</div>
          <div class="card-body text-center py-4 text-muted">
            Coming soon: Inventory status and GRN tracking.
          </div>
        </div>
      </div>

    <?php elseif ($role === 'production'): ?>
      <!-- Production Dashboard -->
      <div class="col-md-12">
        <div class="card shadow-sm border-0">
          <div class="card-header fw-semibold">Production Status</div>
          <div class="card-body text-center py-4 text-muted">
            Coming soon: Production schedules and material requests.
          </div>
        </div>
      </div>

    <?php else: ?>
      <!-- Default fallback -->
      <div class="col-md-12">
        <div class="alert alert-info">Welcome to RMS Dashboard. Please contact admin for role-based access.</div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?= $this->include('layout/footer') ?>
