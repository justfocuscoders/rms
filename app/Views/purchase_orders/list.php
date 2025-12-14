<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="page-header d-flex justify-content-between align-items-center mb-4">
  <div>
    <h3 class="fw-semibold mb-0 text-dark">
      <i class="bx bx-cart text-primary me-2"></i> Purchase Orders
    </h3>
    <small class="text-muted">Manage and track all purchase orders</small>
  </div>

  <?php if (in_array(strtolower(session()->get('role')), ['admin', 'procurement'])): ?>
    <a href="<?= base_url('/purchaseorders/form') ?>" class="btn btn-primary">
      <i class="bx bx-plus"></i> Add Purchase Order
    </a>
  <?php endif; ?>
</div>

<div class="card border-0 shadow-sm rounded-3">
  <div class="card-body">

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-light text-center">
          <tr>
            <th width="5%">#</th>
            <th>PO Number</th>
            <th>Supplier</th>
            <th>Order Date</th>
            <th>Expected Date</th>
            <th>Status</th>
            <th width="18%">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($purchase_orders)): ?>
            <?php $i = 1; foreach ($purchase_orders as $po): ?>
              <tr>
                <td class="text-center"><?= $i++ ?></td>
                <td><?= esc($po['po_number']) ?></td>
                <td><?= esc($po['supplier_name'] ?? '-') ?></td>
                <td><?= date('d M Y', strtotime($po['order_date'])) ?></td>
                <td><?= $po['expected_date'] ? date('d M Y', strtotime($po['expected_date'])) : '-' ?></td>
                <td class="text-center">
                  <?php
                    $status = ucfirst($po['status']);
                    $badgeClass = match ($po['status']) {
                      'Pending' => 'bg-warning text-dark',
                      'Approved' => 'bg-success',
                      'Cancelled' => 'bg-danger',
                      default => 'bg-secondary',
                    };
                  ?>
                  <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                </td>
                <td class="text-center">
                  <a href="<?= base_url('purchaseorders/view/' . $po['id']) ?>" class="btn btn-outline-info btn-sm" title="View">
                    <i class="bx bx-show"></i>
                  </a>
                  <?php if (in_array(strtolower(session()->get('role')), ['admin', 'procurement'])): ?>
                    <a href="<?= base_url('purchaseorders/form/' . $po['id']) ?>" class="btn btn-outline-primary btn-sm" title="Edit">
                      <i class="bx bx-edit"></i>
                    </a>
                    <a href="<?= base_url('purchaseorders/delete/' . $po['id']) ?>" class="btn btn-outline-danger btn-sm"
                       onclick="return confirm('Are you sure you want to delete this Purchase Order?')" title="Delete">
                      <i class="bx bx-trash"></i>
                    </a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center text-muted py-3">No purchase orders found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>
<?= $this->include('layout/footer') ?>
