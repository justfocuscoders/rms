<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?? 'RMS Dashboard' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= csrf_hash() ?>">

  <!-- 🧱 Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

  <!-- 🧩 Favicon -->
  <link rel="icon" type="image/png" href="<?= base_url('assets/img/rmslogo.png') ?>">

  <!-- 🧠 jQuery + Bootstrap Bundle (includes Popper) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- 🧭 Select2 (optional, use only if needed) -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


  <style>
    /* === Base === */
    body {
      font-family: "Segoe UI", sans-serif;
      background: #f9f9f9;
      margin: 0;
    }
    .wrapper {
      display: flex;
      min-height: 100vh;
    }

    /* === Sidebar === */
    .sidebar {
      width: 250px;
      background: #fff;
      color: #0f2862;
      position: fixed;
      top: 0; bottom: 0;
      transition: width .3s ease;
      overflow-y: auto;
      overflow-x: hidden;
      box-shadow: 2px 0 6px rgba(0,0,0,0.1);
      z-index: 1000;
    }
    .sidebar .logo {
      padding: 17px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-weight: bold;
      border-bottom: 2px solid #e0e0e0;
      margin-bottom: 10px;
    }
    .sidebar ul { list-style: none; margin: 0; padding: 0; }
    .sidebar ul li a {
      display: flex;
      align-items: center;
      padding: 12px 20px;
      color: #0f2862;
      text-decoration: none;
      transition: .2s;
      white-space: nowrap;
    }
    .sidebar ul li a:hover {
      background: #f1f5ff;
      color: #0f2862;
      padding-left: 25px;
    }
    .sidebar ul li a i { margin-right: 10px; font-size: 18px; }

    /* === Sidebar Collapse === */
    .sidebar.collapsed { width: 70px; }
    .sidebar.collapsed .logo-text,
    .sidebar.collapsed .menu-title {
      opacity: 0;
      visibility: hidden;
      transition: opacity .2s ease, visibility .2s ease;
    }
    .sidebar.collapsed:hover { width: 250px; }
    .sidebar.collapsed:hover .logo-text,
    .sidebar.collapsed:hover .menu-title {
      opacity: 1; visibility: visible;
    }

    /* Sidebar Scrollbar (shows only on hover) */
    .sidebar::-webkit-scrollbar { width: 6px; background-color: transparent; }
    .sidebar::-webkit-scrollbar-thumb { background-color: transparent; border-radius: 10px; }
    .sidebar:hover::-webkit-scrollbar-thumb { background-color: rgba(0,0,0,0.2); }

    /* === Topbar === */
    .topbar {
      position: fixed;
      top: 0; left: 250px; right: 0;
      height: 60px;
      background: #fff;
      border-bottom: 1px solid #ddd;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      transition: left .3s ease;
      z-index: 999;
    }
    .topbar.expanded { left: 70px; }
    .topbar .btn { margin-left: 10px; }

    /* === Breadcrumb === */
    .breadcrumb-container {
      background: #fff;
      position: fixed;
      top: 60px;
      left: 250px; right: 0;
      height: 42px;
      display: flex;
      align-items: center;
      padding: 0 30px;
      border-bottom: 1px solid #e5e7eb;
      box-shadow: 0 1px 2px rgba(0,0,0,0.03);
      z-index: 998;
      transition: left .3s ease;
    }
    .page-wrapper.expanded .breadcrumb-container { left: 70px; }

    .breadcrumb {
      margin: 0; padding: 0;
      background: transparent;
      display: flex;
      align-items: center;
      font-size: 0.9rem;
      font-weight: 500;
      color: #475569;
    }
    .breadcrumb-item + .breadcrumb-item::before {
      content: "›";
      color: #9ca3af;
      padding: 0 8px;
      font-weight: 600;
    }
    .breadcrumb-item a {
      color: #2563eb;
      text-decoration: none;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 5px;
      transition: all 0.2s ease-in-out;
    }
    .breadcrumb-item a:hover {
      color: #1d4ed8;
      text-decoration: underline;
      transform: translateY(-1px);
    }
    .breadcrumb-item.active { color: #111827; font-weight: 600; }
    .breadcrumb i { color: #2563eb; font-size: 1rem; }

    /* === Page Layout === */
    .page-wrapper { margin-left: 250px; flex-grow: 1; transition: margin-left .3s ease; }
    .page-wrapper.expanded { margin-left: 70px; }
    /* ✅ Remove white gap between collapsed sidebar and content */
.page-wrapper.expanded .page-content {
  margin-left: -1px !important;   /* adds a neat 2px gap */
  padding-left: 0 !important;
  transition: all 0.3s ease;
}

    .page-content { margin-top: 110px !important; }
    .content { margin: 0 auto; max-width: 1200px; }

    /* === Footer === */
    .page-footer {
      position: fixed;
      bottom: 0;
      left: 250px; right: 0;
      background: #fff;
      border-top: 1px solid #ddd;
      text-align: center;
      padding: 10px;
      z-index: 900 !important;
      transition: left .3s ease;
    }
    .page-footer.expanded { left: 70px; }

    /* === Dropdown === */
    .dropdown-menu {
      min-width: 180px;
      border-radius: 8px;
      font-size: 0.9rem;
      z-index: 1100 !important;
    }
    .dropdown-item:hover {
      background-color: #f1f5ff;
      color: #0f2862 !important;
    }
    
    /* Fix dashboard gap on sidebar collapse */
.page-wrapper.expanded .container-fluid {
  padding-left: 0 !important;
  padding-right: 0 !important;
}

.page-wrapper.expanded .page-content {
  margin-left: 0 !important;
}


  </style>
</head>
<body>
  <div class="wrapper">

    <?= $this->include('layout/sidebar') ?>

    <!-- ✅ TOPBAR -->
    <div class="topbar">
      <div class="d-flex align-items-center">
        <strong>Raw Material System</strong>
      </div>

      <div class="d-flex align-items-center gap-2">

        <!-- ✅ Quick Action Buttons -->
        <a href="<?= base_url('purchase/create') ?>" class="btn btn-sm btn-outline-primary d-flex align-items-center">
          <i class='bx bx-file-plus me-1'></i> Create PO
        </a>
        <a href="<?= base_url('suppliers/add') ?>" class="btn btn-sm btn-outline-success d-flex align-items-center">
          <i class='bx bx-user-plus me-1'></i> Add Supplier
        </a>
        <a href="<?= base_url('mrs/create') ?>" class="btn btn-sm btn-outline-warning d-flex align-items-center">
          <i class='bx bx-list-plus me-1'></i> Create MRS
        </a>

        <!-- ✅ User Dropdown -->
        <div class="dropdown ms-3">
          <button class="btn btn-light border-0 d-flex align-items-center dropdown-toggle"
                  id="userMenuButton"
                  data-bs-toggle="dropdown"
                  aria-expanded="false">
            <img src="<?= base_url('assets/img/rmslogo.png') ?>"
                 alt="User"
                 class="rounded-circle me-2"
                 width="32" height="32">
            <span class="fw-semibold text-dark"><?= session('name') ?? 'Admin' ?></span>
          </button>

          <ul class="dropdown-menu dropdown-menu-end shadow-sm mt-2" aria-labelledby="userMenuButton">
            <li>
              <a class="dropdown-item d-flex align-items-center gap-2" href="<?= base_url('profile') ?>">
                <i class='bx bx-user-circle'></i> Profile
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="<?= base_url('logout') ?>">
                <i class='bx bx-log-out'></i> Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- ✅ PAGE WRAPPER -->
    <div class="page-wrapper">

      <?php if (isset($breadcrumbs)): ?>
        <div class="breadcrumb-container">
          <?= view('layout/breadcrumb', compact('breadcrumbs')) ?>
        </div>
      <?php endif; ?>

      <div class="page-content">
        <div class="content">
