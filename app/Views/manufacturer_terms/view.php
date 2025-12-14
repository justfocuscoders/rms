<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
  <h3>View Manufacturer Term</h3>
  <table class="table table-bordered">
    <tr><th>Manufacturer</th><td><?= esc($term['manufacturer_name'] ?? '—') ?></td></tr>
    <tr><th>Term Name</th><td><?= esc($term['term_name']) ?></td></tr>
    <tr><th>Description</th><td><?= esc($term['description']) ?></td></tr>
    <tr><th>Validity (Days)</th><td><?= esc($term['validity_days']) ?></td></tr>
    <tr><th>Status</th><td><?= esc($term['status']) ?></td></tr>
  </table>
  <a href="<?= base_url('/manufacturer-terms/list') ?>" class="btn btn-secondary">Back</a>
</div>
<?= $this->include('layout/footer') ?>
