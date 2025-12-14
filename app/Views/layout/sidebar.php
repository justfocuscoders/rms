<div class="sidebar" id="sidebar">
  <!-- Logo + Toggle -->
  <div class="logo d-flex align-items-center justify-content-between px-3 py-2">
    <div class="d-flex align-items-center">
      <i class="bx bx-cube fs-4 me-2 text-primary"></i>
      <span class="logo-text fw-bold">RMS</span>
    </div>
    <i class="bx bx-menu toggle-icon fs-4" style="cursor:pointer"></i>
  </div>

  <?php
  use App\Models\QcResultModel;

  // Normalize role once (always lowercase)
  $role = strtolower((string) (session('role') ?? session('role_name') ?? ''));
  $isAdmin = ($role === 'admin');

  // QC pending counts (ensure integers)
  $pendingItemCount = (int) (new QcResultModel())->where('qc_status', 'Pending')->countAllResults();

  $db = \Config\Database::connect();
  $row = $db->query("
      SELECT COUNT(DISTINCT g.id) AS c
      FROM grn g
      JOIN grn_details gd ON gd.grn_id = g.id
      JOIN qc_results qr ON qr.grn_detail_id = gd.id
      WHERE qr.qc_status = 'Pending'
  ")->getRow();

  $pendingGrnCount = (int) ($row->c ?? 0);

  // Dashboard route mapping - include manager/staff
  $dashboardRoutes = [
      'admin'       => '/admin/dashboard',
      'manager'     => '/warehouse/dashboard',  // manager -> warehouse dashboard
      'warehouse'   => '/dashboard/warehouse',
      'staff'       => '/qc/dashboard',        // staff -> qc dashboard
      'qc'          => '/qc/dashboard',
      'production'  => '/production/dashboard',
      'procurement' => '/procurement/dashboard',
  ];
  $dashLink = $dashboardRoutes[$role] ?? '/dashboard';
  ?>

  <!-- (optional) remove or keep debug -->
  <!-- <?= '<!-- ROLE: ' . $role . ' -->' ?> 

  <ul class="list-unstyled mt-3">

    <!-- DASHBOARD -->
    <li>
      <a href="<?= base_url($dashLink) ?>" class="d-flex align-items-center">
        <i class="bx bx-home"></i>
        <span class="menu-title ms-2">Dashboard</span>
      </a>
    </li>

    <!-- SECTION LABEL: OPERATIONS -->
    <li class="text-muted small px-3 mt-3 mb-1 fw-semibold">Operations</li>

    <?php if ($isAdmin || in_array($role, ['procurement'])): ?>
      <li>
        <a href="<?= base_url('/purchaseorders/list') ?>" class="d-flex align-items-center">
          <i class="bx bx-cart text-primary"></i>
          <span class="menu-title ms-2">Purchase Orders</span>
        </a>
      </li>
    <?php endif; ?>

    <?php if ($isAdmin || in_array($role, ['warehouse','manager'])): ?>
      <li>
        <a href="<?= base_url('/grn/list') ?>" class="d-flex align-items-center">
          <i class="bx bxs-truck"></i>
          <span class="menu-title ms-2">GRN</span>
        </a>
      </li>
    <?php endif; ?>

    <?php if ($isAdmin || in_array($role, ['qc','staff'])): ?>
      <li class="nav-item position-relative">
        <a href="<?= base_url('/qc') ?>" class="d-flex align-items-center">
          <i class="bx bxs-vial text-primary fs-5 me-2"></i>
          <span class="menu-title">Quality Control</span>

          <?php if ($pendingItemCount > 0): ?>
            <span class="qc-badge"><?= $pendingItemCount ?></span>
          <?php endif; ?>

          <?php if ($pendingGrnCount > 0): ?>
            <span class="ms-2 text-muted small">(<?= $pendingGrnCount ?> GRNs)</span>
          <?php endif; ?>
        </a>
      </li>
    <?php endif; ?>

    <?php if ($isAdmin || in_array($role, ['warehouse','manager'])): ?>
      <li>
        <a href="<?= base_url('/warehouse') ?>" class="d-flex align-items-center">
          <i class="bx bx-package text-success"></i>
          <span class="menu-title ms-2">Warehouse / Inventory</span>
        </a>
      </li>
    <?php endif; ?>

    <?php if ($isAdmin): ?>
      <li class="text-muted small px-3 mt-3 mb-1 fw-semibold">Masters</li>

      <li><a href="<?= base_url('/items/list') ?>"><i class="bx bx-list-ul"></i><span class="menu-title ms-2">Items</span></a></li>
      <li><a href="<?= base_url('/suppliers/list') ?>"><i class="bx bxs-truck"></i><span class="menu-title ms-2">Suppliers</span></a></li>
      <li><a href="<?= base_url('/manufacturers/list') ?>"><i class="bx bx-cart"></i><span class="menu-title ms-2">Manufacturer</span></a></li>
      <li><a href="<?= base_url('/units/list') ?>"><i class="bx bx-ruler"></i><span class="menu-title ms-2">Units</span></a></li>
      <li><a href="<?= base_url('/departments/list') ?>"><i class="bx bx-building"></i><span class="menu-title ms-2">Departments</span></a></li>
      <li><a href="<?= base_url('/grn-category/list') ?>">
      <i class="bx bx-category"></i><span class="menu-title ms-2">GRN Category</span></a>
  </li>
  <li>
  <a href="<?= base_url('/locations/list') ?>">
    <i class="bx bx-map"></i>
    <span class="menu-title ms-2">Locations</span>
  </a>
</li>
<li>
  <a href="<?= base_url('/storage-locations/list') ?>">
    <i class="bx bx-grid-alt"></i>
    <span class="menu-title ms-2">Storage Locations</span>
  </a>
</li>

<li>
  <a href="<?= base_url('/storage-conditions/list') ?>">
    <i class="bx bx-thermometer"></i>
    <span class="menu-title ms-2">Storage Conditions</span>
  </a>
</li>


      <li class="text-muted small px-3 mt-3 mb-1 fw-semibold">Reports & Admin Tools</li>

      <li>
        <a href="<?= base_url('/reports') ?>" class="d-flex align-items-center">
          <i class="bx bx-bar-chart-alt"></i>
          <span class="menu-title ms-2">Reports</span>
        </a>
      </li>

      <li>
        <a href="<?= base_url('/admin/users') ?>" class="d-flex align-items-center">
          <i class="bx bxs-user-account"></i>
          <span class="menu-title ms-2">Manage Users</span>
        </a>
      </li>

      <li>
        <a href="<?= base_url('/admin/activitylog') ?>" class="d-flex align-items-center">
          <i class="bx bx-time-five text-primary"></i>
          <span class="menu-title ms-2">User Activity</span>
        </a>
      </li>
    <?php endif; ?>

    <!-- LOGOUT -->
    <li class="mt-3 border-top pt-2">
      <a href="<?= base_url('/logout') ?>" class="d-flex align-items-center text-danger">
        <i class="bx bx-log-out"></i>
        <span class="menu-title ms-2">Logout</span>
      </a>
    </li>
  </ul>
</div>

<style>
/* same styles as you already had */
.sidebar { background: #fff; border-right: 1px solid #e5e5e5; min-height: 100vh; transition: all 0.3s; }
.sidebar li a { padding: 8px 14px; border-radius: 8px; transition: background 0.25s ease; }
.sidebar li a:hover { background: #f1f3f6; }
.text-muted.small { color: #6c757d !important; font-size: 11px; letter-spacing: 0.5px; }
.qc-badge { background: linear-gradient(135deg, #ff3b3b, #ff7676); color: #fff; font-size: 12px; font-weight: bold; border-radius: 50%; padding: 4px 8px; line-height: 1; position: absolute; top: 8px; right: 14px; box-shadow: 0 0 6px rgba(255, 0, 0, 0.5); animation: qcPulse 1.5s infinite; }
@keyframes qcPulse { 0% { box-shadow: 0 0 6px rgba(255, 0, 0, 0.5); transform: scale(1); } 50% { box-shadow: 0 0 12px rgba(255,0,0,0.7); transform: scale(1.15); } 100% { box-shadow: 0 0 6px rgba(255, 0, 0, 0.5); transform: scale(1); } }
</style>
