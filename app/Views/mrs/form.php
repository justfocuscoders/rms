<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">
<div class="content p-4">
  <div class="page-header mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
    <h3 class="fw-semibold mb-0 text-dark">
      <i class="bx bx-file text-primary me-2"></i>
      <?= isset($mrs) ? 'Edit Material Requisition Slip' : 'Create Material Requisition Slip' ?>
    </h3>
    <a href="<?= base_url('mrs/list') ?>" class="btn btn-sm btn-outline-secondary">
      <i class="bx bx-arrow-back"></i> Back
    </a>
  </div>

  <form action="<?= base_url('mrs/save') ?>" method="post" class="card shadow-sm p-4">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $mrs['id'] ?? '' ?>">

    <!-- ===========================
          MRS HEADER SECTION
    ============================ -->
    <div class="row g-3 mb-4">
      <!-- MRS No -->
<div class="col-md-3">
  <label class="form-label">MRS No</label>
  <input type="text" class="form-control bg-light"
         name="mrs_no"
         value="<?= esc($mrs['mrs_no'] ?? $auto_no ?? 'Auto-generated') ?>" readonly>  <!-- 💡 -->
</div>

      <!-- MRS Date -->
      <div class="col-md-3">
        <label class="form-label">MRS Date <span class="text-danger">*</span></label>
        <input type="date" name="mrs_date" class="form-control"
               value="<?= esc($mrs['mrs_date'] ?? date('Y-m-d')) ?>" required>
      </div>

      <!-- Department -->
      <div class="col-md-3">
        <label class="form-label">Department</label>
        <input type="hidden" name="department_id"
               value="<?= esc($default_department_id ?? $mrs['department_id'] ?? '') ?>">
        <input type="text" class="form-control bg-light" value="Production" readonly>
      </div>

      <!-- Requested By -->
      <div class="col-md-3">
        <label class="form-label">Requested By</label>
        <input type="hidden" name="requested_by" value="<?= session()->get('id') ?>">
        <input type="text" class="form-control bg-light"
               value="<?= esc(session()->get('name')) ?>" readonly>
      </div>

      <!-- Remarks -->
      <div class="col-md-12 mt-3">
        <label class="form-label">Remarks / Purpose</label>
        <textarea name="remarks" class="form-control" rows="2"
                  placeholder="Reason for requesting materials..."><?= esc($mrs['remarks'] ?? '') ?></textarea>
      </div>
    </div>

    <!-- ===========================
          MRS ITEM DETAILS SECTION
    ============================ -->
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="fw-semibold mb-0">
        <i class="bx bx-list-ul text-primary me-1"></i> Item Details
      </h5>
      <button type="button" id="addRow" class="btn btn-sm btn-outline-primary">
        <i class="bx bx-plus"></i> Add Item
      </button>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered align-middle" id="mrsItemsTable">
        <thead class="table-light">
          <tr>
            <th style="width:30%">Item</th>
            <th style="width:15%">Qty Requested</th>
            <th style="width:10%">UOM</th>
            <th style="width:20%">Batch</th>
            <th style="width:20%">Remarks</th>
            <th style="width:5%">Action</th>
          </tr>
        </thead>

        <tbody>
          <?php if (!empty($details)): ?>
            <?php foreach ($details as $detail): ?>
              <tr>
                <!-- ITEM -->
                <td>
                  <select name="item_id[]" class="form-select item-select" required>
                    <option value="">Select Item</option>
                    <?php foreach ($items as $item): ?>
                      <option 
                        value="<?= $item['id'] ?>" 
                        data-uom="<?= esc($item['uom'] ?? '') ?>"
                        <?= ($item['id'] == $detail['item_id']) ? 'selected' : '' ?>>
                        <?= esc($item['name']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </td>

                <!-- QTY -->
                <td>
                  <input type="number" step="0.01" name="qty_requested[]" 
                         value="<?= esc($detail['qty_requested']) ?>" class="form-control" required>
                </td>

                <!-- UOM -->
                <td>
                  <input type="text" name="uom[]" value="<?= esc($detail['uom']) ?>" 
                         class="form-control uom" readonly>
                </td>

                <!-- BATCH -->
                <td>
                  <select name="batch_no[]" class="form-select batch-select">
                    <option value="">Select Batch</option>
                    <?php
                      $itemId = $detail['item_id'];
                      if (!empty($batchMap[$itemId])) {
                        foreach ($batchMap[$itemId] as $b) {
                          $selected = ($detail['batch_no'] ?? '') === $b['batch_no'] ? 'selected' : '';
                          echo '<option value="' . esc($b['batch_no']) . '" ' . $selected . '>';
                          echo esc($b['batch_no']);
                          echo '</option>';
                        }
                      }
                    ?>
                  </select>
                </td>

                <!-- REMARKS -->
                <td>
                  <input type="text" name="item_remarks[]" 
                         value="<?= esc($detail['remarks']) ?>" class="form-control">
                </td>

                <!-- ACTION -->
                <td class="text-center">
                  <button type="button" class="btn btn-sm btn-danger remove-row">
                    <i class="bx bx-trash"></i>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <!-- Blank row for new form -->
            <tr>
              <td>
                <select name="item_id[]" class="form-select item-select" required>
                  <option value="">Select Item</option>
                  <?php foreach ($items as $item): ?>
                    <option value="<?= $item['id'] ?>" data-uom="<?= esc($item['uom'] ?? '') ?>">
                      <?= esc($item['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td><input type="number" step="0.01" name="qty_requested[]" class="form-control" required></td>
              <td><input type="text" name="uom[]" class="form-control uom" readonly></td>
              <td>
                <select name="batch_no[]" class="form-select batch-select">
                  <option value="">Select Batch</option>
                </select>
              </td>
              <td><input type="text" name="item_remarks[]" class="form-control"></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-row"><i class="bx bx-trash"></i></button>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- ===========================
          SAVE BUTTONS
    ============================ -->
    <div class="text-end mt-4">
      <button type="submit" class="btn btn-success">
        <i class="bx bx-save"></i> <?= isset($mrs) ? 'Update MRS' : 'Submit MRS' ?>
      </button>
      <a href="<?= base_url('mrs/list') ?>" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
</div>
<!-- ===========================
      JS SCRIPT SECTION
============================ -->
<script>
/* =============================
   🧩 1. Batch Map (from PHP)
============================= */
const batchData = <?= json_encode($batchMap ?? []) ?>;
console.log("✅ Batch Map Loaded:", batchData);

/* =============================
   ⚙️ 2. Auto-fill + Batch Filter
============================= */
document.addEventListener('change', (e) => {
  if (e.target.classList.contains('item-select')) {
    const tr = e.target.closest('tr');
    const selected = e.target.selectedOptions[0];
    const uom = selected?.dataset?.uom || '';
    tr.querySelector('.uom').value = uom;

    const itemId = e.target.value;
    const batchSelect = tr.querySelector('.batch-select');
    batchSelect.innerHTML = '<option value="">Select Batch</option>';

    if (batchData[itemId] && batchData[itemId].length > 0) {
      batchData[itemId].forEach(batch => {
        const opt = document.createElement('option');
        opt.value = batch.batch_no;
        opt.textContent = batch.batch_no;
        batchSelect.appendChild(opt);
      });
    } else {
      const opt = document.createElement('option');
      opt.disabled = true;
      opt.textContent = 'No batches found';
      batchSelect.appendChild(opt);
    }
  }
});

/* =============================
   ➕ 3. Add Row
============================= */
document.getElementById('addRow').addEventListener('click', () => {
  const tbody = document.querySelector('#mrsItemsTable tbody');
  const firstRow = tbody.querySelector('tr');
  const tr = firstRow.cloneNode(true);
  tr.querySelectorAll('input').forEach(i => i.value = '');
  tr.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
  tbody.appendChild(tr);
});

/* =============================
   ❌ 4. Remove Row
============================= */
document.addEventListener('click', (e) => {
  if (e.target.closest('.remove-row')) {
    const rows = document.querySelectorAll('#mrsItemsTable tbody tr');
    if (rows.length > 1) e.target.closest('tr').remove();
  }
});
</script>

<style>
  #mrsItemsTable input, #mrsItemsTable select {
    min-width: 80px;
  }
  hr { border-top: 1px dashed #ccc; }
  /* Fix invisible dropdown text */
  #mrsItemsTable select option, #mrsItemsTable select {
    color: #000 !important;
    background-color: #fff !important;
  }
</style>

<?= $this->include('layout/footer') ?>
