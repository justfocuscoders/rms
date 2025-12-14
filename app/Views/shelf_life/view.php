<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
  <h3>View Shelf Life</h3>
  <table class="table table-bordered">
    <tr><th>Item</th><td><?= esc($record['item_name'] ?? '—') ?></td></tr>
    <tr><th>Shelf Life (Days)</th><td><?= esc($record['shelf_life_days']) ?></td></tr>
    <tr><th>Retest (Days)</th><td><?= esc($record['retest_days']) ?></td></tr>
    <tr><th>Status</th><td><?= esc($record['status']) ?></td></tr>
  </table>
  <a href="<?= base_url('/shelf-life/list') ?>" class="btn btn-secondary">Back</a>
</div>
<?= $this->include('layout/footer') ?>
