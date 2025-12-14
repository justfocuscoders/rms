<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid pt-1 pb-5 mb-5">
  <h3 class="fw-semibold mb-3 mt-0">Admin Dashboard</h3>

  <!-- 🔔 Alerts -->
  <div class="row mb-3">
    <?php if ($low_stock_count > 0): ?>
      <div class="col-12">
        <div class="alert alert-warning shadow-sm mb-2">
          ⚠️ <?= $low_stock_count ?> items are below reorder level!
        </div>
      </div>
    <?php endif; ?>
    <?php if ($pending_po > 0 || $pending_mrs > 0 || $qc_pending > 0): ?>
      <div class="col-12">
        <div class="alert alert-info shadow-sm mb-0">
          🕒 Pending: <?= $pending_po ?> POs | <?= $pending_mrs ?> MRS | <?= $qc_pending ?> QC Checks
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- 🔹 Summary Cards -->
  <div class="row g-3 mb-4">
    <?php
      $cards = [
        ['Total POs', $total_pos, 'bx bx-cart', base_url('purchaseorders')],
        ['Total MRS', $total_mrs, 'bx bx-list-check', base_url('mrs')],
        ['Total GRNs', $total_grn, 'bx bx-package', base_url('grn')],
        ['Total Users', $total_users, 'bx bx-user', base_url('users')],
        ['Suppliers', $total_suppliers, 'bx bx-group', base_url('suppliers')],
        ['Stock Items', $total_stock_items, 'bx bx-cube', base_url('stock')]
      ];
    ?>

    <?php foreach($cards as $c): ?>
      <div class="col-md-4 col-lg-2">
        <a href="<?= $c[3] ?>" class="text-decoration-none text-dark dashboard-card">
          <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
              <i class="<?= $c[2] ?> text-primary fs-3 mb-2"></i>
              <h6 class="text-muted mb-1"><?= $c[0] ?></h6>
              <h3 class="fw-bold mb-0"><?= $c[1] ?></h3>
            </div>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- 🔹 Charts -->
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card shadow-sm border-0">
        <div class="card-header fw-semibold">Top Stock Items</div>
        <div class="card-body"><canvas id="stockChart"></canvas></div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card shadow-sm border-0">
        <div class="card-header fw-semibold">PO Trend (Last 6 Months)</div>
        <div class="card-body"><canvas id="poChart"></canvas></div>
      </div>
    </div>
  </div>

  <!-- 🔹 Recent Activities -->
  <div class="row g-3">
    <!-- Purchase Orders -->
    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-header fw-semibold">Recent Purchase Orders</div>
        <div class="card-body p-0">
          <table class="table table-striped mb-0">
            <thead><tr><th>PO No</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
              <?php foreach($recent_pos as $r): ?>
              <tr>
                <td><?= esc($r['po_number']) ?></td>
                <td><span class="badge bg-info"><?= esc($r['status']) ?></span></td>
                <td><?= esc($r['created_at']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- MRS -->
    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-header fw-semibold">Recent MRS</div>
        <div class="card-body p-0">
          <table class="table table-striped mb-0">
            <thead><tr><th>MRS No</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
              <?php foreach($recent_mrs as $r): ?>
              <tr>
                <td><?= esc($r['mrs_no']) ?></td>
                <td><span class="badge bg-warning"><?= esc($r['status']) ?></span></td>
                <td><?= esc($r['created_at']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- GRNs -->
    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-header fw-semibold">Recent GRNs</div>
        <div class="card-body p-0">
          <table class="table table-striped mb-0">
            <thead><tr><th>GRN No</th><th>PO ID</th><th>Supplier</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
              <?php foreach($recent_grn as $r): ?>
              <tr>
                <td><?= esc($r['grn_no']) ?></td>
                <td><?= esc($r['po_id']) ?></td>
                <td><?= esc($r['supplier_id']) ?></td>
                <td><span class="badge bg-info"><?= esc($r['status']) ?></span></td>
                <td><?= esc($r['grn_date']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <!-- 🔹 Analytics & Insights -->
<div class="row g-3 mt-4">
  <!-- 🔹 Smart Chart Panel -->
<div class="col-md-8">
  <div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span class="fw-semibold">Dashboard Analytics</span>
      <select id="chartSelect" class="form-select form-select-sm w-auto">
        <option value="monthly" selected>Monthly Overview</option>
        <option value="status">Pending vs Completed</option>
        <option value="suppliers">Top Suppliers</option>
        <option value="stock">Stock Value by Category</option>
      </select>
    </div>
    <div class="card-body" style="height:350px;">
      <canvas id="smartChart"></canvas>
    </div>
  </div>
</div>


  <!-- User Role Breakdown -->
  <div class="col-md-4">
    <div class="card shadow-sm border-0">
      <div class="card-header fw-semibold">User Role Breakdown</div>
      <div class="card-body" style="height:300px">
        <canvas id="roleChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Recent Activities -->
  <div class="col-md-4">
    <div class="card shadow-sm border-0">
      <div class="card-header fw-semibold">Recent Activities</div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush small">
          <?php if (!empty($activities)): ?>
            <?php foreach($activities as $a): ?>
              <li class="list-group-item">
                <i class="bx bx-time-five text-primary me-2"></i>
                <strong><?= esc($a['user']) ?></strong> <?= esc($a['action']) ?>
                <span class="text-muted float-end"><?= esc($a['time']) ?></span>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="list-group-item text-muted">No recent activity.</li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </div>
</div>

</div>
</div>
<!-- 🔹 Charts Script -->
<script>
const stockData = <?= json_encode($stock_chart) ?>;
new Chart(document.getElementById('stockChart'), {
  type: 'bar',
  data: {
    labels: stockData.map(r => r.item_name),
    datasets: [{
      label: 'Qty Available',
      data: stockData.map(r => r.qty_available),
      backgroundColor: 'rgba(54, 162, 235, 0.5)',
      borderColor: 'rgba(54, 162, 235, 1)',
      borderWidth: 1
    }]
  },
  options: { responsive: true, maintainAspectRatio: false }
});

const poData = <?= json_encode($po_trend) ?>;
new Chart(document.getElementById('poChart'), {
  type: 'line',
  data: {
    labels: poData.map(r => r.month),
    datasets: [{
      label: 'POs Created',
      data: poData.map(r => r.total),
      borderColor: '#007bff',
      backgroundColor: 'rgba(0,123,255,0.1)',
      tension: 0.3
    }]
  },
  options: { responsive: true, maintainAspectRatio: false }
});
</script>
<script>
const chartCtx = document.getElementById('smartChart').getContext('2d');

// All datasets
const monthlyData = <?= json_encode($monthly_stats) ?>;
const statusData = <?= json_encode($pending_status) ?>;
const supplierData = <?= json_encode($top_suppliers) ?>;
const stockData = <?= json_encode($stock_value) ?>;

// Function to create chart dynamically
let smartChart;
function renderChart(type) {
  if (smartChart) smartChart.destroy();

  if (type === 'monthly') {
    smartChart = new Chart(chartCtx, {
      type: 'bar',
      data: {
        labels: monthlyData.map(d => d.month),
        datasets: [
          { label: 'MRS', data: monthlyData.map(d => d.mrs), backgroundColor: 'rgba(0,123,255,0.6)' },
          { label: 'POs', data: monthlyData.map(d => d.po), backgroundColor: 'rgba(255,193,7,0.6)' },
          { label: 'GRNs', data: monthlyData.map(d => d.grn), backgroundColor: 'rgba(40,167,69,0.6)' }
        ]
      },
      options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
  }

  if (type === 'status') {
    smartChart = new Chart(chartCtx, {
      type: 'doughnut',
      data: {
        labels: Object.keys(statusData),
        datasets: [{
          data: Object.values(statusData),
          backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545']
        }]
      },
      options: { plugins: { legend: { position: 'bottom' } } }
    });
  }

  if (type === 'suppliers') {
    smartChart = new Chart(chartCtx, {
      type: 'bar',
      data: {
        labels: supplierData.map(s => s.supplier),
        datasets: [{
          label: 'Total GRNs',
          data: supplierData.map(s => s.total_grn),
          backgroundColor: 'rgba(0,123,255,0.6)'
        }]
      },
      options: { plugins: { legend: { display: false } } }
    });
  }

  if (type === 'stock' && stockData.length > 0) {
    smartChart = new Chart(chartCtx, {
      type: 'pie',
      data: {
        labels: stockData.map(c => c.category),
        datasets: [{
          data: stockData.map(c => c.value),
          backgroundColor: [
            'rgba(0,123,255,0.6)', 'rgba(40,167,69,0.6)',
            'rgba(255,193,7,0.6)', 'rgba(220,53,69,0.6)'
          ]
        }]
      },
      options: { plugins: { legend: { position: 'bottom' } } }
    });
  }
}

// Initial render
renderChart('monthly');

// Dropdown listener
document.getElementById('chartSelect').addEventListener('change', e => {
  renderChart(e.target.value);
});
</script>

<script>
/* ===== Department-wise MRS ===== */
const deptData = <?= json_encode($dept_mrs) ?>;
if (deptData.length > 0) {
  new Chart(document.getElementById('deptChart'), {
    type: 'pie',
    data: {
      labels: deptData.map(d => d.department),
      datasets: [{
        data: deptData.map(d => d.total),
        backgroundColor: [
          'rgba(0,123,255,0.6)',
          'rgba(0,201,167,0.6)',
          'rgba(255,193,7,0.6)',
          'rgba(220,53,69,0.6)',
          'rgba(111,66,193,0.6)',
          'rgba(108,117,125,0.6)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      plugins: { legend: { position: 'bottom' } },
      responsive: true
    }
  });
}

/* ===== User Role Breakdown ===== */
const roleData = <?= json_encode($role_breakdown) ?>;
if (roleData.length > 0) {
  new Chart(document.getElementById('roleChart'), {
    type: 'doughnut',
    data: {
      labels: roleData.map(r => r.role),
      datasets: [{
        data: roleData.map(r => r.total),
        backgroundColor: [
          'rgba(0,123,255,0.6)',
          'rgba(255,193,7,0.6)',
          'rgba(40,167,69,0.6)',
          'rgba(220,53,69,0.6)',
          'rgba(111,66,193,0.6)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      plugins: { legend: { position: 'bottom' } },
      cutout: '65%',
      responsive: true
    }
  });
}
</script>
<!-- Toast container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
  <div id="signupToast" class="toast align-items-center text-bg-success border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body">
        🔔 New signup request received!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<script>
// Keep track of old pending count
let lastPendingCount = 0;

async function checkNewSignups() {
  try {
    const res = await fetch("<?= base_url('/admin/check-signups') ?>");
    const data = await res.json();

    if (data.pending > lastPendingCount && lastPendingCount !== 0) {
      const toast = new bootstrap.Toast(document.getElementById('signupToast'));
      toast.show();
    }

    lastPendingCount = data.pending;
  } catch (err) {
    console.error('Error checking signups:', err);
  }
}

// Check every 10 seconds
setInterval(checkNewSignups, 10000);

// Run once at start
checkNewSignups();
</script>



<!-- 🔹 Hover Animation Styles -->
<style>
.dashboard-card .card {
  transition: all 0.4s ease;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  background: #fff;
  border-radius: 0.75rem;
}

/* 🔹 Lift + soft shadow */
.dashboard-card:hover .card {
  transform: translateY(-6px);
  box-shadow: 0 8px 25px rgba(0, 123, 255, 0.35),
              0 0 20px rgba(0, 123, 255, 0.25);
}

/* 🔹 Ambient blue glow background */
.dashboard-card .card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: radial-gradient(
    circle at 50% 40%,
    rgba(0, 123, 255, 0.25) 0%,
    rgba(0, 123, 255, 0.1) 40%,
    transparent 80%
  );
  opacity: 0;
  filter: blur(18px);
  transform: scale(1.3);
  transition: opacity 0.45s ease, transform 0.4s ease;
  z-index: 0;
}

.dashboard-card:hover .card::before {
  opacity: 1;
  transform: scale(1);
}

/* Keep content above glow */
.dashboard-card .card-body {
  position: relative;
  z-index: 1;
}

/* 🔹 Icon interaction */
.dashboard-card i {
  transition: transform 0.3s ease, color 0.3s ease;
}
.dashboard-card:hover i {
  color: #007bff;
  transform: scale(1.2);
}

@keyframes subtlePulse {
  0% { opacity: 0.9; transform: scale(1); }
  50% { opacity: 1; transform: scale(1.03); }
  100% { opacity: 0.9; transform: scale(1); }
}

.dashboard-card:hover .card::before {
  animation: subtlePulse 2.5s infinite ease-in-out;
}

</style>



<?= $this->include('layout/footer') ?>
