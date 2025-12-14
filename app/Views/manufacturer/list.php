<?= $this->include('layout/header') ?>

<div class="container-fluid pt-1 pb-5">

  <!-- ✅ Breadcrumb -->
  <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb">
        <?php foreach ($breadcrumbs as $b): ?>
          <?php if (!empty($b['url'])): ?>
            <li class="breadcrumb-item">
              <a href="<?= base_url($b['url']) ?>"><?= esc($b['title']) ?></a>
            </li>
          <?php else: ?>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($b['title']) ?></li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ol>
    </nav>
  <?php endif; ?>

  <!-- Title + Add Button -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Manufacturers</h3>
    <a href="<?= base_url('/manufacturer/form') ?>" class="btn btn-primary">Add Manufacturer</a>
  </div>

  <!-- Flash Message -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php elseif (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <!-- Search -->
  <form method="get" action="<?= base_url('/manufacturer/list') ?>" class="mb-3 d-flex gap-2">
    <input type="text" name="search" class="form-control" placeholder="Search manufacturer..."
           value="<?= esc($search) ?>">
    <button type="submit" class="btn btn-outline-secondary">Search</button>
    <a href="<?= base_url('/manufacturer/list') ?>" class="btn btn-outline-danger">Reset</a>
  </form>

  <!-- Table -->
  <table class="table table-bordered table-striped align-middle">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Contact Person</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Status</th>
        <th>Created At</th>
        <th width="180">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($manufacturers)): ?>
        <tr><td colspan="8" class="text-center text-muted">No manufacturers found</td></tr>
      <?php else: ?>
        <?php foreach ($manufacturers as $m): ?>
          <tr>
            <td><?= $m['id'] ?></td>
            <td><?= esc($m['name']) ?></td>
            <td><?= esc($m['contact_person'] ?? '-') ?></td>
            <td><?= esc($m['phone'] ?? '-') ?></td>
            <td><?= esc($m['email'] ?? '-') ?></td>
            <td>
              <?php if ($m['status'] === 'Active'): ?>
                <span class="badge bg-success">Active</span>
              <?php else: ?>
                <span class="badge bg-secondary">Inactive</span>
              <?php endif; ?>
            </td>
            <td><?= date('d-M-Y', strtotime($m['created_at'])) ?></td>
            <td>
              <div class="btn-group">
                <a href="<?= base_url('/manufacturer/view/'.$m['id']) ?>" class="btn btn-sm btn-info">View</a>
                <a href="<?= base_url('/manufacturer/form/'.$m['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="<?= base_url('/manufacturer/delete/'.$m['id']) ?>" class="btn btn-sm btn-danger"
                   onclick="return confirm('Delete this manufacturer?')">Delete</a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <div class="mt-3"><?= $pager->links() ?></div>

</div>

<?= $this->include('layout/footer') ?>
