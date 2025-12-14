<div class="container-fluid pt-1 pb-5">
<div class="card border-0 shadow-sm rounded-4">
  <div class="card-body">
    <h5 class="fw-semibold mb-3">
      <i class="bx bx-task text-primary me-2"></i>
      QC-Approved Items Awaiting Store Verification
    </h5>

    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light text-center">
          <tr>
            <th>#</th>
            <th>GRN No</th>
            <th>Item Name</th>
            <th>Batch No</th>
            <th>Supplier</th>
            <th class="text-end">Approved Qty</th>
            <th>QC Remarks</th>
            <th>Main Location</th>
            <th>Storage Location</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($verificationItems)): $i = 1; foreach ($verificationItems as $item): ?>
            <tr data-item-id="<?= $item['id'] ?>">
              <td><?= $i++ ?></td>
              <td><strong><?= esc($item['grn_no']) ?></strong></td>
              <td><?= esc($item['item_name']) ?></td>
              <td><?= esc($item['batch_no'] ?? '-') ?></td>
              <td><?= esc($item['supplier_name'] ?? '-') ?></td>
              <td class="text-end fw-semibold"><?= number_format($item['approved_qty'], 2) ?></td>
              <td><?= esc($item['remarks'] ?? '-') ?></td>
              
              <!-- 🔹 Main Location Dropdown -->
              <td>
                <select class="form-select form-select-sm main-location" data-item-id="<?= $item['id'] ?>">
                  <option value="">Select</option>
                  <?php foreach ($locations as $loc): ?>
                    <option value="<?= $loc['id'] ?>"><?= esc($loc['location_name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </td>

              <!-- 🔹 Storage Dropdown (dynamic) -->
              <td>
                <select class="form-select form-select-sm storage-location" data-item-id="<?= $item['id'] ?>">
                  <option value="">Select</option>
                </select>
              </td>

              <!-- 🔹 Action Buttons -->
              <td class="text-center">
                <button class="btn btn-success btn-sm accept-inline-btn">
                  <i class="bx bx-check"></i> Accept
                </button>
                <button class="btn btn-danger btn-sm reject-inline-btn">
                  <i class="bx bx-x"></i> Reject
                </button>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr>
              <td colspan="10" class="text-center text-muted py-4">
                <i class="bx bx-info-circle me-1"></i> No pending verification items.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>
<!-- ⚙️ Inline JS Logic -->
<script>
$(document).ready(function() {
  // 🔁 Load Storage Locations when Main Location changes
  $(document).on('change', '.main-location', function() {
    const row = $(this).closest('tr');
    const locId = $(this).val();
    const storageDropdown = row.find('.storage-location');

    storageDropdown.html('<option value="">Loading...</option>');

    if (!locId) {
      storageDropdown.html('<option value="">Select</option>');
      return;
    }

    $.post('<?= base_url("/warehouse/get_storage_locations") ?>', { location_id: locId }, function(data) {
      let options = '<option value="">Select</option>';
      $.each(data, function(i, s) {
        options += `<option value="${s.id}">${s.storage_name}</option>`;
      });
      storageDropdown.html(options);
    }, 'json');
  });

  // ✅ Accept Item Inline
  $(document).on('click', '.accept-inline-btn', function() {
    const row = $(this).closest('tr');
    const itemId = row.data('item-id');
    const location = row.find('.main-location').val();
    const storage = row.find('.storage-location').val();

    if (!location || !storage) {
      alert('Please select both location and storage.');
      return;
    }

    $.post('<?= base_url("/warehouse/accept") ?>', {
      item_id: itemId,
      location_id: location,
      storage_id: storage
    }, function(resp) {
      if (resp.status === 'success') {
        row.fadeOut(400, function() { $(this).remove(); });
      } else {
        alert(resp.message || 'Error accepting item.');
      }
    }, 'json');
  });

  // ❌ Reject Item Inline
  $(document).on('click', '.reject-inline-btn', function() {
    const row = $(this).closest('tr');
    const itemId = row.data('item-id');
    if (confirm('Reject this item?')) {
      $.post('<?= base_url("/warehouse/reject/" ) ?>' + itemId, function(resp) {
        row.fadeOut(400, function() { $(this).remove(); });
      });
    }
  });
});
</script>

<style>
.table td, .table th { vertical-align: middle; }
.form-select-sm { font-size: 0.82rem; }
.btn-sm { font-size: 0.82rem; }
</style>
