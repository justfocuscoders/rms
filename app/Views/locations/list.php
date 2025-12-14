<?= $this->include('layout/header') ?>

<div class="container-fluid pt-3 pb-5">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Locations</h3>
    <a href="<?= base_url('/locations/form') ?>" class="btn btn-primary">
      Add Location
    </a>
  </div>

  <!-- Flash message -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
      <?= session()->getFlashdata('success') ?>
    </div>
  <?php endif; ?>

  <!-- Search bar -->
  <form method="get" action="<?= base_url('/locations') ?>" class="mb-3 d-flex gap-2">
    <?= csrf_field() ?>
    <input type="text"
           name="search"
           class="form-control"
           placeholder="Search locations..."
           value="<?= esc($search ?? '') ?>">
    <button type="submit" class="btn btn-outline-secondary">Search</button>
    <a href="<?= base_url('/locations') ?>" class="btn btn-outline-danger">Reset</a>
  </form>

  <!-- Table -->
  <table class="table table-bordered table-striped align-middle">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Code</th>
        <th>Name</th>
        <th>Type</th>
        <th>Capacity</th>
        <th>Conditions</th>
        <th>Created At</th>
        <th width="220">Actions</th>
      </tr>
    </thead>

    <tbody>
    <?php if (empty($locations)): ?>
      <tr>
        <td colspan="8" class="text-center text-muted">
          No locations found
        </td>
      </tr>
    <?php else: ?>
      <?php foreach ($locations as $loc): ?>
        <tr>
          <td><?= $loc['id'] ?></td>
          <td><?= esc($loc['code']) ?></td>
          <td><?= esc($loc['name']) ?></td>
          <td><?= esc($loc['type'] ?? '-') ?></td>
          <td><?= esc($loc['capacity'] ?? '-') ?></td>
          <td><?= esc($loc['conditions'] ?? '-') ?></td>
          <td><?= date('d M Y', strtotime($loc['created_at'])) ?></td>
          <td>
            <div class="btn-group">
  <a href="<?= base_url('/locations/view/'.$loc['id']) ?>"
     class="btn btn-sm btn-info">
     View
  </a>

  <a href="<?= base_url('/locations/form/'.$loc['id']) ?>"
     class="btn btn-sm btn-warning">
     Edit
  </a>

  <a href="<?= base_url('/locations/delete/'.$loc['id']) ?>"
     class="btn btn-sm btn-danger"
     onclick="return confirm('Delete this location?')">
     Delete
  </a>
</div>

          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>

</div>

<?= $this->include('layout/footer') ?>
