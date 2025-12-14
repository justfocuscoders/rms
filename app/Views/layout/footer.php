</div> <!-- /.content -->
</div> <!-- page-content -->
</div> <!-- page-wrapper -->

<!-- ✅ FOOTER -->
<footer class="page-footer">
  <p class="mb-0">© <?= date('Y') ?> Raw Material System</p>
</footer>

</div> <!-- wrapper -->

<!-- ✅ Core Libraries (load once, in correct order) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ✅ Sidebar Toggle + Dropdown Init -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  const sidebar = document.getElementById("sidebar");
  const toggleBtn = document.querySelector(".toggle-icon");
  const pageWrapper = document.querySelector(".page-wrapper");
  const topbar = document.querySelector(".topbar");
  const footer = document.querySelector(".page-footer");

  // Restore saved sidebar state
  if (localStorage.getItem("sidebarState") === "collapsed") {
    sidebar.classList.add("collapsed");
    pageWrapper.classList.add("expanded");
    topbar.classList.add("expanded");
    footer.classList.add("expanded");
  }

  // Toggle sidebar
  toggleBtn?.addEventListener("click", function() {
    const isCollapsed = sidebar.classList.toggle("collapsed");
    pageWrapper.classList.toggle("expanded", isCollapsed);
    topbar.classList.toggle("expanded", isCollapsed);
    footer.classList.toggle("expanded", isCollapsed);
    localStorage.setItem("sidebarState", isCollapsed ? "collapsed" : "expanded");
  });

  // --- Dropdowns ---
  document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(el => {
    new bootstrap.Dropdown(el);
  });

  // --- Tooltips ---
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(el => new bootstrap.Tooltip(el, {
    delay: { show: 50, hide: 100 },
    animation: true,
    customClass: 'tooltip-fade'
  }));

  // --- SweetAlert Notifications ---
  <?php if(session()->getFlashdata('success')): ?>
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: '<?= session()->getFlashdata('success') ?>',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true
    });
  <?php endif; ?>

  <?php if(session()->getFlashdata('error')): ?>
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: '<?= session()->getFlashdata('error') ?>',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true
    });
  <?php endif; ?>
});
</script>

<!-- ✅ Tooltip Style + Footer Fix -->
<style>
.tooltip.tooltip-fade .tooltip-inner {
  background-color: #1e293b;
  color: #fff;
  font-size: 0.75rem;
  padding: 6px 10px;
  border-radius: 6px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.tooltip.show { opacity: 1 !important; }

/* === Footer alignment fix === */
.page-footer {
  position: fixed;
  bottom: 0;
  left: 250px;
  right: 0;
  height: 40px;
  background: #fff;
  border-top: 1px solid #dee2e6;
  text-align: center;
  z-index: 1030;
  transition: left .3s ease;
}
.page-footer.expanded {
  left: 70px !important; /* when sidebar collapsed */
}

/* === Prevent content overlap with footer === */
body {
  padding-bottom: 60px !important;
}
</style>

<!-- 🔒 CSRF Utilities -->
<script>
function getCsrfToken() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta ? meta.content : '';
}
function refreshCsrfToken(newToken) {
  if (newToken) {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) meta.setAttribute('content', newToken);
  }
}
async function csrfFetch(url, options = {}) {
  const token = getCsrfToken();
  const headers = {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': token,
    ...(options.headers || {})
  };
  const response = await fetch(url, { ...options, headers });
  const newToken = response.headers.get('X-CSRF-TOKEN');
  refreshCsrfToken(newToken);
  return response;
}
async function postJSON(url, data) {
  const res = await csrfFetch(url, {
    method: 'POST',
    body: JSON.stringify(data)
  });
  return res.json();
}
</script>

</body>
</html>
