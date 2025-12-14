<?= $this->include('layout/header') ?>
<div class="container-fluid pt-3 pb-5">
<!-- 🔹 Page Header -->
<div class="page-header d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
  <div>
    <h3 class="fw-semibold mb-0 text-dark">
      <i class="bx bxs-truck text-primary me-2"></i> Goods Receipt Note Details
    </h3>
    <small class="text-muted">Detailed record of supplier receipt and quality process</small>
  </div>
  <div class="d-flex align-items-center gap-2 no-print">
    <button id="btnPrint" class="btn btn-outline-primary btn-sm shadow-sm">
      <i class="bx bx-printer me-1"></i> Print
    </button>
    <button id="btnDownloadPDF" class="btn btn-outline-success btn-sm shadow-sm">
      <i class="bx bx-download me-1"></i> Download PDF
    </button>
    <a href="<?= base_url('/grn/list') ?>" class="btn btn-outline-secondary btn-sm shadow-sm">
      <i class="bx bx-arrow-back me-1"></i> Back
    </a>
  </div>
</div>

<!-- 🔹 GRN Info + Status -->
<div id="grnContent" class="grn-content">
  <div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body">
      <div class="row g-4 align-items-center">
        <div class="col-md-4 border-end">
          <h6 class="fw-semibold text-secondary mb-2">
            <i class="bx bx-file me-1 text-primary"></i> GRN Information
          </h6>
          <p class="mb-1"><strong>GRN No:</strong> <span class="text-primary"><?= esc($grn['grn_no']) ?></span></p>
          <p class="mb-1"><strong>ARN No:</strong> <?= esc($grn['arn_no']) ?></p>
          <p class="mb-0"><strong>Date:</strong> <?= date('d M Y', strtotime($grn['grn_date'])) ?></p>
        </div>

        <div class="col-md-4 border-end">
          <h6 class="fw-semibold text-secondary mb-2">
            <i class="bx bx-user me-1 text-primary"></i> Supplier Details
          </h6>
          <p class="mb-1"><strong>Supplier:</strong> <?= esc($grn['supplier_name']) ?></p>
          <p class="mb-0"><strong>Received By:</strong> 
  <span class="text-primary fw-semibold"><?= esc($grn['received_by_name'] ?? '-') ?></span>
</p>
        </div>

        <div class="col-md-4">
  <h6 class="fw-semibold text-secondary mb-2">
    <i class="bx bx-check-shield me-1 text-primary"></i> Current Status
  </h6>

  <?php
    $status = strtolower($grn['status']);
    $statusLabels = [
        'pending' => ['class' => 'bg-warning text-dark', 'icon' => 'bx bx-time-five', 'label' => 'Pending'],
        'approved' => ['class' => 'bg-success text-white', 'icon' => 'bx bx-check-circle', 'label' => 'Approved'],
        'rejected' => ['class' => 'bg-danger text-white', 'icon' => 'bx bx-x-circle', 'label' => 'Rejected'],
        'quarantine' => ['class' => 'bg-purple text-white', 'icon' => 'bx bx-error-circle', 'label' => 'Quarantine']
    ];
    $s = $statusLabels[$status] ?? ['class' => 'bg-secondary text-white', 'icon' => 'bx bx-info-circle', 'label' => ucfirst($status)];
  ?>

  <!-- 🔹 Status Badge -->
  <span class="badge <?= $s['class'] ?> px-3 py-2 fs-6 mb-3 d-inline-flex align-items-center shadow-sm">
    <i class="<?= $s['icon'] ?> me-1 fs-5"></i> <?= $s['label'] ?>
  </span>

  <!-- 🔹 Horizontal Status Tracker -->
  <div class="status-timeline mt-3" id="statusTimeline">
    <?php
      $steps = [
        [
          'label' => 'Created',
          'icon' => 'bx bx-file',
          'active' => !empty($grn['created_at']),
          'date' => $grn['created_at'] ?? null,
          'user' => $grn['received_by_name'] ?? '-'
        ],
        [
          'label' => 'QC Verified',
          'icon' => 'bx bx-test-tube',
          'active' => !empty($grn['verified_at']),
          'date' => $grn['verified_at'] ?? null,
          'user' => $grn['verified_by_name'] ?? '-'
        ],
        [
          'label' => 'Approved',
          'icon' => 'bx bx-check-circle',
          'active' => !empty($grn['approved_at']),
          'date' => $grn['approved_at'] ?? null,
          'user' => $grn['approved_by_name'] ?? '-'
        ],
        [
          'label' => 'Rejected',
          'icon' => 'bx bx-x-circle',
          'active' => $status === 'rejected',
          'date' => $grn['updated_at'] ?? null,
          'user' => $status === 'rejected' ? ($grn['approved_by_name'] ?? '-') : null
        ],
      ];
    ?>

    <div class="timeline-line"></div>

    <?php foreach ($steps as $step): ?>
  <?php
    // Only show tooltip if step is active and has date/user info
    $hasTooltip = $step['active'] && !empty($step['date']);
    $tooltipAttrs = $hasTooltip
      ? 'data-bs-toggle="tooltip" data-bs-html="true" title="<b>'
        . $step['label'] . '</b><br>'
        . date('d M Y, h:i A', strtotime($step['date']))
        . '<br>' . esc($step['user']) . '"'
      : '';
  ?>
  
  <div class="timeline-step <?= $step['active'] ? 'active' : '' ?>" <?= $tooltipAttrs ?>>
    <i class="<?= $step['icon'] ?>"></i>
    <span><?= $step['label'] ?></span>
  </div>
