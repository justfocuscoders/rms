<?= $this->include('layout/header') ?>

<div class="container-fluid py-3">
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white">
      <h5 class="mb-0"><?= esc($title) ?></h5>
    </div>

    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4"><strong>ARN No:</strong> <?= esc($arn['arn_no']) ?></div>
        <div class="col-md-4"><strong>PO No:</strong> <?= esc($arn['po_no']) ?></div>
        <div class="col-md-4"><strong>Item:</strong> <?= esc($arn['item_name']) ?></div>

        <div class="col-md-4"><strong>Supplier:</strong> <?= esc($arn['supplier_name']) ?></div>
        <div class="col-md-4"><strong>Batch No:</strong> <?= esc($arn['batch_no']) ?></div>
        <div class="col-md-4"><strong>Received Qty:</strong> <?= esc($arn['received_qty']) . ' ' . esc($arn['uom']) ?></div>

        <div class="col-md-4"><strong>Received Date:</strong> <?= esc($arn['received_date']) ?></div>
        <div class="col-md-4"><strong>Expiry Date:</strong> <?= esc($arn['expiry_date']) ?: '-' ?></div>
        <div class="col-md-4">
          <strong>Status:</strong>
          <span class="badge bg-<?= $arn['status']=='Pending'?'warning':($arn['status']=='Converted'?'success':'danger') ?>">
            <?= esc($arn['status']) ?>
          </span>
        </div>
      </div>

      <div class="mt-4 text-end">
        <?php if (in_array(session()->get('role'), ['admin', 'warehouse'])): ?>
          <a href="<?= site_url('arn/edit/'.$arn['id']) ?>" class="btn btn-primary">
            <i class="bx bx-edit"></i> Edit
          </a>
        <?php endif; ?>
        <a href="<?= site_url('arn') ?>" class="btn btn-secondary">Back</a>
      </div>
    </div>
  </div>
</div>

<?= $this->include('layout/footer') ?>
