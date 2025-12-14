<?= $this->include('layout/header') ?>

<div class="container py-4">
  <h3 class="mb-3">Storage Condition Details</h3>

  <div class="card shadow-sm">
    <div class="card-body">

      <table class="table table-bordered">
        <tr>
          <th width="30%">Condition Name</th>
          <td><?= esc($condition['condition_name']) ?></td>
        </tr>

        <tr>
          <th>Description</th>
          <td><?= esc($condition['description'] ?? '-') ?></td>
        </tr>

        <tr>
          <th>Status</th>
          <td>
            <?= $condition['status'] === 'Active'
              ? '<span class="badge bg-success">Active</span>'
              : '<span class="badge bg-secondary">Inactive</span>' ?>
          </td>
        </tr>

        <tr>
          <th>Created At</th>
          <td><?= esc($condition['created_at']) ?></td>
        </tr>
      </table>

      <a href="<?= base_url('/storage-conditions/list') ?>" class="btn btn-secondary">
        Back
      </a>

      <a href="<?= base_url('/storage-conditions/edit/' . $condition['id']) ?>"
         class="btn btn-primary ms-2">
        Edit
      </a>

    </div>
  </div>
</div>

<?= $this->include('layout/footer') ?>
