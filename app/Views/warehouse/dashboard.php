<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="content px-4 pt-3">

  <!-- ✅ Page Header -->
  <div class="page-header mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
    <div>
      <h3 class="fw-semibold mb-0 text-dark">
        <i class="bx bxs-store text-primary me-2"></i> <?= esc($page_title ?? 'Store Dashboard') ?>
      </h3>
      <small class="text-muted"><?= esc($sub_title ?? 'Verification, Inventory & Movement Logs') ?></small>
    </div>
  </div>

  <!-- ✅ Tabs: Verification | Inventory | Movements -->
  <ul class="nav nav-tabs mb-3" id="storeTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active fw-semibold" id="verify-tab" data-bs-toggle="tab" data-bs-target="#verify" type="button" role="tab">
        <i class="bx bx-clipboard-check me-1"></i> Verification
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link fw-semibold" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab">
        <i class="bx bx-box me-1"></i> Inventory
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link fw-semibold" id="movement-tab" data-bs-toggle="tab" data-bs-target="#movement" type="button" role="tab">
        <i class="bx bx-transfer-alt me-1"></i> Movement Logs
      </button>
    </li>
    <li class="nav-item" role="presentation">
  <button class="nav-link fw-semibold d-flex align-items-center" id="expiry-tab" data-bs-toggle="tab" data-bs-target="#expiry" type="button" role="tab">
  <i class="bx bx-time-five me-1"></i> Expiry Alerts
  <?php if (!empty($expiryAlerts)): ?>
    <span class="badge bg-<?= esc($expiryBadgeColor) ?> ms-2">
      <?= count($expiryAlerts) ?>
    </span>
  <?php endif; ?>
