<?= $this->include('layout/header') ?>
<div class="container-fluid pt-3 pb-5">
<div class="content p-5">
  <h3>Department Details</h3>

  <table class="table table-bordered">
    <tr>
      <th>ID</th>
      <td><?= esc($department['id']) ?></td>
    </tr>
    <tr>
      <th>Name</th>
      <td><?= esc($department['name']) ?></td>
    </tr>
    <tr>
      <th>Created At</th>
      <td><?= esc($department['created_at'] ?? '-') ?></td>
    </tr>
    <tr>
      <th>Updated At</th>
      <td><?= esc($department['updated_at'] ?? '-') ?></td>
    </tr>
  </table>

  <a href="<?= base_url('/departments/form/'.$department['id']) ?>" class="btn btn-warning">Edit</a>
  <a href="<?= base_url('/departments/list') ?>" class="btn btn-secondary">Back</a>
</div>
</div>
<?= $this->include('layout/footer') ?>
