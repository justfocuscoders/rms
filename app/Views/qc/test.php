<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
  <div class="page-header mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
    <h3 class="fw-semibold mb-0 text-dark">
      <i class="bx bxs-flask text-primary me-2"></i> QC Testing – <?= esc($grn_info['grn_no']) ?>
    </h3>
    <a href="<?= base_url('/qc') ?>" class="btn btn-outline-secondary btn-sm">
      <i class="bx bx-left-arrow-alt"></i> Back
    </a>
  </div>

  <!-- 🧾 Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0 small">
      <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= base_url('/qc') ?>">Quality Control</a></li>
      <li class="breadcrumb-item active" aria-current="page">Testing (<?= esc($grn_info['grn_no']) ?>)</li>
    </ol>
  </nav>

  <!-- 🌈 QC Summary -->
  <div class="qc-summary mb-4 p-3 border rounded-3 bg-white shadow-sm">
    <div class="d-flex align-items-center justify-content-between">
      <h5 class="mb-0 fw-semibold text-dark">
        <i class="bx bx-line-chart text-primary"></i> QC Progress
      </h5>
      <span id="completionBadge" class="badge bg-secondary px-3 py-2">0% Completed</span>
    </div>

    <div class="progress mt-2" style="height: 16px;">
      <div id="progressBar" class="progress-bar bg-primary" style="width: 0%; transition: width 0.8s ease;"></div>
    </div>

    <div class="mt-2 small text-muted d-flex justify-content-between flex-wrap">
      <span>Total: <strong id="countTotal"><?= count($grn_items) ?></strong></span>
      <span class="text-success">Accepted: <strong id="countAccepted">0</strong></span>
      <span class="text-danger">Rejected: <strong id="countRejected">0</strong></span>
      <span class="text-secondary">Pending: <strong id="countPending"><?= count($grn_items) ?></strong></span>
    </div>
  </div>

  <!-- 🧪 QC Form -->
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-body">
      <form id="qcForm">
        <?= csrf_field() ?>
        <input type="hidden" name="grn_id" value="<?= esc($grn_info['id']) ?>">

        <table class="table table-bordered align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Item</th>
              <th>Batch No</th>
              <th>Expiry</th>
              <th>Qty</th>
              <th>Status</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($grn_items as $i => $item): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= esc($item['item_name']) ?></td>
                <td><?= esc($item['batch_no']) ?></td>
                <td><?= esc($item['expiry_date']) ?></td>
                <td><?= esc($item['qty_received']) ?></td>
                <td>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio"
                      name="qc[<?= $item['grn_detail_id'] ?>][status]" value="Accepted"
                      <?= ($item['qc_status'] == 'Accepted') ? 'checked' : '' ?>>
                    <label class="form-check-label text-success">✔ Accept</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio"
                      name="qc[<?= $item['grn_detail_id'] ?>][status]" value="Rejected"
                      <?= ($item['qc_status'] == 'Rejected') ? 'checked' : '' ?>>
                    <label class="form-check-label text-danger">✖ Reject</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio"
                      name="qc[<?= $item['grn_detail_id'] ?>][status]" value="Pending"
                      <?= ($item['qc_status'] == 'Pending') ? 'checked' : '' ?>>
                    <label class="form-check-label text-secondary">Pending</label>
                  </div>
                </td>
                <td>
                  <input type="text" class="form-control form-control-sm"
                    name="qc[<?= $item['grn_detail_id'] ?>][remarks]"
                    value="<?= esc($item['remarks']) ?>"
                    placeholder="Enter remarks...">
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="text-end mt-3">
          <button type="submit" class="btn btn-success px-4" id="saveBtn">
            <i class="bx bx-save"></i> Save Results
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
<!-- 🌟 Styles -->
<style>
  .form-check-input:checked[value="Accepted"] {
    accent-color: #28a745;
  }

  .form-check-input:checked[value="Rejected"] {
    accent-color: #dc3545;
  }

  .form-check-input:checked[value="Pending"] {
    accent-color: #6c757d;
  }

  .table td,
  .table th {
    vertical-align: middle;
  }

  .toast {
    z-index: 1200;
  }
</style>

<!-- ⚡ JS: AJAX Save + Live Update -->
<script>
  let isSubmitting = false; // ✅ prevent double submit

  document.getElementById('qcForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    if (isSubmitting) return; // ✅ block double click
    isSubmitting = true;

    const formData = new FormData(this);
    const btn = document.getElementById('saveBtn');

    btn.disabled = true;
    btn.innerHTML = '<i class="bx bx-loader bx-spin"></i> Saving...';

    try {
      const res = await fetch('<?= base_url('/qc/updateQcAjax') ?>', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const data = await res.json();

      if (data.success) {
        showToast('Success', 'QC results saved successfully!');

        // ✅ Update progress once
        updateLiveProgress(data.summary, data.completion);

        // ✅ Confirm + Redirect to main QC page
        setTimeout(() => {
          if (confirm('QC saved successfully. Go back to QC list?')) {
            window.location.href = "<?= base_url('/qc') ?>";
          }
        }, 800);

      } else {
        showToast('Error', data.message || 'Failed to save QC data', 'danger');
      }

    } catch (err) {
      console.error(err);
      showToast('Error', 'Something went wrong while saving', 'danger');
    } finally {
      isSubmitting = false;
      btn.disabled = false;
      btn.innerHTML = '<i class="bx bx-save"></i> Save Results';
    }
  });


  function updateLiveProgress(summary, completion) {
    if (!summary) return;

    document.getElementById('countTotal').textContent = summary.total;
    document.getElementById('countAccepted').textContent = summary.accepted;
    document.getElementById('countRejected').textContent = summary.rejected;
    document.getElementById('countPending').textContent = summary.pending;

    const progressBar = document.getElementById('progressBar');
    const badge = document.getElementById('completionBadge');

    progressBar.style.width = completion + '%';
    badge.textContent = completion + '% Completed';

    badge.classList.remove('bg-secondary', 'bg-warning', 'bg-success');
    if (completion === 100) badge.classList.add('bg-success');
    else if (completion >= 50) badge.classList.add('bg-warning');
    else badge.classList.add('bg-secondary');
  }

  function showToast(title, message, type = 'success') {
    const bgClass = type === 'danger' ? 'bg-danger' : 'bg-success';
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white ${bgClass} border-0 position-fixed top-0 end-0 m-3`;
    toast.innerHTML = `
    <div class="d-flex">
      <div class="toast-body"><strong>${title}</strong><br>${message}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>`;
    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, {
      delay: 2500
    });
    bsToast.show();
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
  }
</script>


<?= $this->include('layout/footer') ?>