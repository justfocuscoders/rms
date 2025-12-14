<?= $this->include('layout/header') ?>

<div class="container-fluid py-3">
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><?= esc($title) ?></h5>

      <?php if (in_array(session()->get('role'), ['admin', 'warehouse'])): ?>
        <a href="<?= site_url('arn/create') ?>" class="btn btn-primary btn-sm">
          <i class="bx bx-plus"></i> Add ARN
        </a>
      <?php endif; ?>
    </div>

    <div class="card-body table-responsive">
      <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>ARN No</th>
            <th>PO No</th>
            <th>Item</th>
            <th>Supplier</th>
            <th>Qty</th>
            <th>Status</th>
            <th width="160">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($arns)): ?>
            <tr>
              <td colspan="7" class="text-center text-muted py-3">
                No ARN records found.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($arns as $a): ?>
              <tr>
                <td><?= esc($a['arn_no']) ?></td>
                <td><?= esc($a['po_no']) ?></td>
                <td><?= esc($a['item_name']) ?></td>
                <td><?= esc($a['supplier_name']) ?></td>
                <td><?= esc($a['received_qty']) . ' ' . esc($a['uom']) ?></td>
                <td>
                  <span class="badge bg-<?= $a['status']=='Pending'?'warning':($a['status']=='Converted'?'success':'danger') ?>">
                    <?= esc($a['status']) ?>
                  </span>
                </td>
                <td>
                  <a href="<?= site_url('arn/view/'.$a['id']) ?>" class="btn btn-outline-info btn-sm">
                    <i class="bx bx-show"></i> View
                  </a>

                  <?php if (in_array(session()->get('role'), ['admin', 'warehouse'])): ?>
                    <a href="<?= site_url('arn/edit/'.$a['id']) ?>" class="btn btn-outline-primary btn-sm">
                      <i class="bx bx-edit"></i>
                    </a>
                    <a href="<?= site_url('arn/delete/'.$a['id']) ?>" 
                       class="btn btn-outline-danger btn-sm" 
                       onclick="return confirm('Delete this ARN?')">
                       <i class="bx bx-trash"></i>
                    </a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?= $this->include('layout/footer') ?>
