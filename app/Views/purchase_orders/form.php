<?= $this->include('layout/header') ?>
<div class="container-fluid pt-1 pb-5">

  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0">
        <i class="bx bx-cart text-primary me-2"></i>
        <?= isset($po['id']) ? 'Edit Purchase Order' : 'Create Purchase Order' ?>
      </h5>
      <a href="<?= base_url('/purchaseorders/list') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bx bx-arrow-back"></i> Back
      </a>
    </div>

    <div class="card-body">
      <form action="<?= base_url('purchaseorders/store') ?>" method="post">
        <input type="hidden" name="id" value="<?= $po['id'] ?? '' ?>">
        <?= csrf_field() ?>

        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold">PO Number</label>
            <input type="text" name="po_number" class="form-control"
              value="<?= esc($po['po_number'] ?? $auto_no) ?>" readonly>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
            <div class="input-group">
              <select name="supplier_id" id="supplierSelect" class="form-select" required>
                <option value="">Select Supplier</option>
                <?php foreach($suppliers as $s): ?>
                  <option value="<?= $s['id'] ?>" <?= isset($po['supplier_id']) && $po['supplier_id'] == $s['id'] ? 'selected' : '' ?>>
                    <?= esc($s['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <button type="button" class="btn btn-outline-primary btn-sm"
                data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                <i class="bx bx-plus"></i>
              </button>
            </div>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Order Date</label>
            <input type="date" name="order_date" class="form-control"
              value="<?= esc($po['order_date'] ?? date('Y-m-d')) ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Expected Delivery</label>
            <input type="date" name="expected_date" class="form-control"
              value="<?= esc($po['expected_date'] ?? '') ?>">
          </div>

          <div class="col-md-8">
            <label class="form-label fw-semibold">Remarks</label>
            <input type="text" name="remarks" class="form-control"
              value="<?= esc($po['remarks'] ?? '') ?>" placeholder="Optional notes">
          </div>
        </div>

        <hr class="my-4">

        <h6 class="fw-bold mb-3"><i class="bx bx-list-plus text-primary me-2"></i> Items</h6>
        <div class="table-responsive">
          <table class="table table-bordered align-middle" id="itemTable">
            <thead class="table-light text-center">
              <tr>
                <th width="18%">Item Code</th>
                <th>Item</th>
                <th width="12%">Quantity</th>
                <th width="12%">Unit Price</th>
                <th width="12%">Amount</th>
                <th width="10%">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><input type="text" name="items[0][item_code]" class="form-control item-code"
                    placeholder="Enter code"></td>
                <td>
                  <select name="items[0][item_id]" class="form-select item-select" required>
                    <option value="">Select Item</option>
                    <?php foreach($items as $it): ?>
                      <option value="<?= $it['id'] ?>"><?= esc($it['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </td>
                <td><input type="number" name="items[0][qty]" class="form-control qty-input" step="0.01" required></td>
                <td><input type="number" name="items[0][unit_price]" class="form-control rate-input" step="0.01" required></td>
                <td><input type="text" name="items[0][amount]" class="form-control amount-field" readonly></td>
                <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm remove-row">
                    <i class="bx bx-trash"></i>
                  </button></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="text-end mt-3">
          <h5 class="fw-bold text-dark">Total Amount: ₹ <span id="grandTotal">0.00</span></h5>
          <button type="button" class="btn btn-outline-primary" id="addRowBtn">
            <i class="bx bx-plus"></i> Add Item
          </button>
        </div>

        <hr class="my-4">

        <div class="text-end">
          <button type="submit" class="btn btn-success px-4">
            <i class="bx bx-save"></i> Save Purchase Order
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add Supplier Modal -->
  <div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <form id="addSupplierForm" class="modal-content">
        <?= csrf_field() ?>
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title"><i class="bx bx-user-plus me-2"></i> Add Supplier</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body"><?= $this->include('suppliers/_form_fields') ?></div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-info text-white">Save Supplier</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add Item Modal -->
  <div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <form id="addItemForm" class="modal-content">
        <?= csrf_field() ?>
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bx bx-plus-circle me-2"></i> Add Item</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body"><?= $this->include('items/_form_fields') ?></div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Save Item</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add Unit Modal -->
  <div class="modal fade" id="addUnitModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <form id="addUnitForm" class="modal-content">
        <?= csrf_field() ?>
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title"><i class="bx bx-ruler me-2"></i> Add Unit</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body"><?= $this->include('units/_form_fields') ?></div>
        <div class="modal-footer"><button type="submit" class="btn btn-success">Save Unit</button></div>
      </form>
    </div>
  </div>

  <!-- Toast + Loader -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="statusToast" class="toast text-white border-0 shadow-sm" role="alert">
      <div class="d-flex align-items-center">
        <i id="toastIcon" class="bx fs-4 ms-3 me-2"></i>
        <div class="toast-body fw-semibold"></div>
        <button type="button" class="btn-close btn-close-white me-3" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>
  <div id="loaderOverlay"
    class="position-fixed top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center"
    style="background:rgba(255,255,255,0.8);z-index:2000;">
    <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>
</div>

<style>
/* --- Select2 Field Appearance --- */
.select2-container--default .select2-selection--single {
  height: 38px !important;
  border: 1px solid #ced4da !important;
  border-radius: 6px;
  padding: 4px 8px;
}

.select2-container--default .select2-selection__arrow {
  height: 36px !important;
}

/* --- Fix input-group alignment (for + Add Supplier button) --- */
.input-group .select2-container {
  flex: 1 1 auto !important;
  width: 1% !important;
}

.input-group .btn {
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* --- Keep dropdowns visible inside table cells if needed --- */
.table-responsive {
  overflow: visible !important;
}
</style>


<script>
document.addEventListener('DOMContentLoaded', function () {
  // --- CSRF tokens ---
  const csrfName = '<?= csrf_token() ?>';
  let csrfHash = '<?= csrf_hash() ?>';

  // --- Elements ---
  const tableBody = document.querySelector('#itemTable tbody');
  const grandTotalEl = document.getElementById('grandTotal');

  // --- Toast helper ---
  const showToast = (msg, type = 'success') => {
    const toastEl = document.getElementById('statusToast');
    const body = toastEl.querySelector('.toast-body');
    const icon = document.getElementById('toastIcon');
    toastEl.className = 'toast text-white border-0 shadow-sm bg-' + 
      (type === 'error' ? 'danger' : type === 'warning' ? 'warning text-dark' : 'success');
    icon.className = 'bx fs-4 ms-3 me-2 ' + 
      (type === 'error' ? 'bx-x-circle' : type === 'warning' ? 'bx-error' : 'bx-check-circle');
    body.textContent = msg;
    new bootstrap.Toast(toastEl, { delay: 2500 }).show();
  };

  // --- Loader helper ---
  const showLoader = (show = true) => {
    document.getElementById('loaderOverlay').classList.toggle('d-none', !show);
  };

  // --- Calculate totals ---
  const updateTotal = () => {
    let total = 0;
    document.querySelectorAll('.amount-field').forEach(a => total += parseFloat(a.value) || 0);
    grandTotalEl.textContent = total.toFixed(2);
  };
  const calcRow = row => {
    const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
    const rate = parseFloat(row.querySelector('.rate-input')?.value) || 0;
    row.querySelector('.amount-field').value = (qty * rate).toFixed(2);
    updateTotal();
  };

  // --- Add new item row ---
  function addItemRow() {
  const idx = tableBody.querySelectorAll('tr').length;
  const tr = document.createElement('tr');

  tr.innerHTML = `
    <td><input type="text" name="items[${idx}][item_code]" class="form-control item-code" readonly></td>
    <td>
      <select name="items[${idx}][item_id]" class="form-select item-select" required>
        <option value="">Select Item</option>
      </select>
    </td>
    <td><input type="number" name="items[${idx}][qty]" class="form-control qty-input" step="0.01" required></td>
    <td><input type="number" name="items[${idx}][unit_price]" class="form-control rate-input" step="0.01" required></td>
    <td><input type="text" name="items[${idx}][amount]" class="form-control amount-field" readonly></td>
    <td class="text-center">
      <button type="button" class="btn btn-outline-danger btn-sm remove-row">
        <i class="bx bx-trash"></i>
      </button>
    </td>
  `;

  tableBody.appendChild(tr);

  // ✅ Reset dropdown so it starts blank
  $(tr).find('.item-select').val(null).trigger('change');

  // ✅ Activate Select2 for the new row
  activateSelect2();
}


  // --- Row actions ---
  document.getElementById('addRowBtn').addEventListener('click', addItemRow);
  tableBody.addEventListener('click', e => {
    if (e.target.closest('.remove-row')) {
      if (tableBody.children.length > 1) {
        e.target.closest('tr').remove();
        updateTotal();
      } else {
        showToast('At least one item row is required', 'warning');
      }
    }
  });
  document.body.addEventListener('input', e => {
    if (e.target.matches('.qty-input, .rate-input')) calcRow(e.target.closest('tr'));
  });
  
  // ✅ Auto-fetch when user enters item code manually
tableBody.addEventListener('blur', async (e) => {
  if (e.target.classList.contains('item-code')) {
    const row = e.target.closest('tr');
    const code = e.target.value.trim();
    if (!code) return;

    showLoader(true);
    try {
      const res = await $.post("<?= base_url('purchaseorders/getItemByCode') ?>", {
        [csrfName]: csrfHash,
        code: code
      });

      showLoader(false);

      if (res.success) {
        $(row).find('.item-select')
          .append(new Option(`${res.item.code} - ${res.item.name}`, res.item.id, true, true))
          .trigger('change');
        $(row).find('.rate-input').val(res.item.unit_price);
        calcRow(row);
      } else {
        showToast('Item not found', 'error');
        $(row).find('.item-select').val(null).trigger('change');
        $(row).find('.rate-input').val('');
      }
    } catch (err) {
      showLoader(false);
      showToast('Error fetching item info', 'error');
    }
  }
}, true);


  // --- Initialize Select2 dropdowns ---
  // ✅ Item dropdowns — load all items once
let allItemsCache = null;

function activateSelect2() {
  // Supplier dropdown
  // ✅ Supplier dropdown (show all + search)
$('#supplierSelect').select2({
  placeholder: 'Search or select supplier',
  width: '100%',
  dropdownParent: $('body'),
  allowClear: true,
  ajax: {
    url: "<?= base_url('purchaseorders/searchSuppliersAjax') ?>",
    dataType: 'json',
    delay: 250,
    data: function (params) {
      return { q: params.term || '' };
    },
    processResults: function (data) {
      const results = data.results.map(sup => ({
        id: sup.id,
        text: sup.name,
        contact: sup.contact_person,
        phone: sup.phone
      }));
      return { results };
    }
  },
  templateResult: function (supplier) {
    if (!supplier.id) return supplier.text;
    return $(`
      <div>
        <strong>${supplier.text}</strong><br>
        <small>${supplier.contact || ''} ${supplier.phone ? ' - ' + supplier.phone : ''}</small>
      </div>
    `);
  },
  templateSelection: function (supplier) {
    return supplier.text || '';
  }
});

// Auto-focus on search field
$('#supplierSelect').on('select2:open', function () {
  $('.select2-search__field').focus();
});


  // Item dropdowns
  $('.item-select').each(function () {
    if ($(this).data('select2')) return;

    $(this).select2({
      placeholder: 'Search or select item',
      width: '100%',
      dropdownParent: $('body'),
      allowClear: true,
      data: allItemsCache, // if already loaded
      ajax: allItemsCache ? null : {
        url: "<?= base_url('purchaseorders/searchItemsAjax') ?>",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return { q: params.term || '' };
        },
        processResults: function (data) {
          const results = data.results.map(item => ({
            id: item.id,
            text: `${item.code} - ${item.name}`,
            unit_price: item.unit_price
          }));
          if (!allItemsCache) allItemsCache = results; // cache for later
          return { results };
        }
      },
      templateResult: function (item) {
        if (!item.id) return item.text;
        const [code, name] = item.text.split(' - ');
        return $(`
          <div>
            <strong>${code}</strong> - ${name}<br>
            <small>₹${item.unit_price || '0.00'} per unit</small>
          </div>
        `);
      },
      templateSelection: function (item) {
        return item.text || '';
      }
    });

    // Auto-fill item data
    $(this).on('select2:select', function (e) {
      const data = e.params.data;
      const row = $(this).closest('tr');
      row.find('.item-code').val(data.text.split(' - ')[0]);
      row.find('.rate-input').val(data.unit_price);
      const qty = parseFloat(row.find('.qty-input').val()) || 0;
      row.find('.amount-field').val((qty * parseFloat(data.unit_price)).toFixed(2));
      updateTotal();
    });
  });
}

// ✅ Load all items once on page load
fetch("<?= base_url('purchaseorders/searchItemsAjax') ?>")
  .then(r => r.json())
  .then(data => {
    allItemsCache = data.results.map(item => ({
      id: item.id,
      text: `${item.code} - ${item.name}`,
      unit_price: item.unit_price
    }));
    activateSelect2();
  });


  // --- Initialize everything on load ---
  activateSelect2();
});
</script>



<?= $this->include('layout/footer') ?>
