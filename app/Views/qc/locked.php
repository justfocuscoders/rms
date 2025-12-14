<?= $this->include('layout/header') ?>

<div class="container mt-5">
  <div class="alert alert-warning shadow-sm">
    <h5 class="mb-2">
      <i class="bx bx-lock"></i> QC In Progress
    </h5>

    <p class="mb-1">
      <strong>GRN:</strong> <?= esc($grn_info['grn_no']) ?>
    </p>

    <p class="mb-1">
      <strong>Currently handled by:</strong> <?= esc($qc_user) ?>
    </p>

    <p class="mb-3">
      <strong>Since:</strong> <?= date('d M Y, h:i A', strtotime($since)) ?>
    </p>

    <a href="<?= base_url('/qc') ?>" class="btn btn-secondary">
      <i class="bx bx-arrow-back"></i> Back to QC List
    </a>
  </div>
</div>

<?= $this->include('layout/footer') ?>
