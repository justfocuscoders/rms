<?= $this->include('layout/header') ?>

<div class="page-header mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
  <h3 class="fw-semibold mb-0 text-dark">
    <i class="bx bxs-file me-2 text-primary"></i> Material Requisition Slips
  </h3>
  <a href="<?= base_url('mrs/create') ?>" class="btn btn-primary">
    <i class="bx bx-plus me-1"></i> New MRS
  </a>
</div>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
  <ol class="breadcrumb small">
    <?php foreach ($breadcrumb as $b): ?>
      <li class="breadcrumb-item <?= $b['url'] == '' ? 'active' : '' ?>">
        <?php if ($b['url']): ?><a href="<?= $b['url'] ?>"><?= esc($b['title']) ?></a>
        <?php else: ?><?= esc($b['title']) ?><?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ol>
</nav>

<!-- Table -->
<div class="card">
  <div class="card-body table-responsive">
    <table class="table table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>MRS No</th>
          <th>Department</th>
          <th>Requested By</th>
          <th>Date</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($mrs)): ?>
          <tr><td colspan="7" class="text-center text-muted">No records found</td></tr>
        <?php else: foreach ($mrs as $m): ?>
          <tr>
            <td><?= $m['id'] ?></td>
            <td><?= esc($m['mrs_no']) ?></td>
            <td><?= esc($m['department']) ?></td>
            <td><?= esc($m['requested_by_name']) ?></td>
            <td><?= esc($m['mrs_date']) ?></td>
            <td><span class="badge bg-info"><?= esc($m['status']) ?></span></td>
            <td>
              <a href="<?= base_url('mrs/'.$m['id']) ?>" class="btn btn-sm btn-outline-secondary">
                <i class="bx bx-show"></i> View
              </a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->include('layout/footer') ?>