<?php endforeach; ?>

  </div>
</div>

      </div>
    </div>
  </div>

  <!-- 🔹 Items Table -->
  <div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3 px-4">
      <h6 class="mb-0 fw-semibold text-primary">
        <i class="bx bx-list-ul me-2"></i> Items Received
      </h6>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-center text-secondary">
  <tr>
    <th>Item</th>
    <th>Batch No</th>
    <th>MFG Date</th>
    <th>Expiry Date</th> 
    <th>Quantity</th>
  </tr>
</thead>

          <tbody class="text-center">
            <?php if (empty($items)): ?>
              <tr><td colspan="5" class="text-muted py-3">No items found for this GRN</td></tr>
            <?php else: ?>
              <?php foreach ($items as $i): ?>
                <tr>
                  <td class="text-start">
                    <strong><?= esc($i['item_name']) ?></strong><br>
                    <small class="text-muted"><?= esc($i['item_code']) ?></small>
                  </td>
                  <td><?= esc($i['batch_no']) ?: '<span class="text-muted">—</span>' ?></td>
                  <td><?= $i['mfg_date'] ? date('d M Y', strtotime($i['mfg_date'])) : '<span class="text-muted">—</span>' ?></td>
                  <td><?= $i['expiry_date'] ? date('d M Y', strtotime($i['expiry_date'])) : '<span class="text-muted">—</span>' ?></td>
<td><span class="fw-semibold"><?= esc($i['qty_received']) ?></span></td>
                  
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- 🔹 Signature Footer -->
  <!-- 🔹 Signature Footer -->
<div class="d-flex justify-content-between mt-5 px-3 signature-section">

  <!-- 🟦 Received By -->
  <div class="text-center">
    <div class="fw-semibold mb-1 signature-name received">
      <i class="bx bx-user-circle me-1"></i>
      <?= esc($grn['received_by_name'] ?? '-') ?>
    </div>
    <hr class="signature-line">
    <small class="text-muted d-block">Received By</small>
    <?php if (!empty($grn['created_at'])): ?>
      <small class="text-secondary fst-italic">
        <?= date('d M Y, h:i A', strtotime($grn['created_at'])) ?>
      </small>
    <?php endif; ?>
  </div>

  <!-- 🟣 Verified By -->
  <div class="text-center">
    <div class="fw-semibold mb-1 signature-name verified">
      <i class="bx bx-test-tube me-1"></i>
      <?= esc($grn['verified_by_name'] ?? '-') ?>
    </div>
    <hr class="signature-line">
    <small class="text-muted d-block">Verified By</small>
    <?php if (!empty($grn['verified_at'])): ?>
      <small class="text-secondary fst-italic">
        <?= date('d M Y, h:i A', strtotime($grn['verified_at'])) ?>
      </small>
    <?php endif; ?>
  </div>

  <!-- 🟢 Approved By -->
  <div class="text-center">
    <div class="fw-semibold mb-1 signature-name approved">
      <i class="bx bx-check-circle me-1"></i>
      <?= esc($grn['approved_by_name'] ?? '-') ?>
    </div>
    <hr class="signature-line">
    <small class="text-muted d-block">Approved By</small>
    <?php if (!empty($grn['approved_at'])): ?>
      <small class="text-secondary fst-italic">
        <?= date('d M Y, h:i A', strtotime($grn['approved_at'])) ?>
      </small>
    <?php endif; ?>
  </div>

