<div class="card border-0 shadow-sm rounded-4">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle table-hover">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>GRN No</th>
            <th>Item</th>
            <th>Batch No</th>
            <th>Supplier</th>
            <th class="text-end">Qty</th>
            <th>QC Remarks</th>
            <th class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($pendingItems)): $i = 1; foreach ($pendingItems as $p): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= esc($p['grn_no']) ?></td>
            <td><?= esc($p['item_name']) ?></td>
            <td><?= esc($p['batch_no']) ?></td>
            <td><?= esc($p['supplier_name']) ?></td>
            <td class="text-end"><?= number_format($p['approved_qty'], 2) ?></td>
            <td><?= esc($p['remarks']) ?></td>
            <td class="text-center">
              <a href="<?= base_url('/store/accept/'.$p['id']) ?>" class="btn btn-success btn-sm rounded-pill px-3">
                <i class="bx bx-check"></i> Accept
              </a>
              <a href="<?= base_url('/store/reject/'.$p['id']) ?>" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('Reject this item?');">
                <i class="bx bx-x"></i> Reject
              </a>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr>
            <td colspan="8" class="text-center text-muted py-3">
              <i class="bx bx-info-circle me-1"></i> No QC-approved items pending verification.
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
