<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="page-header mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
  <h3 class="fw-semibold mb-0 text-dark">
    <i class="bx bx-cog text-warning me-2"></i> Batch Details
  </h3>
  <a href="<?= base_url('production') ?>" class="btn btn-sm btn-outline-secondary">
    <i class="bx bx-arrow-back"></i> Back
  </a>
</div>

<div class="card mb-3 shadow-sm">
  <div class="card-header bg-light fw-semibold">Batch Information</div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6 mb-2"><strong>Batch No:</strong> <?= esc($batch['batch_no']) ?></div>
      <div class="col-md-6 mb-2"><strong>Product Name:</strong> <?= esc($batch['product_name']) ?></div>
      <div class="col-md-4 mb-2"><strong>Planned Qty:</strong> <?= esc($batch['planned_qty']) . ' ' . esc($batch['uom']) ?></div>
      <div class="col-md-4 mb-2"><strong>Status:</strong> <span class="badge bg-info"><?= esc($batch['status']) ?></span></div>
      <div class="col-md-4 mb-2"><strong>Created By:</strong> <?= esc($batch['created_by'] ?? '-') ?></div>
      <div class="col-md-4 mb-2"><strong>Start Date:</strong> <?= esc($batch['start_date']) ?></div>
      <div class="col-md-4 mb-2"><strong>End Date:</strong> <?= esc($batch['end_date']) ?></div>
      <div class="col-md-12 mt-2"><strong>Remarks:</strong> <?= esc($batch['remarks']) ?></div>
    </div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <span class="fw-semibold">Linked Material Requests</span>
    <a href="<?= base_url('mrs/form?batch_id=' . $batch['id']) ?>" class="btn btn-sm btn-primary">
      <i class="bx bx-plus"></i> Create MRS
    </a>
  </div>
  <div class="card-body">
    <table class="table table-bordered table-sm align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>MRS No</th>
          <th>Date</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($mrs_list)): $i=1; foreach ($mrs_list as $mrs): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= esc($mrs['mrs_no']) ?></td>
            <td><?= esc($mrs['mrs_date']) ?></td>
            <td><span class="badge bg-info"><?= esc($mrs['status']) ?></span></td>
            <td>
              <a href="<?= base_url('mrs/view/'.$mrs['id']) ?>" class="btn btn-outline-primary btn-sm">
                <i class="bx bx-show"></i>
              </a>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="5" class="text-center text-muted">No MRS linked yet</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<?= $this->include('layout/footer') ?>
