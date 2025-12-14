<?= $this->include('layout/header') ?>

<div class="page-header mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
  <div>
    <h3 class="fw-semibold mb-0 text-dark">
      <i class="bx bx-transfer-alt text-primary me-2"></i> <?= esc($page_title) ?>
    </h3>
    <small class="text-muted"><?= esc($sub_title) ?></small>
  </div>
  <a href="<?= base_url('store') ?>" class="btn btn-sm btn-outline-secondary">
    <i class="bx bx-arrow-back"></i> Back
  </a>
</div>

<div class="card shadow-sm p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-semibold text-dark mb-0">
      <i class="bx bx-history text-success me-2"></i> Movement History
    </h5>
    <a href="<?= base_url('stock-movements/export') ?>" class="btn btn-sm btn-outline-success">
      <i class="bx bx-download"></i> Export CSV
    </a>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered align-middle table-hover shadow-sm">
      <thead class="table-light">
        <tr class="text-center">
          <th>#</th>
          <th>Date / Time</th>
          <th>Item</th>
          <th>Batch</th>
          <th>Location</th>
          <th>Movement</th>
          <th>Qty</th>
          <th>Balance After</th>
          <th>Remarks</th>
          <th>By</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($movements)): ?>
          <?php foreach ($movements as $i => $m): ?>
            <tr>
              <td class="text-center"><?= $i + 1 ?></td>
              <td><?= date('d M Y, h:i A', strtotime($m['moved_at'])) ?></td>
              <td class="fw-semibold"><?= esc($m['item_name']) ?></td>
              <td><?= esc($m['batch_no'] ?? '-') ?></td>
              <td><?= esc($m['location_code'] ?? '-') ?> — <?= esc($m['location_name'] ?? '-') ?></td>
              <td class="text-center">
                <?php if ($m['movement_type'] === 'IN'): ?>
                  <span class="badge bg-success px-3">IN</span>
                <?php elseif ($m['movement_type'] === 'OUT'): ?>
                  <span class="badge bg-danger px-3">OUT</span>
                <?php else: ?>
                  <span class="badge bg-warning text-dark px-3">ADJ</span>
                <?php endif; ?>
              </td>
              <td class="text-end"><?= number_format($m['qty'], 2) ?></td>
              <td class="text-end text-muted"><?= number_format($m['balance_after'], 2) ?></td>
              <td><?= esc($m['remarks']) ?></td>
              <td class="text-center"><?= esc($m['moved_by_name'] ?? '-') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="10" class="text-center text-muted py-4">
              <i class="bx bx-info-circle me-1"></i> No stock movements recorded yet.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<style>
.table th, .table td {
  vertical-align: middle !important;
}
.table th {
  font-size: 0.9rem;
  white-space: nowrap;
}
.badge {
  font-size: 0.75rem;
  letter-spacing: 0.3px;
}
.page-header h3 i {
  vertical-align: middle;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const stockSelect = document.querySelector('select[name="stock_id"]');
  const qtyInfo = document.createElement('small');
  qtyInfo.className = 'text-muted ms-1';
  stockSelect.parentNode.appendChild(qtyInfo);

  stockSelect.addEventListener('change', function() {
    const stockId = this.value;
    if (!stockId) {
      qtyInfo.textContent = '';
      return;
    }
    fetch(`<?= base_url('store/getStockQty/') ?>${stockId}`)
      .then(res => res.json())
      .then(data => {
        if (data && data.qty_available !== undefined) {
          qtyInfo.innerHTML = `<i class="bx bx-package me-1"></i> Available Qty: <strong>${parseFloat(data.qty_available).toFixed(2)}</strong>`;
        } else {
          qtyInfo.textContent = '';
        }
      })
      .catch(() => qtyInfo.textContent = '');
  });
});
</script>


<?= $this->include('layout/footer') ?>
