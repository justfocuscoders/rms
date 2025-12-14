<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="page-header mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
  <h3 class="fw-semibold mb-0 text-dark">
    <i class="bx bxs-test-tube text-primary me-2"></i> Quality Control
  </h3>
  <div>
    <a href="<?= base_url('/qc/dashboard') ?>" class="btn btn-sm btn-outline-primary me-2">
      <i class="bx bx-pie-chart-alt"></i> Dashboard / Summary
    </a>
    <a href="<?= base_url('/grn/list') ?>" class="btn btn-sm btn-success">
      <i class="bx bx-plus"></i> New QC Entry
    </a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header bg-light">
    <h5 class="mb-0 text-primary"><i class="bx bx-list-ul me-2"></i> All QC Results</h5>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>GRN</th>
            <th>Item</th>
            <th>Qty Received</th>
            <th>Status</th>
            <th>Remarks</th>
            <th>Tested By</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($qc_results)): ?>
            <?php foreach ($qc_results as $i => $r): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td>GRN-<?= esc($r['grn_id']) ?></td>
                <td><?= esc($r['item_name'] ?? 'Unknown') ?></td>
                <td><?= esc($r['qty_received']) ?></td>
                <td>
                  <span class="badge 
                    <?= $r['qc_status'] == 'Accepted' ? 'bg-success' : 
                        ($r['qc_status'] == 'Rejected' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                    <?= esc($r['qc_status']) ?>
                  </span>
                </td>
                <td><?= esc($r['remarks']) ?></td>
                <td><?= esc($r['tested_by']) ?></td>
                <td><?= date('d M Y', strtotime($r['tested_at'])) ?></td>
                <td>
                  <a href="<?= base_url('/qc/view/'.$r['id']) ?>" class="btn btn-sm btn-outline-info" title="View Details">
                    <i class="bx bx-show"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="9" class="text-center text-muted">No QC results found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>
<?= $this->include('layout/footer') ?>
