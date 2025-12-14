<?= $this->include('layout/header') ?>
<?= $this->include('layout/sidebar') ?>

<div class="card shadow">
  <div class="card-header">
    <h4>Inventory Management</h4>
  </div>
  <div class="card-body">
    <p>Manage raw material inventory in various storage locations.</p>
    <ul>
      <li>View current stock levels</li>
      <li>Track material locations (cold storage, ambient, etc.)</li>
      <li>Generate inventory reports</li>
    </ul>
  </div>
</div>

<?= $this->include('layout/footer') ?>
