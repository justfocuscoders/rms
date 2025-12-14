<div class="container-fluid pt-1 pb-5">
<div class="card border-0 shadow-sm rounded-4">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle table-hover">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Item</th>
            <th>Batch</th>
            <th>Expiry</th>
            <th class="text-end">Qty</th>
            <th>UOM</th>
            <th>Location</th>
            <th>Added On</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($stocks)): $i=1; foreach ($stocks as $s): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= esc($s['item_name']) ?></td>
            <td><?= esc($s['batch_no']) ?></td>
            <td><?= esc($s['expiry_date']) ?></td>
            <td class="text-end"><?= number_format($s['qty_available'], 2) ?></td>
            <td><?= esc($s['uom']) ?></td>
            <td><?= esc($s['location_code']) ?> - <?= esc($s['location_name']) ?></td>
            <td><?= date('d M Y', strtotime($s['created_at'])) ?></td>
          </tr>
          <?php endforeach; else: ?>
          <tr>
            <td colspan="8" class="text-center text-muted py-3">
              <i class="bx bx-info-circle me-1"></i> No items in stock.
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>