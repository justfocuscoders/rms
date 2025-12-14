<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
  <h3>View QC Parameter</h3>
  <table class="table table-bordered">
    <tr><th>Name</th><td><?= esc($parameter['name']) ?></td></tr>
    <tr><th>Method</th><td><?= esc($parameter['method']) ?></td></tr>
    <tr><th>Unit</th><td><?= esc($parameter['unit']) ?></td></tr>
    <tr><th>Minimum Limit</th><td><?= esc($parameter['min_limit']) ?></td></tr>
    <tr><th>Maximum Limit</th><td><?= esc($parameter['max_limit']) ?></td></tr>
    <tr><th>Status</th><td><?= esc($parameter['status']) ?></td></tr>
  </table>
  <a href="<?= base_url('/qc-parameters/list') ?>" class="btn btn-secondary">Back</a>
</div>
<?= $this->include('layout/footer') ?>
