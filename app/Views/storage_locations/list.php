<?= $this->include('layout/header') ?>

<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Storage Locations</h3>
    <a href="<?= base_url('/storage-locations/add') ?>" class="btn btn-primary">
      + Add Storage Location
    </a>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
      <?= session()->getFlashdata('success') ?>
    </div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <table class="table table-striped mb-0">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Warehouse</th>
            <th>Code</th>
            <th>Name</th>
            <th>Type</th>
            <th>Condition</th>
            <th>Status</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>

        <?php if (!empty($storageLocations)): ?>
          <?php foreach ($storageLocations as $i => $sl): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= esc($sl['location_name'] ?? '-') ?></td>
              <td><?= esc($sl['code']) ?></td>
              <td><?= esc($sl['name']) ?></td>
              <td><?= esc($sl['type']) ?></td>
              <td><?= esc($sl['condition_name'] ?? '-') ?></td>
              <td>
                <?= $sl['status'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?>
              </td>
              <td class="text-end">
                <a href="<?= base_url('/storage-locations/view/' . $sl['id']) ?>" class="btn btn-sm btn-outline-info">View</a>
                <a href="<?= base_url('/storage-locations/edit/' . $sl['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <a href="<?= base_url('/storage-locations/delete/' . $sl['id']) ?>"
                   class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('Delete this storage location?')">
                  Delete
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" class="text-center text-muted">No storage locations found</td>
          </tr>
        <?php endif; ?>

        </tbody>
      </table>
    </div>
  </div>
</div>

<?= $this->include('layout/footer') ?>
