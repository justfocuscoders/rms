<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
  <h3>View Manufacturer</h3>
  <table class="table table-bordered">
    <tr><th>Name</th><td><?= esc($manufacturer['name']) ?></td></tr>
    <tr><th>Address</th><td><?= esc($manufacturer['address']) ?></td></tr>
    <tr><th>Contact Person</th><td><?= esc($manufacturer['contact_person']) ?></td></tr>
    <tr><th>Phone</th><td><?= esc($manufacturer['phone']) ?></td></tr>
    <tr><th>Email</th><td><?= esc($manufacturer['email']) ?></td></tr>
    <tr><th>Status</th><td><?= esc($manufacturer['status']) ?></td></tr>
    <tr><th>Created At</th><td><?= esc($manufacturer['created_at']) ?></td></tr>
  </table>
  <a href="<?= base_url('/manufacturers/list') ?>" class="btn btn-secondary">Back</a>
</div>
<?= $this->include('layout/footer') ?>
