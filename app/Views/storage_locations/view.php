<?= $this->include('layout/header') ?>

<div class="container py-4">
  <h3 class="mb-3">Storage Location Details</h3>

  <div class="card shadow-sm">
    <div class="card-body">

      <table class="table table-bordered">
        <tr>
          <th width="30%">Warehouse</th>
          <td><?= esc($storageLocation['location_name']) ?></td>
        </tr>
        <tr>
          <th>Storage Location Code</th>
          <td><?= esc($storageLocation['code']) ?></td>
        </tr>
        <tr>
          <th>Storage Location Name</th>
          <td><?= esc($storageLocation['name']) ?></td>
        </tr>
        <tr>
          <th>Type</th>
          <td><?= esc($storageLocation['type']) ?></td>
        </tr>
        <tr>
          <th>Storage Condition</th>
          <td><?= esc($storageLocation['condition_name']) ?></td>
        </tr>
        <tr>
          <th>Capacity</th>
          <td><?= esc($storageLocation['capacity'] ?? '-') ?></td>
        </tr>
        <tr>
          <th>Description</th>
          <td><?= esc($storageLocation['description'] ?? '-') ?></td>
        </tr>
        <tr>
          <th>Status</th>
          <td><?= $storageLocation['status'] ? 'Active' : 'Inactive' ?></td>
        </tr>
        <tr>
          <th>Created At</th>
          <td><?= esc($storageLocation['created_at']) ?></td>
        </tr>
      </table>

      <a href="<?= base_url('/storage-locations/list') ?>" class="btn btn-secondary">
        Back
      </a>
      <a href="<?= base_url('/storage-locations/edit/' . $storageLocation['id']) ?>" class="btn btn-primary ms-2">
        Edit
      </a>

    </div>
  </div>
</div>

<?= $this->include('layout/footer') ?>
