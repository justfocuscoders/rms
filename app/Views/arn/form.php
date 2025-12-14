<?= $this->include('layout/header') ?>

<div class="container-fluid py-3">
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-semibold">
        <i class="bx bx-barcode text-primary me-2"></i> <?= esc($title ?? 'Create ARN') ?>
      </h5>
      <a href="<?= site_url('arn') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bx bx-arrow-back"></i> Back
      </a>
    </div>

    <div class="card-body">
      <!-- single-item form (default shown when no PO selected) -->
      <form action="<?= site_url('arn/save') ?>" method="post" id="singleArnForm">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $arn['id'] ?? '' ?>">

        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold">ARN No</label>
            <input type="text" name="arn_no" class="form-control" value="<?= esc($arn_no ?? '') ?>" readonly>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Purchase Order</label>
            <select name="po_id" id="poSelect" class="form-select searchable-select">
              <option value="">Select PO</option>
              <?php foreach ($pos as $p): ?>
                <option value="<?= $p['id'] ?>" <?= ($arn['po_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                  <?= esc($p['po_number']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <small class="text-muted">Select PO to auto-switch to bulk mode (if PO has multiple items).</small>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Supplier</label>
            <select name="supplier_id" id="supplierSelect" class="form-select searchable-select">
              <option value="">Select Supplier</option>
              <?php foreach ($suppliers as $s): ?>
                <option value="<?= $s['id'] ?>" <?= ($arn['supplier_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                  <?= esc($s['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Item</label>
            <select name="item_id" id="itemSelect" class="form-select searchable-select">
              <option value="">Select Item</option>
              <?php foreach ($items as $i): ?>
                <option value="<?= $i['id'] ?>" <?= ($arn['item_id'] ?? '') == $i['id'] ? 'selected' : '' ?>>
                  <?= esc($i['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Batch No</label>
            <input type="text" name="batch_no" class="form-control" value="<?= esc($arn['batch_no'] ?? '') ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Received Quantity</label>
            <input type="number" step="0.01" name="received_qty" class="form-control" value="<?= esc($arn['received_qty'] ?? '') ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">UOM</label>
            <input type="text" name="uom" id="singleUom" class="form-control" value="<?= esc($arn['uom'] ?? '') ?>" readonly>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Received Date</label>
            <input type="date" name="received_date" class="form-control" value="<?= esc($arn['received_date'] ?? date('Y-m-d')) ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Expiry Date</label>
            <input type="date" name="expiry_date" class="form-control" value="<?= esc($arn['expiry_date'] ?? '') ?>">
          </div>
        </div>

        <div class="mt-4 text-end">
          <button type="submit" class="btn btn-primary px-4">
            <i class="bx bx-save me-1"></i> Save ARN
          </button>
        </div>
      </form>

      <!-- bulk view: hidden by default, shown when PO selected -->
      <div id="bulkArnContainer" class="d-none mt-4">
        <div class="card border-0 shadow-sm rounded-3">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold text-primary"><i class="bx bx-list-ul me-2"></i> PO Items — Create ARNs</h6>
            <div>
              <button id="createAllArnBtn" class="btn btn-primary btn-sm">
                <i class="bx bx-layer-plus me-1"></i> Create All ARNs
              </button>
              <button id="cancelBulkBtn" class="btn btn-outline-secondary btn-sm ms-2">
                Cancel
              </button>
            </div>
          </div>

          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm align-middle mb-0" id="bulkItemsTable">
                <thead class="bg-light text-secondary text-center">
                  <tr>
                    <th>Item</th>
                    <th>Ordered Qty</th>
                    <th>Unit</th>
                    <th>Batch No</th>
                    <th>MFG Date</th>
                    <th>Expiry Date</th>
                    <th>Received Qty</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody class="text-center">
                  <!-- rows populated by JS -->
                </tbody>
              </table>
            </div>
          </div>

          <div class="card-footer text-end bg-white border-top">
            <small class="text-muted me-3">Tip: edit qty, batch or dates before creating ARNs</small>
            <button id="createAllArnBtnFooter" class="btn btn-primary">Create All ARNs</button>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- loader & toast (reused style) -->
<div id="loaderOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center" style="background: rgba(255,255,255,0.75); z-index: 2000;">
  <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;"><span class="visually-hidden">Loading...</span></div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
  <div id="statusToast" class="toast align-items-center text-white border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex align-items-center">
      <i id="toastIcon" class="bx fs-4 ms-3 me-2"></i>
      <div class="toast-body fw-semibold"></div>
      <button type="button" class="btn-close btn-close-white me-3" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<style>
/* minimal UI polish to integrate with RMS header styles */
.card { border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
.form-label { font-weight:600; font-size:0.9rem; }
.select2-container .select2-selection--single { height:38px !important; border-radius:6px !important; }
#bulkItemsTable tbody td input { width:100%; }
</style>

<script>
$(function () {
  // init select2 (header already includes library)
  $('.searchable-select').select2({ placeholder: 'Search or select...', width: '100%' });

  const $po = $('#poSelect');
  const $supplier = $('#supplierSelect');
  const $item = $('#itemSelect');
  const $singleUom = $('#singleUom');
  const $bulkContainer = $('#bulkArnContainer');
  const $bulkTbody = $('#bulkItemsTable tbody');
  const $loader = $('#loaderOverlay');

  const showLoader = (on) => $loader.toggleClass('d-none', !on);

  const showToast = (message, type='success') => {
    const $toast = $('#statusToast');
    const $body = $toast.find('.toast-body');
    const $icon = $('#toastIcon');
    $toast.removeClass('bg-success bg-danger bg-warning text-dark');
    if (type === 'success') $toast.addClass('bg-success');
    else if (type === 'error') $toast.addClass('bg-danger');
    else if (type === 'warning') $toast.addClass('bg-warning text-dark');
    $icon.attr('class', 'bx fs-4 ms-3 me-2');
    $body.text(message);
    new bootstrap.Toast($toast[0], { delay: 3000 }).show();
  };

  // when item selected in single form -> fetch UOM
  $item.on('change', function () {
    const itemId = $(this).val();
    if (!itemId) { $singleUom.val(''); return; }
    $.getJSON('<?= base_url('items/info/') ?>' + itemId)
      .done(res => {
        if (res.success && res.item) {
          const uom = res.item.unit || res.item.uom || '';
          $singleUom.val(uom);
        }
      }).fail(() => console.error('item info fetch failed'));
  });

  // when PO selected -> fetch PO items, supplier and switch to bulk view (if items exist)
  $po.on('change', function () {
    const poId = $(this).val();

    // if no PO => revert to single form
    if (!poId) {
      $bulkContainer.addClass('d-none');
      $('#singleArnForm').removeClass('d-none');
      return;
    }

    showLoader(true);
    fetch('<?= base_url('purchaseorders/info/') ?>' + poId, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => {
      showLoader(false);
      if (!res.success) {
        showToast('Failed to fetch PO details', 'error');
        return;
      }

      // set supplier
      if (res.po && res.po.supplier_id) {
        $supplier.val(res.po.supplier_id).trigger('change');
      }

      // items array - if none, stay in single mode
      if (!res.items || res.items.length === 0) {
        $bulkContainer.addClass('d-none');
        $('#singleArnForm').removeClass('d-none');
        showToast('Selected PO has no items', 'warning');
        return;
      }

      // build table rows
      $bulkTbody.empty();
      res.items.forEach((it, idx) => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td class="text-start">${escapeHtml(it.item_name)}<input type="hidden" name="rows[${idx}][item_id]" value="${it.item_id}"></td>
          <td>${(it.quantity ?? '')}</td>
          <td>${escapeHtml(it.unit ?? '')}</td>
          <td><input type="text" class="form-control form-control-sm" name="rows[${idx}][batch_no]" value=""></td>
          <td><input type="date" class="form-control form-control-sm" name="rows[${idx}][mfg_date]" value=""></td>
          <td><input type="date" class="form-control form-control-sm" name="rows[${idx}][expiry_date]" value=""></td>
          <td><input type="number" step="0.01" class="form-control form-control-sm text-end" name="rows[${idx}][received_qty]" value="${it.quantity ?? ''}"></td>
          <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bx bx-trash"></i></button></td>
        `;
        $bulkTbody.append(row);
      });

      // switch UI
      $('#singleArnForm').addClass('d-none'); // hide single form
      $bulkContainer.removeClass('d-none');   // show bulk container

      // attach remove action
      $bulkTbody.off('click.bulk').on('click.bulk', '.remove-row', function () {
        $(this).closest('tr').remove();
      });

    })
    .catch(err => { showLoader(false); console.error(err); showToast('Failed to fetch PO details', 'error'); });
  });

  // Cancel bulk mode -> revert to single
  $('#cancelBulkBtn').on('click', function () {
    $po.val('').trigger('change.select2'); // clear PO
    $bulkTbody.empty();
    $bulkContainer.addClass('d-none');
    $('#singleArnForm').removeClass('d-none');
  });

  // Create all ARNs (footer and header button do the same)
  $('#createAllArnBtn, #createAllArnBtnFooter').on('click', function () {
    const rows = Array.from($bulkTbody.find('tr'));
    if (rows.length === 0) { showToast('No items to create ARN for', 'warning'); return; }

    // build payloads per row
    const poId = $po.val();
    const supplierId = $supplier.val();
    if (!poId || !supplierId) { showToast('PO or Supplier missing', 'warning'); return; }

    const payloads = [];
    for (let r of rows) {
      const $r = $(r);
      const itemId = $r.find('input[type="hidden"][name^="rows"]').val() || $r.find('input[name*="[item_id]"]').val();
      // fallback parse hidden input as name attr uses rows[idx][item_id]
      const itemIdHidden = $r.find('input[type="hidden"]').val();
      const batch = $r.find('input[name*="[batch_no]"]').val() || '';
      const mfg = $r.find('input[name*="[mfg_date]"]').val() || '';
      const exp = $r.find('input[name*="[expiry_date]"]').val() || '';
      const received_qty = $r.find('input[name*="[received_qty]"]').val() || '';
      // itemIdHidden is correct
      if (!itemIdHidden) continue;
      payloads.push({
        po_id: poId,
        supplier_id: supplierId,
        item_id: itemIdHidden,
        batch_no: batch,
        mfg_date: mfg,
        expiry_date: exp,
        received_qty: received_qty
      });
    }

    if (payloads.length === 0) { showToast('No valid items to create', 'warning'); return; }

    // confirm
    if (!confirm(`Create ${payloads.length} ARNs for this PO?`)) return;

    showLoader(true);

    // send parallel requests (but small throttle to avoid server spikes)
    const promises = payloads.map(pl => {
      // include CSRF token from meta tag
      const token = $('meta[name="csrf-token"]').attr('content');
      return fetch('<?= base_url('arn/save_ajax') ?>', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': token,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(pl)
      }).then(r => r.json())
      .catch(err => ({ success: false, error: err }));
    });

    Promise.all(promises)
      .then(results => {
        showLoader(false);
        const successCount = results.filter(r => r && r.success).length;
        const failedCount = results.length - successCount;
        if (successCount) {
          showToast(`${successCount} ARN(s) created successfully`, 'success');
        }
        if (failedCount) {
          showToast(`${failedCount} failed. Check console.`, 'warning');
          console.error('ARN bulk errors:', results.filter(r => !r.success));
        }
        // After creation: reset form / show single form
        $bulkTbody.empty();
        $bulkContainer.addClass('d-none');
        $('#singleArnForm').removeClass('d-none');
        $po.val('').trigger('change.select2');
      })
      .catch(err => {
        showLoader(false);
        console.error('Bulk create failed:', err);
        showToast('Bulk create failed', 'error');
      });
  });

  // utility: escape to prevent HTML insertion in table cell
  function escapeHtml(str) {
    if (!str && str !== 0) return '';
    return String(str).replace(/[&<>"'`=\/]/g, function (s) {
      return ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
        '/': '&#x2F;',
        '`': '&#x60;',
        '=': '&#x3D;'
      })[s];
    });
  }

});
</script>

<?= $this->include('layout/footer') ?>