</div>



</div>
</div>
<!-- ✅ CSS (Clean & Structured) -->
<style>
    /* ===============================================
   🔹 GENERAL CARD & TABLE STYLING
================================================== */
.card {
  border-radius: 10px;
  border: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.table thead th {
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.9rem;
  color: #475569;
}

.table tbody td {
  font-size: 0.95rem;
  color: #1e293b;
}

.badge {
  font-size: 0.9rem;
  border-radius: 8px;
  font-weight: 500;
}

.bg-purple {
  background-color: #6f42c1 !important;
}

/* ===============================================
   🔹 STATUS TIMELINE (HORIZONTAL)
================================================== */
.status-timeline {
  position: relative;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-top: 10px;
  padding-top: 25px;
}

.status-timeline .timeline-line {
  position: absolute;
  top: 30px;
  left: 0;
  right: 0;
  height: 4px;
  background: #e5e7eb;
  border-radius: 3px;
  z-index: 1;
}

/* Step container */
.status-timeline .timeline-step {
  text-align: center;
  flex: 1;
  position: relative;
  z-index: 2;
  transition: all 0.3s ease;
  cursor: pointer;
}

/* Step icon */
.status-timeline .timeline-step i {
  font-size: 1.3rem;
  background: #f1f5f9;
  color: #9ca3af;
  border-radius: 50%;
  padding: 8px;
  transition: all 0.25s ease-in-out;
}

/* Step labels */
.status-timeline .timeline-step span {
  display: block;
  margin-top: 6px;
  font-size: 0.85rem;
  font-weight: 600;
  color: #475569;
  white-space: nowrap;
  transition: all 0.25s ease-in-out;
}

/* Active step */
.status-timeline .timeline-step.active i {
  background: #22c55e;
  color: #fff;
  box-shadow: 0 0 10px rgba(34, 197, 94, 0.4);
  transform: scale(1.05);
}

.status-timeline .timeline-step.active span {
  color: #16a34a;
}

/* Inactive dimming */
.status-timeline .timeline-step:not(.active) i {
  opacity: 0.6;
}

.status-timeline .timeline-step:not(.active):hover i {
  opacity: 0.9;
}

/* Hover effect */
.status-timeline .timeline-step:hover i {
  transform: scale(1.1);
  box-shadow: 0 0 8px rgba(37, 99, 235, 0.3);
}

/* Progress bar */
.status-timeline::after {
  content: "";
  position: absolute;
  top: 30px;
  left: 0;
  height: 4px;
  background: linear-gradient(90deg, #22c55e, #86efac);
  width: 0;
  border-radius: 4px;
  z-index: 1;
  transition: width 1.5s cubic-bezier(0.25, 1, 0.5, 1);
}

/* Progress levels */
.status-timeline[data-progress="25"]::after { width: 25%; }
.status-timeline[data-progress="50"]::after { width: 50%; }
.status-timeline[data-progress="75"]::after { width: 75%; }
.status-timeline[data-progress="100"]::after { width: 100%; }

/* Rejected state */
.status-timeline.rejected::after {
  background: linear-gradient(90deg, #ef4444, #f87171);
}

.status-timeline.rejected .timeline-step.active i {
  background: #dc2626;
  color: #fff;
}

/* Optional: glow by stage */
.status-timeline[data-progress="25"] .timeline-step.active i { box-shadow: 0 0 10px rgba(245,158,11,0.4); }
.status-timeline[data-progress="50"] .timeline-step.active i { box-shadow: 0 0 10px rgba(99,102,241,0.4); }
.status-timeline[data-progress="75"] .timeline-step.active i { box-shadow: 0 0 10px rgba(22,163,74,0.4); }
.status-timeline.rejected .timeline-step.active i { box-shadow: 0 0 10px rgba(220,38,38,0.4); }

/* ===============================================
   🔹 SIGNATURE SECTION
================================================== */
.signature-section {
  margin-top: 40px;
  display: flex;
  justify-content: space-between;
  gap: 15px;
  text-align: center;
}

.signature-name {
  min-height: 24px;
  font-size: 1rem;
  font-weight: 600;
  letter-spacing: 0.3px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: 0.2s ease-in-out;
}

.signature-name i {
  font-size: 1.1rem;
  opacity: 0.9;
  margin-right: 4px;
}

/* Role-based colors */
.signature-name.received { color: #2563eb; }  /* Blue */
.signature-name.verified { color: #7c3aed; }  /* Purple */
.signature-name.approved { color: #16a34a; }  /* Green */

/* Hover glow */
.signature-name:hover {
  text-shadow: 0 0 6px rgba(37, 99, 235, 0.2);
  transform: scale(1.03);
}

.signature-line {
  width: 180px;
  margin: auto;
  border: 1px solid #9ca3af;
  opacity: 0.8;
}

.signature-section small {
  font-size: 0.83rem;
  color: #6b7280;
}

.signature-section .fst-italic {
  font-style: italic;
  font-size: 0.82rem;
  color: #9ca3af;
}

/* ===============================================
   🔹 PRINT VIEW OPTIMIZATION
================================================== */
@media print {
  .no-print { display: none !important; }
  body { background: #fff !important; }

  .card, .page-header {
    box-shadow: none !important;
    border: none !important;
  }

  .table { border: 1px solid #ddd; }

  .signature-section {
    justify-content: space-around !important;
  }

  @page { margin: 15mm; }
}

</style>

<!-- ✅ JS (Organized & Polished) -->
<script>
document.addEventListener("DOMContentLoaded", () => {

  /* -------------------------------------------
     🔸 1. Initialize Timeline Progress Animation
  --------------------------------------------*/
  const timeline = document.querySelector(".status-timeline");
  if (timeline) {
    const steps = timeline.querySelectorAll(".timeline-step");
    const activeSteps = timeline.querySelectorAll(".timeline-step.active").length;
    const progress = ((activeSteps - 1) / (steps.length - 1)) * 100;
    timeline.setAttribute("data-progress", progress >= 0 ? progress : 0);

    // Animate icons one-by-one
    steps.forEach((step, i) => {
      setTimeout(() => step.classList.add("visible"), i * 200);
    });
  }

  /* -------------------------------------------
     🔸 2. Enable Bootstrap Tooltips
  --------------------------------------------*/
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

  /* -------------------------------------------
     🔸 3. Print & PDF Download
  --------------------------------------------*/
  const printBtn = document.getElementById("btnPrint");
  const pdfBtn = document.getElementById("btnDownloadPDF");

  if (printBtn) {
    printBtn.addEventListener("click", () => window.print());
  }

  if (pdfBtn) {
    pdfBtn.addEventListener("click", () => {
      const content = document.getElementById("grnContent");
      const opt = {
        margin: 0.5,
        filename: "<?= esc($grn['grn_no']) ?>_GRN.pdf",
        image: { type: "jpeg", quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: "in", format: "a4", orientation: "portrait" }
      };

      // Custom PDF header
      const header = document.createElement("div");
      header.style.textAlign = "center";
      header.style.marginBottom = "10px";
      header.innerHTML = `
        <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo" style="width:80px;">
        <h3 style="margin:5px 0;">Raw Material Management System</h3>
        <h5 style="margin:0;">Goods Receipt Note (GRN)</h5>
        <hr style="margin-top:8px;">
      `;

      const clone = content.cloneNode(true);
      const wrapper = document.createElement("div");
      wrapper.appendChild(header);
      wrapper.appendChild(clone);

      html2pdf().set(opt).from(wrapper).save();
    });
  }
});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<?= $this->include('layout/footer') ?>
