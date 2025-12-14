<?= $this->include('layout/header') ?>

<div class="container-fluid pt-3 pb-5">
  <div class="container py-4 mb-5">

    <h3><?= isset($location['id']) ? 'Edit Location' : 'Add Location' ?></h3>

    <form method="post"
          action="<?= base_url('/locations/save' . (!empty($location['id']) ? '/' . $location['id'] : '')) ?>">

      <?= csrf_field() ?>

      <input type="hidden" name="id" value="<?= $location['id'] ?? '' ?>">

      <!-- Location Code -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Location Code</label>
        <input type="text"
               name="code"
               class="form-control"
               value="<?= esc($location['code'] ?? '') ?>"
               placeholder="WH-MAIN"
               required>
      </div>

      <!-- Location Name -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Location Name</label>
        <input type="text"
               name="name"
               class="form-control"
               value="<?= esc($location['name'] ?? '') ?>"
               placeholder="Main Warehouse"
               required>
      </div>

      <!-- Location Type (Facility Type) -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Location Type</label>
        <select name="type" class="form-select" required>
          <option value="">-- Select Type --</option>

          <?php
          $types = [
              'Warehouse',
              'Manufacturing Plant',
              'Cold Storage Facility',
              'Vendor Location',
              'Distribution Center'
          ];
          foreach ($types as $t):
          ?>
            <option value="<?= $t ?>"
              <?= (($location['type'] ?? '') === $t) ? 'selected' : '' ?>>
              <?= $t ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Capacity -->
      <div class="mb-3">
        <label class="form-label fw-semibold">
          Capacity <small class="text-muted">(Optional – overall capacity)</small>
        </label>
        <input type="number"
               step="0.01"
               name="capacity"
               class="form-control"
               value="<?= esc($location['capacity'] ?? '') ?>"
               placeholder="e.g. 10000">
      </div>

      <!-- Notes / Remarks -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Remarks</label>
        <textarea name="remarks"
                  class="form-control"
                  rows="2"
                  placeholder="Optional notes about this facility"><?= esc($location['remarks'] ?? '') ?></textarea>
      </div>

      <!-- Actions -->
      <div class="mt-4">
        <button type="submit" class="btn btn-success">
          <?= isset($location['id']) ? 'Update Location' : 'Save Location' ?>
        </button>
        <a href="<?= base_url('/locations') ?>" class="btn btn-secondary ms-2">
          Cancel
        </a>
      </div>

    </form>

  </div>
</div>

<?= $this->include('layout/footer') ?>
