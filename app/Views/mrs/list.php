<?= $this->include('layout/header') ?>
<div class="container-fluid pt-3 pb-5">
<!-- 🔹 Page Header -->
<div class="page-header d-flex justify-content-between align-items-center mb-4">
  <div>
    <h3 class="fw-semibold mb-0 text-dark">
      <i class="bx bx-package text-primary me-2"></i> Material Requisition Slips (MRS)
    </h3>
    <small class="text-muted">Track and manage material requests</small>
  </div>

  <?php $role = strtolower(session()->get('role') ?? ''); ?>
  <!-- ✅ Show "New MRS" button only for Admin or Production -->
  <?php if (in_array($role, ['admin', 'production'])): ?>
    <a href="<?= base_url('/mrs/form') ?>" class="btn btn-primary shadow-sm">
      <i class="bx bx-plus me-1"></i> New MRS
    </a>
  <?php endif; ?>
</div>


<!-- 🔹 MRS List Table -->
<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
  <div class="card-body table-responsive">

    <table class="table table-bordered align-middle mb-0 table-hover">
      <thead class="table-light text-center">
        <tr>
          <th>#</th>
          <th>MRS No</th>
          <th>Department</th>
          <th>Requested By</th>
          <th>Date</th>
          <th>Status</th>
          <th width="200">Actions</th>
        </tr>
      </thead>

      <tbody class="text-center">
        <?php if (empty($mrs)): ?>
          <tr>
            <td colspan="7" class="text-muted py-4">No MRS records found.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($mrs as $index => $m): ?>
            <?php
              $status = strtolower($m['status']);
              $badgeClass = match($status) {
                'issued'   => 'bg-success',
                'approved' => 'bg-info',
                'submitted' => 'bg-warning text-dark',
                'rejected' => 'bg-danger',
                default    => 'bg-secondary'
              };
            ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td class="fw-semibold text-primary"><?= esc($m['mrs_no']) ?></td>
              <td><?= esc($m['department']) ?></td>
              <td><?= esc($m['requested_by_name']) ?></td>
              <td><?= date('d M Y', strtotime($m['mrs_date'])) ?></td>
              <td><span class="badge <?= $badgeClass ?>"><?= ucfirst($m['status']) ?></span></td>

              <td>
                <div class="btn-group" role="group">
                  <!-- ✅ Everyone with access can view -->
                  <a href="<?= base_url('mrs/view/'.$m['id']) ?>" 
                     class="btn btn-outline-info btn-sm" 
                     title="View">
                    <i class="bx bx-show"></i>
                  </a>

                  <!-- ✅ Edit (Admin/Production) only if still Submitted -->
                  <?php if (in_array($role, ['admin', 'production']) && $m['status'] == 'Submitted'): ?>
                    <a href="<?= base_url('mrs/form/'.$m['id']) ?>" 
                       class="btn btn-outline-primary btn-sm" 
                       title="Edit">
                      <i class="bx bx-edit"></i>
                    </a>
                  <?php endif; ?>

                  <!-- ✅ Approve/Reject (Store/Admin) only if Submitted -->
                  <?php if (in_array($role, ['admin', 'store']) && $m['status'] == 'Submitted'): ?>
                    <a href="<?= base_url('mrs/approve/'.$m['id']) ?>" 
                       class="btn btn-outline-success btn-sm" 
                       title="Approve">
                      <i class="bx bx-check"></i>
                    </a>
                    <a href="<?= base_url('mrs/reject/'.$m['id']) ?>" 
                       class="btn btn-outline-danger btn-sm" 
                       title="Reject">
                      <i class="bx bx-x"></i>
                    </a>
                  <?php endif; ?>

                  <!-- ✅ Issue (Store/Admin) only after Approved -->
                  <?php if (in_array($role, ['admin', 'store']) && $m['status'] == 'Approved'): ?>
                    <a href="<?= base_url('mrs/view/'.$m['id']) ?>#issue" 
                       class="btn btn-outline-warning btn-sm" 
                       title="Issue">
                      <i class="bx bx-package"></i>
                    </a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</dv>
<!-- 🔹 Embedded Styles -->
<style>
.page-header h3 { font-size: 1.5rem; font-weight: 600; color: #1e293b; }
.page-header small { font-size: 0.9rem; color: #6b7280; }
.table th {
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.9rem;
  color: #475569;
  letter-spacing: 0.03em;
}
.table td {
  font-size: 0.92rem;
  color: #1e293b;
  vertical-align: middle;
}
.table-hover tbody tr:hover { background-color: #f8fafc; transition: 0.2s ease; }
.badge {
  border-radius: 6px;
  font-size: 0.85rem;
  padding: 0.4em 0.75em;
  font-weight: 500;
}
.card { border-radius: 10px; }
</style>

<?= $this->include('layout/footer') ?>