</button>
</li>

  </ul>

  <div class="tab-content" id="storeTabsContent">

    <!-- 🧾 TAB 1: Verification -->
    <div class="tab-pane fade show active" id="verify" role="tabpanel">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
          <h5 class="fw-semibold mb-3">
            <i class="bx bx-task text-primary me-2"></i> QC-Approved Items Awaiting Store Verification
          </h5>

          <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>GRN No</th>
                  <th>Item Name</th>
                  <th>Batch No</th>
                  <th>Supplier</th>
                  <th class="text-end">Approved Qty</th>
                  <th>QC Remarks</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($verificationItems)): $i = 1; foreach ($verificationItems as $item): ?>
                  <tr>
                    <td><?= $i++ ?></td>
                    <td><strong><?= esc($item['grn_no']) ?></strong></td>
                    <td><?= esc($item['item_name']) ?></td>
                    <td><?= esc($item['batch_no'] ?? '-') ?></td>
                    <td><?= esc($item['supplier_name'] ?? '-') ?></td>
                    <td class="text-end fw-semibold"><?= number_format($item['approved_qty'], 2) ?></td>
                    <td><?= esc($item['remarks'] ?? '-') ?></td>
                    <td class="text-center">
                      <a href="<?= base_url('/warehouse/accept/' . $item['id']) ?>" class="btn btn-success btn-sm rounded-pill px-3">
                        <i class="bx bx-check"></i> Accept
                      </a>
                      <a href="<?= base_url('/warehouse/reject/' . $item['id']) ?>" 
                         onclick="return confirm('Reject this item?');"
                         class="btn btn-danger btn-sm rounded-pill px-3">
                        <i class="bx bx-x"></i> Reject
                      </a>
                    </td>
                  </tr>
                <?php endforeach; else: ?>
                  <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                      <i class="bx bx-info-circle me-1"></i> No pending verification items.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- 📦 TAB 2: Inventory -->
    <div class="tab-pane fade" id="inventory" role="tabpanel">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
          <h5 class="fw-semibold mb-3"><i class="bx bx-package text-primary me-2"></i> Current Inventory</h5>

          <!-- 🔍 Filters -->
          <form method="get" class="row mb-3">
              <?= csrf_field() ?>
            <div class="col-md-3">
              <label class="form-label fw-semibold text-muted small mb-1">Supplier</label>
              <select name="supplier_id" class="form-select" onchange="this.form.submit()">
                <option value="">All Suppliers</option>
                <?php foreach ($suppliers as $s): ?>
                  <option value="<?= $s['id'] ?>" <?= ($s['id'] == ($selectedSupplier ?? '')) ? 'selected' : '' ?>>
                    <?= esc($s['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label fw-semibold text-muted small mb-1">Item</label>
              <select name="item_id" class="form-select" onchange="this.form.submit()">
                <option value="">All Items</option>
                <?php foreach ($items as $i): ?>
                  <option value="<?= $i['id'] ?>" <?= ($i['id'] == ($selectedItem ?? '')) ? 'selected' : '' ?>>
                    <?= esc($i['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
              <a href="<?= base_url('store?tab=inventory') ?>" class="btn btn-outline-secondary w-100">
                <i class="bx bx-reset"></i> Reset
              </a>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Item Name</th>
                  <th>Batch No</th>
                  <th>Expiry Date</th>
                  <th>Qty Available</th>
                  <th>UOM</th>
                  <th>Location</th>
                  <th>Added On</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($inventoryItems)): $i = 1; foreach ($inventoryItems as $stock): ?>
                  <tr>
                    <td><?= $i++ ?></td>
                    <td><?= esc($stock['item_name']) ?></td>
                    <td><?= esc($stock['batch_no'] ?? '-') ?></td>
                    <td><?= esc($stock['expiry_date'] ?? '-') ?></td>
                    <td class="fw-semibold text-end"><?= number_format($stock['qty_available'], 2) ?></td>
                    <td><?= esc($stock['uom'] ?? '-') ?></td>
                    <td><?= esc($stock['location_name'] ?? '-') ?></td>
                    <td><?= date('d M Y', strtotime($stock['created_at'])) ?></td>
                    <td class="text-center">
                      <a href="<?= base_url('warehouse/view/'.$stock['id'].'?from=inventory') ?>" class="btn btn-sm btn-info">
                        <i class="bx bx-show"></i> View
                      </a>
                    </td>
                  </tr>
                <?php endforeach; else: ?>
                  <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                      <i class="bx bx-info-circle me-1"></i> No stock available in inventory.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- 🔄 TAB 3: Stock Movement Logs -->
    <div class="tab-pane fade" id="movement" role="tabpanel">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-semibold mb-0 text-dark">
              <i class="bx bx-transfer-alt text-primary me-2"></i> Stock Movement Logs
            </h5>
            <div>
              <button type="button" class="btn btn-sm btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addMovementModal">
                <i class="bx bx-plus"></i> Add Movement
              </button>
              <a href="<?= base_url('stock-movements/export') ?>" class="btn btn-sm btn-outline-success">
                <i class="bx bx-download"></i> Export CSV
              </a>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
              <thead class="table-light">
                <tr class="text-center">
                  <th>#</th>
                  <th>Date / Time</th>
                  <th>Item</th>
                  <th>Batch</th>
                  <th>Movement</th>
                  <th>Qty</th>
                  <th>Balance After</th>
                  <th>Remarks</th>
                  <th>By</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($movements)): $i = 1; foreach ($movements as $m): ?>
                  <tr>
                    <td><?= $i++ ?></td>
                    <td><?= date('d M Y, h:i A', strtotime($m['moved_at'])) ?></td>
                    <td><?= esc($m['item_name']) ?></td>
                    <td><?= esc($m['batch_no'] ?? '-') ?></td>
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
                <?php endforeach; else: ?>
                  <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                      <i class="bx bx-info-circle me-1"></i> No stock movements recorded yet.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    
    <!-- ⏳ TAB 4: Expiry Alerts -->
<div class="tab-pane fade" id="expiry" role="tabpanel">
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
      <h5 class="fw-semibold mb-3 text-warning">
        <i class="bx bx-time-five me-2"></i> Items Nearing Expiry (Next 60 Days)
      </h5>

      <?php if (!empty($expiryAlerts)): ?>
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Item Name</th>
                <th>Batch No</th>
                <th>Expiry Date</th>
                <th>Days Left</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($expiryAlerts as $i => $alert): ?>
                <?php
                  $daysLeft = (int)$alert['days_left'];
                  $colorClass = $daysLeft <= 10 
                    ? 'text-danger fw-semibold'
                    : ($daysLeft <= 30 ? 'text-warning fw-semibold' : 'text-success fw-semibold');
                ?>
                <tr>
                  <td><?= $i + 1 ?></td>
                  <td><?= esc($alert['item_name']) ?></td>
                  <td><?= esc($alert['batch_no'] ?? '-') ?></td>
                  <td><?= date('d M Y', strtotime($alert['expiry_date'])) ?></td>
                  <td class="<?= $colorClass ?>"><?= $daysLeft ?> days</td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="alert alert-success shadow-sm rounded-3">
          <i class="bx bx-check-circle"></i> No items nearing expiry within 60 days.
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>


    <!-- ➕ Add Movement Modal -->
    <div class="modal fade" id="addMovementModal" tabindex="-1" aria-labelledby="addMovementLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
          <form action="<?= base_url('warehouse/addMovement') ?>" method="post">
            <div class="modal-header bg-light border-0">
              <h5 class="modal-title fw-semibold text-dark" id="addMovementLabel">
                <i class="bx bx-plus-circle text-primary me-2"></i> Add Stock Movement
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
              <!-- Select Item -->
              <div class="mb-3">
                <label class="form-label">Select Item / Batch</label>
                <select name="stock_id" class="form-select" required>
                  <option value="">Choose...</option>
                  <?php foreach ($inventoryItems as $st): ?>
                    <option value="<?= $st['id'] ?>">
                      <?= esc($st['item_name']) ?> (Batch <?= esc($st['batch_no'] ?? '-') ?>)
                      — <?= number_format($st['qty_available'], 2) ?> <?= esc($st['uom'] ?? '') ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Movement Type -->
              <div class="mb-3">
                <label class="form-label">Movement Type</label>
                <select name="movement_type" class="form-select" required>
                  <option value="OUT">OUT — Issue / Consume</option>
                  <option value="ADJUSTMENT">ADJUSTMENT — Manual correction</option>
                </select>
              </div>

              <!-- Quantity -->
              <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" step="0.01" name="qty" class="form-control" placeholder="Enter quantity" required>
              </div>

              <!-- Remarks -->
              <div class="mb-3">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="2" placeholder="Enter remarks (optional)"></textarea>
              </div>
            </div>

            <div class="modal-footer border-0">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">
                <i class="bx bx-save"></i> Save Movement
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div> <!-- /tab-content -->

</div>

</div>

<style>
  .table td, .table th { vertical-align: middle; }
  .btn-sm { font-size: 0.82rem; }
  .nav-tabs .nav-link { border-radius: 0.5rem 0.5rem 0 0; }
  .card { border-radius: 1rem; }

  /* Expiry Alerts */
  .alert ul {
    list-style: none;
    padding-left: 1rem;
  }
  .alert-warning {
    background-color: #fffbea;
    border-left: 5px solid #ffc107;
  }

  /* Filter Form */
  form.row select.form-select {
    font-size: 0.85rem;
  }
  form.row label {
    font-size: 0.75rem;
  }

  .badge {
    font-size: 0.75rem;
  }
  
  .text-danger { color: #dc3545 !important; }
.text-warning { color: #ffc107 !important; }
.text-success { color: #198754 !important; }
.table td, .table th { vertical-align: middle; }

.nav-tabs .badge {
  font-size: 0.7rem;
  padding: 0.25em 0.5em;
  border-radius: 1rem;
</style>

<script>
// 🔄 Remember last active tab on Store Dashboard
document.addEventListener("DOMContentLoaded", function () {
  const tabKey = "storeActiveTab";

  // Restore last active tab from localStorage
  const lastTab = localStorage.getItem(tabKey);
  if (lastTab) {
    const tabTriggerEl = document.querySelector(`button[data-bs-target="${lastTab}"]`);
    if (tabTriggerEl) {
      const tab = new bootstrap.Tab(tabTriggerEl);
      tab.show();
    }
  }

  // Save active tab whenever user clicks a tab
  const tabButtons = document.querySelectorAll('#storeTabs button[data-bs-toggle="tab"]');
  tabButtons.forEach(btn => {
    btn.addEventListener('shown.bs.tab', function (event) {
      const activeTab = event.target.getAttribute('data-bs-target');
      localStorage.setItem(tabKey, activeTab);
    });
  });
});
</script>

<?= $this->include('layout/footer') ?>