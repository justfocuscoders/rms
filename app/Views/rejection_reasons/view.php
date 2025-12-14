<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
  <h3>View Rejection Reason</h3>
  <table class="table table-bordered">
    <tr><th>Reason</th><td><?= esc($reason['reason']) ?></td></tr>
    <tr><th>Category</th><td><?= esc($reason['category']) ?></td></tr>
    <tr><th>Status</th><td><?= esc($reason['status']) ?></td></tr>
  </table>
  <a href="<?= base_url('/rejection-reasons/list') ?>" class="btn btn-secondary">Back</a>
</div>
<?= $this->include('layout/footer') ?>
