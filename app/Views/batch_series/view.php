<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
  <h3>View Batch Series</h3>
  <table class="table table-bordered">
    <tr><th>Prefix</th><td><?= esc($series['prefix']) ?></td></tr>
    <tr><th>Type</th><td><?= esc($series['type']) ?></td></tr>
    <tr><th>Next Number</th><td><?= esc($series['next_number']) ?></td></tr>
    <tr><th>Format</th><td><?= esc($series['format']) ?></td></tr>
    <tr><th>Status</th><td><?= esc($series['status']) ?></td></tr>
  </table>
  <a href="<?= base_url('/batch-series/list') ?>" class="btn btn-secondary">Back</a>
</div>
<?= $this->include('layout/footer') ?>
