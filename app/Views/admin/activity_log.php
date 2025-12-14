<?= $this->include('layout/header') ?>

<div class="container-fluid p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-semibold mb-0">
      <i class="bx bx-time-five text-primary me-2"></i>
      User Activity Log
    </h4>
    <small class="text-muted">
      Auto-refreshes every 10 seconds
    </small>
  </div>

  <div class="table-responsive shadow-sm rounded">
    <table class="table table-bordered table-hover table-sm align-middle">
      <thead class="table-light sticky-top" style="top: 0; z-index: 10;">
        <tr>
          <th>User</th>
          <th>Action</th>
          <th>Module</th>
          <th>Record ID</th>
          <th>IP</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($logs)): ?>
          <?php foreach ($logs as $log): ?>
            <tr>
              <td><strong><?= esc($log['name']) ?></strong></td>
              <td><?= esc($log['action']) ?></td>
              <td><span class="badge bg-secondary"><?= esc($log['module']) ?></span></td>
              <td><?= esc($log['record_id']) ?></td>
              <td><code><?= esc($log['ip_address']) ?></code></td>
              <td><?= date('d M Y, H:i', strtotime($log['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center text-muted py-3">
              <i class="bx bx-info-circle me-1"></i> No activity logs found.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="mt-3">
    <?= $pager->links() ?>
  </div>
</div>

<script>
  // 🕒 Auto-refresh only if on first page (avoid losing pagination)
  const urlParams = new URLSearchParams(window.location.search);
  const page = urlParams.get('page') || 1;
  if (page == 1) {
      setInterval(() => {
          location.reload();
      }, 10000);
  }
</script>

<?= $this->include('layout/footer') ?>
