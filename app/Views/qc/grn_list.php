<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<!-- Toast container (top-right) -->
<div aria-live="polite" aria-atomic="true" class="position-fixed" style="top: 1rem; right: 1rem; z-index: 1200;">
  <div id="qcToastContainer"></div>
</div>


<div class="page-header mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
  <h3 class="fw-semibold mb-0 text-dark">
    <i class="bx bx-list-check text-primary me-2"></i> GRN-wise Quality Control
  </h3>

  <div class="d-flex align-items-center gap-2">
    <div class="btn-group btn-group-sm" role="group" aria-label="filters">
      <button class="btn btn-outline-primary filter-main active" data-filter="all">All</button>
      <button class="btn btn-outline-warning filter-main" data-filter="pending">Pending</button>
      <button class="btn btn-outline-success filter-main" data-filter="completed">Completed</button>
    </div>
    <button class="btn btn-sm btn-outline-secondary" id="refreshNow">
      <i class="bx bx-refresh"></i> Refresh
    </button>
    <small class="text-muted ms-2" id="lastUpdated">Last updated: <?= date('d M Y H:i:s') ?></small>
  </div>
</div>

<!-- Pending GRNs quick badges (optional) -->
<div id="pendingBanner" class="mb-3"></div>

<div class="card shadow-sm border-0 rounded-3">
  <div class="card-body table-responsive p-0">
    <table class="table table-bordered align-middle table-hover mb-0" id="grnQcTable">
      <thead class="table-light">
        <tr>
          <th>GRN No</th>
          <th>Supplier</th>
          <th>Date</th>
          <th class="text-center">Total</th>
          <th class="text-center">Tested</th>
          <th class="text-center text-success">Accepted</th>
          <th class="text-center text-danger">Rejected</th>
          <th class="text-center text-warning">Pending</th>
          <th class="text-center">Progress</th>
          <th class="text-center">Action</th>
        </tr>
      </thead>
      <tbody id="grnQcBody">
        <?php if (!empty($grn_qc_summary)): ?>
          <?php foreach ($grn_qc_summary as $row): 
            // compute percentages on server to show initial values
            $total = (int)$row['total_items'];
            $accepted = (int)$row['accepted'];
            $rejected = (int)$row['rejected'];
            $pending = (int)$row['pending'];
            $tested = $accepted + $rejected;
            $completion = $total > 0 ? round(($tested/$total) * 100) : 0;
            $accPct = $total > 0 ? round(($accepted/$total) * 100) : 0;
            $rejPct = $total > 0 ? round(($rejected/$total) * 100) : 0;
            $pendPct = $total > 0 ? round(($pending/$total) * 100) : 0;
          ?>
            <tr data-grn-id="<?= esc($row['grn_id']) ?>"
                data-pending="<?= $pending ?>"
                data-completion="<?= $completion ?>">
              <td><strong><?= esc($row['grn_no']) ?></strong></td>
              <td><?= esc($row['supplier_name']) ?></td>
              <td><?= date('d M Y', strtotime($row['grn_date'])) ?></td>
              <td class="text-center"><?= $total ?></td>
              <td class="text-center"><?= $tested ?></td>
              <td class="text-center text-success"><?= $accepted ?></td>
              <td class="text-center text-danger"><?= $rejected ?></td>
              <td class="text-center text-warning"><?= $pending ?></td>
              <td style="min-width:220px;">
                <div class="progress mb-1" style="height:12px;">
                  <div class="progress-bar bg-success" role="progressbar" style="width:<?= $accPct ?>%"></div>
                  <div class="progress-bar bg-danger" role="progressbar" style="width:<?= $rejPct ?>%"></div>
                  <div class="progress-bar bg-secondary" role="progressbar" style="width:<?= $pendPct ?>%"></div>
                </div>
                <small class="text-muted"><?= $completion ?>% tested</small>
              </td>
              <td class="text-center">
                  
                <!-- Always show View Summary -->
  <a href="<?= base_url('/qc/view/'.$row['grn_id']) ?>"
     class="btn btn-sm btn-outline-dark rounded-pill px-3 shadow-sm d-inline-flex align-items-center">
    <i class="bx bx-show me-1"></i> View
  </a>
  
  <?php
    // Dynamic button color based on completion percentage
    if ($completion === 100) {
        $btnClass = 'btn-success';
        $btnLabel = '<i class="bx bx-check-circle me-1"></i> Completed';
    } elseif ($completion >= 25) {
        $btnClass = 'btn-primary';
        $btnLabel = '<i class="bx bx-flask me-1"></i> Continue Test';
    } else {
        $btnClass = 'btn-warning';
        $btnLabel = '<i class="bx bx-flask me-1"></i> Test Now';
    }
  ?>

  <!-- Dynamic Test Button -->
  <a href="<?= base_url('/qc/test/'.$row['grn_id']) ?>"
     class="btn btn-sm <?= $btnClass ?> rounded-pill px-3 shadow-sm position-relative me-1 d-inline-flex align-items-center">
     <?= $btnLabel ?>
     <?php if ($pending > 0): ?>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
          <?= $pending ?>
        </span>
     <?php endif; ?>
  </a>

  
</td>



            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="10" class="text-center text-muted">No GRNs found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<!-- small style polish -->
<style>
  .progress { border-radius: 8px; overflow: hidden; }
  .progress-bar { transition: width 0.8s ease; }
  #pendingBanner .badge { margin-right: .4rem; }
  .filter-main.active { font-weight: 600; }
  .btn.rounded-pill {
  font-weight: 500;
  transition: all 0.25s ease-in-out;
}

