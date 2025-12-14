<?= $this->include('layout/header') ?>

<div class="container-fluid pt-1 pb-5">
  <div class="content-wrapper">
    <div class="container mt-4">
      <h2>View Supplier</h2>
      <table class="table table-bordered">
        <tr><th>ID</th><td><?= $supplier['id'] ?></td></tr>
        <tr><th>Name</th><td><?= $supplier['name'] ?></td></tr>
        <tr><th>Contact Person</th><td><?= $supplier['contact_person'] ?></td></tr>
        <tr><th>Phone</th><td><?= $supplier['phone'] ?></td></tr>
        <tr><th>Email</th><td><?= $supplier['email'] ?></td></tr>
        <tr><th>Address</th><td><?= $supplier['address'] ?></td></tr>

        <!-- 🆕 Optional new fields -->
        <?php if(!empty($supplier['gst_number'])): ?>
          <tr><th>GST / Tax Number</th><td><?= $supplier['gst_number'] ?></td></tr>
        <?php endif; ?>

        <?php if(isset($supplier['status'])): ?>
          <tr><th>Status</th>
            <td>
              <?= $supplier['status'] == 1
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-secondary">Inactive</span>'; ?>
            </td>
          </tr>
        <?php endif; ?>

        <?php if(!empty($supplier['remarks'])): ?>
          <tr><th>Remarks</th><td><?= $supplier['remarks'] ?></td></tr>
        <?php endif; ?>

        <!-- 🆕 Created/Updated By (if you join with users table) -->
        <?php if(!empty($supplier['created_by_name'])): ?>
          <tr><th>Created By</th><td><?= $supplier['created_by_name'] ?></td></tr>
        <?php endif; ?>
        <?php if(!empty($supplier['updated_by_name'])): ?>
          <tr><th>Last Updated By</th><td><?= $supplier['updated_by_name'] ?></td></tr>
        <?php endif; ?>

        <tr><th>Created At</th><td><?= $supplier['created_at'] ?? '-' ?></td></tr>
        <tr><th>Updated At</th><td><?= $supplier['updated_at'] ?? '-' ?></td></tr>
      </table>
      <a href="<?= site_url('suppliers') ?>" class="btn btn-secondary">Back</a>
    </div>
  </div>
</div>

<?= $this->include('layout/footer') ?>
