<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="card shadow-sm border-0 rounded-3">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <h5 class="mb-0">
      <i class="bx bxs-flask text-primary me-2"></i>
      <?= isset($qc['id']) ? 'Edit QC Result' : 'Perform QC Test' ?>
    </h5>
    <a href="<?= base_url('/qc/listGrns') ?>" class="btn btn-outline-secondary btn-sm">
      <i class="bx bx-arrow-back"></i> Back
    </a>
  </div>

  <div class="card-body">
    <form action="<?= esc($action) ?>" method="post">
      <?= csrf_field() ?>

      <!-- 🔹 GRN & Item Details -->
      <div class="row g-3 mb-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">GRN No</label>
          <input type="text" class="form-control" value="<?= esc($grnDetail['grn_no']) ?>" readonly>
        </div>

        <div class="col-md-4">
          <label class="form-label fw-semibold">Item</label>
          <input type="text" class="form-control" value="<?= esc($grnDetail['item_name']) ?>" readonly>
        </div>

        <div class="col-md-4">
          <label class="form-label fw-semibold">Batch No</label>
          <input type="text" class="form-control" value="<?= esc($grnDetail['batch_no'] ?? '-') ?>" readonly>
        </div>
      </div>

      <!-- 🔹 QC Inputs -->
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">QC Status</label>
          <select name="qc_status" class="form-select" required>
            <option value="Pending" <?= isset($qc['qc_status']) && $qc['qc_status']=='Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Approved" <?= isset($qc['qc_status']) && $qc['qc_status']=='Approved' ? 'selected' : '' ?>>Approved</option>
            <option value="Rejected" <?= isset($qc['qc_status']) && $qc['qc_status']=='Rejected' ? 'selected' : '' ?>>Rejected</option>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label fw-semibold">Tested By</label>
          <select name="tested_by" class="form-select" required>
            <option value="">Select User</option>
            <?php foreach($users as $u): ?>
              <option value="<?= $u['id'] ?>" <?= isset($qc['tested_by']) && $qc['tested_by']==$u['id'] ? 'selected' : '' ?>>
                <?= esc($u['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label fw-semibold">Tested At</label>
          <input type="datetime-local" name="tested_at" class="form-control"
            value="<?= isset($qc['tested_at']) ? date('Y-m-d\TH:i', strtotime($qc['tested_at'])) : date('Y-m-d\TH:i') ?>">
        </div>

        <div class="col-12">
          <label class="form-label fw-semibold">Remarks</label>
          <textarea name="remarks" class="form-control" rows="3" placeholder="Enter remarks or QC observations"><?= esc($qc['remarks'] ?? '') ?></textarea>
        </div>
      </div>

      <hr class="my-4">

      <!-- 🔹 Buttons -->
      <div class="text-end">
        <button type="submit" class="btn btn-success px-4">
          <i class="bx bx-save"></i>
          <?= isset($qc['id']) ? 'Update QC' : 'Save QC' ?>
        </button>
      </div>
    </form>
  </div>
</div>
</div>
<!-- 🔹 Inline Styles (matching GRN form design) -->
<style>
  .form-control, .form-select {
    border-radius: 8px;
    height: 42px;
  }
  label.form-label { font-size: 0.9rem; }
  .card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.05); transition: 0.3s ease; }
</style>

<?= $this->include('layout/footer') ?>
