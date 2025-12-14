<?= $this->include('layout/header') ?>
<?= $this->include('layout/sidebar') ?>

<div class="card shadow">
  <div class="card-header">
    <h4>GRN (Goods Receipt Note)</h4>
  </div>
  <div class="card-body">
    <p>Welcome to the GRN module. Here you can manage incoming raw materials and verify supplier receipts.</p>
    <ul>
      <li>Create new GRN entries</li>
      <li>Track supplier receipts</li>
      <li>View past GRN history</li>
    </ul>
  </div>
</div>

<?= $this->include('layout/footer') ?>