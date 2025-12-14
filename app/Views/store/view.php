<?= $this->include('layout/header') ?>

<div class="page-header mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
  <h3 class="fw-semibold mb-0 text-dark">
    <i class="bx bx-package text-primary me-2"></i> Material Details
  </h3>
  <a href="<?= esc($back_url) ?>" class="btn btn-outline-secondary">
    <i class="bx bx-arrow-back"></i> Back
</a>

</div>

<!-- 🧭 Breadcrumbs -->
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb mb-0 small">
    <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Home</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('/store') ?>">Store Department</a></li>
    <li class="breadcrumb-item active" aria-current="page">Material Details</li>
  </ol>
</nav>

<!-- 🧾 Material Overview -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
  <div class="card-body">
    <div class="row g-4">
      <div class="col-md-4">
        <label class="fw-semibold text-muted">Item Name</label>
        <div class="fs-6 fw-semibold text-dark"><?= esc($item['item_name'] ?? '-') ?></div>
      </div>
      <div class="col-md-4">
        <label class="fw-semibold text-muted">Item Code</label>
        <div><?= esc($item['item_code'] ?? '-') ?></div>
      </div>
      <div class="col-md-4">
        <label class="fw-semibold text-muted">Batch No</label>
        <div><?= esc($item['batch_no'] ?? '-') ?></div>
      </div>

      <div class="col-md-4">
        <label class="fw-semibold text-muted">Expiry Date</label>
        <div><?= esc($item['expiry_date'] ?? '-') ?></div>
      </div>
      <div class="col-md-4">
        <label class="fw-semibold text-muted">Available Qty</label>
        <div class="fw-semibold"><?= number_format($item['qty_available'] ?? 0, 2) . ' ' . esc($item['unit_name'] ?? '') ?></div>
      </div>
      <div class="col-md-4">
        <label class="fw-semibold text-muted">Storage Location</label>
        <div><?= esc($item['location_name'] ?? '-') ?></div>
      </div>
    </div>
  </div>
</div>

<!-- 🧪 QC + Store Status -->
<div class="row">
  <div class="col-md-6">
    <div class="card shadow-sm border-0 rounded-3 mb-3">
      <div class="card-body">
        <h6 class="fw-semibold text-dark mb-3"><i class="bx bxs-flask text-primary me-2"></i>QC Details</h6>

        <div class="d-flex align-items-center justify-content-between mb-2">
          <span>QC Status</span>
          <span class="badge bg-<?= ($item['qc_status'] ?? 'Pending') === 'Accepted' ? 'success' : (($item['qc_status'] ?? 'Pending') === 'Rejected' ? 'danger' : 'secondary') ?>">
            <?= esc($item['qc_status'] ?? 'Pending') ?>
          </span>
        </div>

        <div class="small text-muted">
          <strong>QC Remarks:</strong><br>
          <?= esc($item['qc_remarks'] ?? '-') ?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card shadow-sm border-0 rounded-3 mb-3">
      <div class="card-body">
        <h6 class="fw-semibold text-dark mb-3"><i class="bx bxs-archive text-primary me-2"></i>Store Details</h6>

        <div class="d-flex align-items-center justify-content-between mb-2">
          <span>Store Status</span>
          <span class="badge bg-<?= ($item['store_status'] ?? 'Pending') === 'Accepted' ? 'success' : (($item['store_status'] ?? 'Pending') === 'Rejected' ? 'danger' : 'secondary') ?>">
            <?= esc($item['store_status'] ?? 'Pending') ?>
          </span>
        </div>

        <div class="small text-muted">
          <strong>Recorded On:</strong> <?= date('d M Y, h:i A', strtotime($item['created_at'] ?? 'now')) ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 📝 Optional Notes Section -->
<div class="card shadow-sm border-0 rounded-3 mt-3">
  <div class="card-body">
    <h6 class="fw-semibold text-dark mb-2"><i class="bx bx-info-circle text-primary me-2"></i>Additional Info</h6>
    <p class="text-muted small mb-0">
      This material record was automatically created after QC approval. Once verified by Store, it will appear in Inventory Dashboard.
    </p>
  </div>
</div>

<!-- 💾 Print / Export Buttons -->
<div class="text-end mt-4">
  <button class="btn btn-outline-secondary me-2" onclick="window.print()">
    <i class="bx bx-printer"></i> Print
  </button>
  <a href="<?= esc($back_url) ?>" class="btn btn-primary">
    <i class="bx bx-left-arrow-alt"></i> Back to List
  </a>
</div>

<style>
  label { font-size: 0.85rem; }
  .card { transition: 0.3s ease; }
  .card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.07); }
  .badge { font-size: 0.8rem; padding: 0.4em 0.6em; }
</style>

<?= $this->include('layout/footer') ?>
