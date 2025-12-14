<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="card border-0 shadow-sm rounded-3 mb-4">
  <div class="card-body">

    <!-- 🔹 Header Row with Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-semibold mb-0 text-dark">
        <i class="bx bx-file text-primary me-2"></i> Purchase Order Details
      </h5>
      <a href="<?= base_url('purchaseorders') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bx bx-arrow-back"></i> Back
      </a>
    </div>
    <!-- 🔹 End Header Row -->

    <div class="row mb-3">
      <div class="col-md-4"><strong>PO Number:</strong> <?= esc($po['po_number']) ?></div>
      <div class="col-md-4"><strong>Supplier:</strong> <?= esc($po['supplier_name']) ?></div>
      <div class="col-md-4"><strong>Status:</strong>
        <span class="badge bg-<?= match(strtolower($po['status'])) {
          'approved' => 'success',
          'cancelled' => 'danger',
          'pending' => 'warning text-dark',
          default => 'secondary'
        } ?>"><?= ucfirst($po['status']) ?></span>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4"><strong>Order Date:</strong> <?= date('d M Y', strtotime($po['order_date'])) ?></div>
      <div class="col-md-4"><strong>Expected:</strong> <?= $po['expected_date'] ?: '-' ?></div>
      <div class="col-md-4"><strong>Remarks:</strong> <?= esc($po['remarks'] ?: '-') ?></div>
    </div>

    <?php if (in_array(strtolower(session()->get('role')), ['admin', 'procurement'])): ?>
      <div class="text-end mb-3">
        <?php if ($po['status'] !== 'Approved'): ?>
          <button class="btn btn-success me-2" id="approveBtn"><i class="bx bx-check"></i> Approve</button>
        <?php endif; ?>
        <?php if ($po['status'] !== 'Cancelled'): ?>
          <button class="btn btn-danger" id="cancelBtn"><i class="bx bx-x"></i> Cancel</button>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</div>


<div class="card border-0 shadow-sm rounded-3">
  <div class="card-header bg-light fw-semibold">
    <i class="bx bx-list-ul text-primary me-2"></i> Items
  </div>
  <div class="card-body">
    <table class="table table-bordered align-middle">
      <thead class="table-light text-center">
        <tr>
          <th>#</th>
          <th>Item</th>
          <th>Qty</th>
          <th>Rate</th>
          <th>Total</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody class="text-center">
        <?php if (empty($items)): ?>
          <tr><td colspan="6" class="text-muted">No items found.</td></tr>
        <?php else: ?>
          <?php foreach ($items as $i => $it): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= esc($it['item_name']) ?></td>
              <td><?= esc($it['qty_ordered']) ?></td>
              <td>₹<?= number_format($it['unit_price'], 2) ?></td>
              <td>₹<?= number_format($it['qty_ordered'] * $it['unit_price'], 2) ?></td>
              <td><span class="badge bg-secondary"><?= esc($it['status']) ?></span></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const approveBtn = document.getElementById('approveBtn');
  const cancelBtn = document.getElementById('cancelBtn');
  const poId = "<?= $po['id'] ?>";
  const csrfName = "<?= csrf_token() ?>";
  const csrfHash = "<?= csrf_hash() ?>";

  const updateStatus = (status, url) => {
    Swal.fire({
      title: `Confirm ${status}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: `Yes, ${status}`,
      confirmButtonColor: status === 'Approved' ? '#16a34a' : '#dc2626',
    }).then(result => {
      if (result.isConfirmed) {
        fetch(url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            "X-Requested-With": "XMLHttpRequest"
          },
          body: new URLSearchParams({
            [csrfName]: csrfHash
          })
        })
        .then(res => res.json())
        .then(response => {
          if (response.status === 'success') {

            // ✅ Immediately refresh dashboard if it's open in another tab
            try {
              if (window.opener && window.opener.refreshStats) {
                window.opener.refreshStats(); // updates counts instantly
              } else if (window.refreshStats) {
                window.refreshStats(); // if dashboard and PO are same tab
              }
            } catch (e) {
              console.warn('Dashboard refresh skipped:', e);
            }

            Swal.fire({
              icon: 'success',
              title: `Purchase Order ${status}!`,
              timer: 1500,
              showConfirmButton: false
            }).then(() => location.reload());
          } else {
            Swal.fire('Error', response.message || 'Update failed', 'error');
          }
        })
        .catch(err => {
          console.error(err);
          Swal.fire('Error', 'Something went wrong.', 'error');
        });
      }
    });
  };

  approveBtn?.addEventListener('click', () =>
    updateStatus('Approved', "<?= base_url('purchaseorders/approve/'.$po['id']) ?>")
  );
  cancelBtn?.addEventListener('click', () =>
    updateStatus('Cancelled', "<?= base_url('purchaseorders/cancel/'.$po['id']) ?>")
  );
});
</script>



<?= $this->include('layout/footer') ?>
