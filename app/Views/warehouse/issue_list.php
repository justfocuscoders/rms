<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="page-header mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
  <h3 class="fw-semibold mb-0 text-dark">
    <i class="bx bx-package text-primary me-2"></i> Pending Material Requests
  </h3>
</div>

<div class="card shadow-sm p-3">
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php elseif (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th width="5%">#</th>
          <th>MRS No</th>
          <th>Date</th>
          <th>Total Items</th>
          <th>Issued</th>
          <th>Status</th>
          <th width="10%">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($mrs_list)): ?>
          <?php foreach ($mrs_list as $index => $mrs): ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td><?= esc($mrs['mrs_no']) ?></td>
              <td><?= date('d-M-Y', strtotime($mrs['mrs_date'])) ?></td>
              <td><?= esc($mrs['total_items']) ?></td>
              <td><?= esc($mrs['issued_items']) ?></td>
              <td>
                <span class="badge bg-<?= $mrs['status'] == 'Issued' ? 'success' : ($mrs['status'] == 'Partial' ? 'warning' : 'secondary') ?>">
                  <?= esc($mrs['status']) ?>
                </span>
              </td>
              <td>
                <a href="<?= base_url('warehouse/issue/' . $mrs['id']) ?>" class="btn btn-sm btn-outline-primary">
                  <i class="bx bx-right-arrow-alt"></i> Issue
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-center text-muted">No pending requests found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<?= $this->include('layout/footer') ?>
