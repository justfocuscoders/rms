<?= $this->include('layout/header') ?>

<div class="container py-4">
  <h3><?= isset($storageLocation) ? 'Edit Storage Location' : 'Add Storage Location' ?></h3>

  <form method="post" action="<?= base_url('/storage-locations/save' . (!empty($storageLocation['id']) ? '/' . $storageLocation['id'] : '')) ?>">

    <?= csrf_field() ?>

    <!-- Warehouse -->
    <div class="mb-3">
      <label>Warehouse</label>
      <select name="location_id" class="form-control" required>
        <option value="">Select Warehouse</option>
        <?php foreach ($locations as $l): ?>
          <option value="<?= $l['id'] ?>"
            <?= (($storageLocation['location_id'] ?? '') == $l['id']) ? 'selected' : '' ?>>
            <?= $l['code'] ?> - <?= $l['name'] ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Storage Location Name -->
    <div class="mb-3">
      <label>Storage Location Name</label>
      <input type="text" name="name" class="form-control"
             value="<?= esc($storageLocation['name'] ?? '') ?>" required>
    </div>

    <!-- Storage Location Code -->
    <div class="mb-3">
      <label>Storage Location Code</label>
      <input type="text" name="code" class="form-control"
             value="<?= esc($storageLocation['code'] ?? '') ?>" required>
    </div>

    <!-- Type -->
    <div class="mb-3">
      <label>Storage Location Type</label>
      <select name="type" class="form-control" required>
        <option value="">Select Type</option>
        <?php
        $types = ['Rack','Shelf','Cold Room','Quarantine','Finished Goods'];
        foreach ($types as $t):
        ?>
          <option value="<?= $t ?>"
            <?= (($storageLocation['type'] ?? '') === $t) ? 'selected' : '' ?>>
            <?= $t ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Condition -->
    <div class="mb-3">
      <label>Storage Condition</label>
      <select name="storage_condition_id" class="form-control" required>
        <option value="">Select Condition</option>
        <?php foreach ($conditions as $c): ?>
          <option value="<?= $c['id'] ?>"
            <?= (($storageLocation['storage_condition_id'] ?? '') == $c['id']) ? 'selected' : '' ?>>
            <?= $c['condition_name'] ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Capacity -->
    <div class="mb-3">
      <label>Capacity</label>
      <input type="number" step="0.01" name="capacity"
             class="form-control"
             value="<?= esc($storageLocation['capacity'] ?? '') ?>">
    </div>

    <!-- Description -->
    <div class="mb-3">
      <label>Description</label>
      <textarea name="description" class="form-control"><?= esc($storageLocation['description'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="btn btn-success">Save</button>
    <a href="<?= base_url('/storage-locations/list') ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?= $this->include('layout/footer') ?>
