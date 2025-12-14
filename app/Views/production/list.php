<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="page-header mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
  <h3 class="fw-semibold mb-0 text-dark">
    <i class="bx bx-cog text-warning me-2"></i> Production Batches
  </h3>
  <a href="<?= base_url('production/form') ?>" class="btn btn-sm btn-primary">
    <i class="bx bx-plus"></i> Add Batch
  </a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Batch No</th>
          <th>Product</th>
          <th>Planned Qty</th>
          <th>Status</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>MRS (Issued / Total)</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($batches)): $i=1; foreach ($batches as $batch): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= esc($batch['batch_no']) ?></td>
            <td><?= esc($batch['product_name']) ?></td>
            <td><?= esc($batch['planned_qty']) . ' ' . esc($batch['uom']) ?></td>
            <td><span class="badge bg-info"><?= esc($batch['status']) ?></span></td>
            <td><?= esc($batch['start_date']) ?></td>
            <td><?= esc($batch['end_date']) ?></td>
            <td><?= esc($batch['issued_mrs'] ?? 0) ?> / <?= esc($batch['total_mrs'] ?? 0) ?></td>
            <td>
              <a href="<?= base_url('production/view/'.$batch['batch_id']) ?>" class="btn btn-sm btn-outline-primary">
                <i class="bx bx-show"></i>
              </a>
              <a href="<?= base_url('production/form/'.$batch['batch_id']) ?>" class="btn btn-sm btn-outline-secondary">
                <i class="bx bx-edit"></i>
              </a>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="9" class="text-center text-muted">No production batches found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<?= $this->include('layout/footer') ?>
