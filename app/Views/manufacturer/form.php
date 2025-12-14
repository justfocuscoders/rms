<?= $this->include('layout/header') ?>

<div class="container-fluid pt-1 pb-5">

  <!-- ✅ Breadcrumb -->
  <?php if (isset($breadcrumbs)): ?>
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb">
        <?php foreach ($breadcrumbs as $b): ?>
          <?php if (!empty($b['url'])): ?>
            <li class="breadcrumb-item"><a href="<?= base_url($b['url']) ?>"><?= esc($b['title']) ?></a></li>
          <?php else: ?>
            <li class="breadcrumb-item active"><?= esc($b['title']) ?></li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ol>
    </nav>
  <?php endif; ?>

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?= isset($manufacturer) ? 'Edit Manufacturer' : 'Add Manufacturer' ?></h3>
    <a href="<?= base_url('/manufacturer/list') ?>" class="btn btn-secondary">Back</a>
  </div>

  <!-- Flash -->
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <!-- Form -->
  <form method="post" action="<?= base_url(isset($manufacturer) ? '/manufacturer/save/'.$manufacturer['id'] : '/manufacturer/save') ?>"
        class="card p-4 shadow-sm">
    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Name *</label>
        <input type="text" name="name" class="form-control" required
               value="<?= esc($manufacturer['name'] ?? '') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Contact Person</label>
        <input type="text" name="contact_person" class="form-control"
               value="<?= esc($manufacturer['contact_person'] ?? '') ?>">
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control"
               value="<?= esc($manufacturer['phone'] ?? '') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control"
               value="<?= esc($manufacturer['email'] ?? '') ?>">
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Address</label>
      <textarea name="address" class="form-control" rows="2"><?= esc($manufacturer['address'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="Active" <?= isset($manufacturer) && $manufacturer['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
        <option value="Inactive" <?= isset($manufacturer) && $manufacturer['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
      </select>
    </div>

    <button type="submit" class="btn btn-success px-4">Save</button>
  </form>

</div>

<?= $this->include('layout/footer') ?>
