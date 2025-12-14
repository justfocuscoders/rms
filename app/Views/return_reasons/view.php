<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
  <h3>View Return Reason</h3>
  <table class="table table-bordered">
    <tr><th>Reason</th><td><?= esc($reason['reason']) ?></td></tr>
    <tr><th>Type</th><td><?= esc($reason['type']) ?></td></tr>
    <tr><th>Status</th><td><?= esc($reason['status']) ?></td></tr>
  </table>
  <a href="<?= base_url('/return-reasons/list') ?>" class="btn btn-secondary">Back</a>
</div>
<?= $this->include('layout/footer') ?>