.btn.rounded-pill i {
  font-size: 1.1rem;
}

.btn-warning {
  background: linear-gradient(45deg, #ffca2c, #ffc107);
  border: none;
  color: #5a4300;
}

.btn-primary {
  background: linear-gradient(45deg, #007bff, #0056b3);
  border: none;
}

.btn-success {
  background: linear-gradient(45deg, #28a745, #218838);
  border: none;
}

.btn-outline-dark:hover {
  background-color: #343a40;
  color: #fff !important;
}

.btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}

</style>

<!-- Live JS: filters + auto-refresh -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const filterButtons = document.querySelectorAll('.filter-main');
  const tbody = document.getElementById('grnQcBody');
  const pendingBanner = document.getElementById('pendingBanner');
  const lastUpdated = document.getElementById('lastUpdated');
  const refreshBtn = document.getElementById('refreshNow');
  const toastContainer = document.getElementById('qcToastContainer');

  // client-side known list of GRN ids (initially set after first fetch)
  let knownGrnIds = new Set();

  // filter buttons behavior
  filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      filterButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      applyFilter(btn.getAttribute('data-filter'));
    });
  });

  function applyFilter(filter) {
    const rows = tbody.querySelectorAll('tr[data-grn-id]');
    rows.forEach(r => {
      const pending = parseInt(r.getAttribute('data-pending') || 0, 10);
      const completion = parseInt(r.getAttribute('data-completion') || 0, 10);
      let show = true;
      if (filter === 'pending') show = pending > 0;
      else if (filter === 'completed') show = completion === 100;
      r.style.display = show ? '' : 'none';
    });
  }

  refreshBtn.addEventListener('click', () => fetchAndUpdate());

  function renderPendingBanner(items) {
    let html = '';
    if (items && items.length) {
      html = '<div class="alert alert-warning shadow-sm rounded-3"><strong>Pending QC GRNs: </strong>';
      items.slice(0,6).forEach(g => {
        html += '<span class="badge bg-light text-dark border ms-2"> ' + escapeHtml('GRN-' + g.grn_no) + '</span>';
      });
      if (items.length > 6) html += ' <small class="text-muted ms-2">and ' + (items.length - 6) + ' more</small>';
      html += '</div>';
    }
    pendingBanner.innerHTML = html;
  }

  function escapeHtml(s) {
    return String(s).replace(/[&<>"'`=\/]/g, function (c) {
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;','`':'&#x60;','=':'&#x3D;'}[c];
    });
  }

  // show bootstrap toast
  function showToast(title, message, delay = 6000) {
    // create toast element
    const id = 'toast-' + Date.now();
    const toastEl = document.createElement('div');
    toastEl.className = 'toast align-items-center text-white bg-primary border-0';
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');
    toastEl.id = id;
    toastEl.style.minWidth = '220px';
    toastEl.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">
          <strong>${escapeHtml(title)}</strong><div class="mt-1">${escapeHtml(message)}</div>
        </div>
        <button type="button" class="btn-close btn-close-white me-1 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    `;
    toastContainer.appendChild(toastEl);

    // initialize and show using Bootstrap's toast API
    const bsToast = new bootstrap.Toast(toastEl, { delay });
    bsToast.show();

    // remove from DOM after hide
    toastEl.addEventListener('hidden.bs.toast', () => {
      toastEl.remove();
    });
  }

  // fetch and update table + pending banner; detect new GRNs
  async function fetchAndUpdate() {
    try {
      const resp = await fetch('<?= base_url('qc/refreshGrnList') ?>', { cache: 'no-store' });
      if (!resp.ok) throw new Error('Network response problem');
      const data = await resp.json();

      // On first fetch when knownGrnIds empty → populate known set silently
      if (knownGrnIds.size === 0) {
        (data.grnIds || []).forEach(g => knownGrnIds.add(String(g.grn_id)));
      } else {
        // detect newly arrived GRNs (present in data.grnIds but not in known set)
        const newOnes = [];
        (data.grnIds || []).forEach(g => {
          if (!knownGrnIds.has(String(g.grn_id))) {
            newOnes.push(g);
          }
        });
        if (newOnes.length > 0) {
          // push all new ids into known set so we don't re-notify repeatedly
          newOnes.forEach(g => knownGrnIds.add(String(g.grn_id)));

          // show toast for each new GRN (or aggregate)
          if (newOnes.length === 1) {
            const g = newOnes[0];
            showToast('New GRN received', `GRN-${g.grn_no} is waiting for QC.`, 8000);
          } else {
            showToast('New GRNs received', `${newOnes.length} new GRNs are waiting for QC.`, 9000);
          }
        }
      }

      // replace table body HTML
      if (data.html) tbody.innerHTML = data.html;

      // render pending banner
      renderPendingBanner(data.pendingList || []);

      // update last updated timestamp
      const now = new Date();
      lastUpdated.textContent = 'Last updated: ' + now.toLocaleString();

      // re-apply currently selected filter
      const active = document.querySelector('.filter-main.active')?.getAttribute('data-filter') || 'all';
      applyFilter(active);

    } catch (err) {
      console.error('Error refreshing GRN QC list:', err);
    }
  }

  // initial load + start interval
  fetchAndUpdate();
  setInterval(fetchAndUpdate, 10000);
});
</script>


<?= $this->include('layout/footer') ?>
