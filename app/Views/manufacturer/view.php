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
    <h3>Manufacturer Details</h3>
    <a href="<?= base_url('/manufacturer/list') ?>" class="btn btn-secondary">Back</a>
  </div>

  <!-- Details Card -->
  <div class="card shadow-sm p-4">
    <div class="row mb-2">
      <div class="col-md-6"><strong>Name:</strong> <?= esc($manufacturer['name']) ?></div>
      <div class="col-md-6"><strong>Contact Person:</strong> <?= esc($manufacturer['contact_person'] ?? '-') ?></div>
    </div>
    <div class="row mb-2">
      <div class="col-md-6"><strong>Phone:</strong> <?= esc($manufacturer['phone'] ?? '-') ?></div>
      <div class="col-md-6"><strong>Email:</strong> <?= esc($manufacturer['email'] ?? '-') ?></div>
    </div>
    <div class="row mb-2">
      <div class="col-md-12"><strong>Address:</strong> <?= esc($manufacturer['address'] ?? '-') ?></div>
    </div>
    <div class="row mb-2">
      <div class="col-md-6"><strong>Status:</strong>
        <?php if ($manufacturer['status'] === 'Active'): ?>
          <span class="badge bg-success">Active</span>
        <?php else: ?>
          <span class="badge bg-secondary">Inactive</span>
        <?php endif; ?>
      </div>
      <div class="col-md-6"><strong>Created At:</strong> <?= date('d-M-Y', strtotime($manufacturer['created_at'])) ?></div>
    </div>
  </div>
</div>

<?= $this->include('layout/footer') ?>
