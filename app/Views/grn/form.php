<?= $this->include('layout/header') ?>
<div class="container-fluid pt-3 pb-5">
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0">
        <i class="bx bxs-truck text-primary me-2"></i>
        <?= isset($grn['id']) ? 'Edit GRN' : 'Create GRN' ?>
      </h5>
      <a href="<?= base_url('/grn/list') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bx bx-arrow-back"></i> Back
      </a>
    </div>

    <div class="card-body">
      <form action="<?= base_url('/grn/save') ?>" method="post" id="grnForm">
        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="bx bx-error-circle me-1"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>
        <?php if (isset($grn['id'])): ?>
          <input type="hidden" name="id" value="<?= esc($grn['id']) ?>"> <!-- keep name 'id' -->
        <?php endif; ?>

        <?= csrf_field() ?>

        <!-- GRN Header -->
        <div class="row g-3">

          <div class="col-md-4">
            <label class="form-label fw-semibold">
  GRN Category <span class="text-danger">*</span>
</label>

<div class="input-group">
  <select name="category_id"
    class="form-select <?= session()->getFlashdata('error') ? 'is-invalid' : '' ?>"
    required>

    <option value="">Select</option>

    <?php foreach ($categories as $cat): ?>
      <option value="<?= $cat['id'] ?>"
        <?= (string) old('category_id', $grn['category_id'] ?? '') === (string) $cat['id']
            ? 'selected'
            : '' ?>>
        <?= esc($cat['name']) ?>
      </option>
    <?php endforeach; ?>

  </select>

  <button type="button"
    class="btn btn-outline-primary btn-square"
    title="Add New Category"
    data-bs-toggle="modal"
    data-bs-target="#addCategoryModal">
    <i class="bx bx-plus"></i>
  </button>
</div>

          </div>



          <div class="col-md-4">
            <label class="form-label fw-semibold">Purchase Order</label>

            <div class="input-group">
              <select name="po_id"
                id="poSelect"
                class="form-select <?= session()->getFlashdata('error') ? 'is-invalid' : '' ?>">

                <option value="">Select PO</option>

                <?php if (!empty($purchase_orders)): ?>
                  <?php foreach ($purchase_orders as $po): ?>
                    <option value="<?= $po['id'] ?>"
                      <?= old('po_id', $grn['po_id'] ?? '') == $po['id'] ? 'selected' : '' ?>>
                      <?= esc($po['po_number']) ?> — <?= esc($po['supplier_name'] ?? '') ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>

              </select>
            </div>

            <small class="text-muted">
              Selecting a PO will auto-fill supplier &amp; items.
            </small>
          </div>


          <div class="col-md-4">
            <label class="form-label fw-semibold">
              Supplier <span class="text-danger">*</span>
            </label>

            <div class="input-group">
              <select name="supplier_id"
                id="supplierSelect"
                class="form-select <?= session()->getFlashdata('error') ? 'is-invalid' : '' ?>"
                required>

                <option value="">Select Supplier</option>

                <?php if (!empty($suppliers)): ?>
                  <?php foreach ($suppliers as $s): ?>
                    <option value="<?= $s['id'] ?>"
                      <?= old('supplier_id', $grn['supplier_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                      <?= esc($s['name']) ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>

              </select>

              <button type="button"
                class="btn btn-outline-primary btn-square ms-1"
                title="Add New Supplier"
                data-bs-toggle="modal"
                data-bs-target="#addSupplierModal">
                <i class="bx bx-plus"></i>
              </button>
            </div>
          </div>


          <div class="col-md-4">
            <label class="form-label fw-semibold">GRN Number</label>
            <input type="text"
              class="form-control"
              value="<?= esc($grn['grn_no'] ?? $auto_no) ?>"
              readonly>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">ARN Number</label>
            <input type="text"
              class="form-control"
              value="<?= esc($grn['arn_no'] ?? str_replace('GRN', 'ARN', $auto_no)) ?>"
              readonly>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Received Date</label>
            <input type="date"
              name="received_date"
              class="form-control <?= session()->getFlashdata('error') ? 'is-invalid' : '' ?>"
              value="<?= old('received_date', $grn['received_date'] ?? date('Y-m-d')) ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Status</label>
            <select name="status"
              class="form-select <?= session()->getFlashdata('error') ? 'is-invalid' : '' ?>"
              required>
              <?php foreach (['QC Pending', 'QC Approved', 'QC Rejected'] as $st): ?>
                <option value="<?= $st ?>"
                  <?= old('status', $grn['status'] ?? 'QC Pending') === $st ? 'selected' : '' ?>>
                  <?= $st ?>
                </option>
              <?php endforeach; ?>
            </select>
            <small class="text-muted">Default status: QC Pending</small>
          </div>


        </div>

        <!-- Extended GRN Fields -->
        <div class="row g-3 mt-2">
          <div class="col-md-3">
            <label class="form-label fw-semibold">Gate Entry No</label>
            <input type="text"
              name="gate_entry_no"
              class="form-control"
              value="<?= old('gate_entry_no', $grn['gate_entry_no'] ?? '') ?>">
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Gate Entry Date</label>
            <input type="date"
              name="gate_entry_date"
              class="form-control"
              value="<?= old('gate_entry_date', $grn['gate_entry_date'] ?? '') ?>">
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Challan No</label>
            <input type="text"
              name="challan_no"
              class="form-control"
              value="<?= old('challan_no', $grn['challan_no'] ?? '') ?>">
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Challan Date</label>
            <input type="date"
              name="challan_date"
              class="form-control"
              value="<?= old('challan_date', $grn['challan_date'] ?? '') ?>">
          </div>
        </div>


        <div class="row g-3 mt-2">
          <div class="col-md-3">
            <label class="form-label fw-semibold">Transport Name</label>
            <input type="text"
              name="transport_name"
              class="form-control"
              value="<?= old('transport_name', $grn['transport_name'] ?? '') ?>">
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">LR No</label>
            <input type="text"
              name="lr_no"
              class="form-control"
              value="<?= old('lr_no', $grn['lr_no'] ?? '') ?>">
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">LR Date</label>
            <input type="date"
              name="lr_date"
              class="form-control"
              value="<?= old('lr_date', $grn['lr_date'] ?? '') ?>">
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Vehicle No</label>
            <input type="text"
              name="vehicle_no"
              class="form-control"
              value="<?= old('vehicle_no', $grn['vehicle_no'] ?? '') ?>">
          </div>
        </div>


        <div class="row g-3 mt-2">

          <div class="col-md-3">
  <label class="form-label fw-semibold">Location</label>

  <select name="location" class="form-select" required>
    <option value="">Select Location</option>

    <?php foreach ($locations as $loc): ?>
      <option value="<?= $loc['id'] ?>"
        <?= old('location', $grn['location'] ?? '') == $loc['id']
            ? 'selected' : '' ?>>
        <?= esc($loc['name']) ?>
      </option>
    <?php endforeach; ?>

  </select>
</div>


          <div class="col-md-3">
            <label class="form-label fw-semibold">Manufacturer</label>
            <input type="text"
              name="manufacturer"
              class="form-control"
              value="<?= old('manufacturer', $grn['manufacturer'] ?? '') ?>">
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Reported</label>
            <input type="datetime-local"
              name="reported_at"
              class="form-control"
              value="<?= old('reported_at', $grn['reported_at'] ?? '') ?>">
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Unloaded</label>
            <input type="datetime-local"
              name="unloaded_at"
              class="form-control"
              value="<?= old('unloaded_at', $grn['unloaded_at'] ?? '') ?>">
          </div>

        </div>



        <hr class="my-4">

        <!-- Items Section -->
        <div class="card border-0 shadow-sm rounded-3 mt-4">
          <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3 px-4">
            <h6 class="mb-0 fw-semibold text-primary">
              <i class="bx bx-list-ul me-2"></i> Items Received
            </h6>
            <button type="button" id="addRow" class="btn btn-sm btn-outline-primary">
              <i class="bx bx-plus"></i> Add Item
            </button>
          </div>

          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm align-middle mb-0" id="itemTable">
                <thead class="bg-light text-center text-secondary align-middle">
                  <tr>
                    <th style="width:14%;">Item</th>
                    <th style="width:8%;">Batch No</th>
                    <th style="width:8%;">Lot No</th>
                    <th style="width:7%;">Capacity</th>
                    <th style="width:6%;">NoC</th>
                    <th style="width:7%;">Qty</th>
                    <th style="width:7%;">Unit</th>
                    <th style="width:7%;">Rate</th>
                    <th style="width:8%;">Amount</th>
                    <th style="width:8%;">MFG DT</th>

                    <!-- ? Header dropdown: EXP DT / RET DT -->
                    <th style="width:8%;">
                      <select id="headerExpiryType"
                        class="form-select form-select-sm fw-semibold text-center">

                        <option value="expiry"
                          <?= old('expiry_type', 'expiry') === 'expiry' ? 'selected' : '' ?>>
                          EXP DT
                        </option>

                        <option value="retest"
                          <?= old('expiry_type') === 'retest' ? 'selected' : '' ?>>
                          RET DT
                        </option>

                      </select>

                    </th>
                    <th style="width:10%;">Storage Location</th>
                    <th style="width:8%;">Remarks</th>
                    <th style="width:4%;"></th>
                  </tr>
                </thead>

                <tbody class="text-center">

                  <?php
                  $rows = old('items') ?? (!empty($grn_items) ? $grn_items : [[]]);
                  ?>

                  <?php foreach ($rows as $i => $item): ?>
                    <tr>

                      <!-- Item -->
                      <td class="text-start">
                        <select name="items[<?= $i ?>][item_id]"
                          class="form-select form-select-sm item-select"
                          required>
                          <option value="">Select</option>
                          <?php foreach ($items as $itm): ?>
                            <option value="<?= $itm['id'] ?>"
                              <?= old("items.$i.item_id", $item['item_id'] ?? '') == $itm['id'] ? 'selected' : '' ?>>
                              <?= esc($itm['name']) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </td>

                      <!-- Batch -->
                      <td>
                        <input type="text"
                          name="items[<?= $i ?>][batch_no]"
                          class="form-control form-control-sm"
                          value="<?= old("items.$i.batch_no", $item['batch_no'] ?? '') ?>">
                      </td>

                      <!-- Lot -->
                      <td>
                        <input type="text"
                          name="items[<?= $i ?>][lot_no]"
                          class="form-control form-control-sm"
                          value="<?= old("items.$i.lot_no", $item['lot_no'] ?? '') ?>">
                      </td>

                      <!-- Capacity -->
                      <td>
                        <input type="number" step="0.01"
                          name="items[<?= $i ?>][capacity]"
                          class="form-control form-control-sm text-end"
                          value="<?= old("items.$i.capacity", $item['capacity'] ?? '') ?>">
                      </td>

                      <!-- NoC -->
                      <td>
                        <input type="number" step="1"
                          name="items[<?= $i ?>][noc]"
                          class="form-control form-control-sm text-end"
                          value="<?= old("items.$i.noc", $item['noc'] ?? '') ?>">
                      </td>

                      <!-- Quantity -->
                      <td>
                        <input type="number" step="0.01"
                          name="items[<?= $i ?>][quantity]"
                          class="form-control form-control-sm text-end qty-input"
                          value="<?= old("items.$i.quantity", $item['qty_received'] ?? '') ?>">
                      </td>

                      <!-- Unit -->
                      <td>
                        <select name="items[<?= $i ?>][unit_id]"
                          class="form-select form-select-sm unit-dropdown">
                          <option value="">Select</option>
                          <?php foreach ($units as $unit): ?>
                            <option value="<?= $unit['id'] ?>"
                              <?= old("items.$i.unit_id", $item['unit_id'] ?? '') == $unit['id'] ? 'selected' : '' ?>>
                              <?= esc($unit['name']) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </td>

                      <!-- Rate -->
                      <td>
                        <input type="number" step="0.01"
                          name="items[<?= $i ?>][rate]"
                          class="form-control form-control-sm text-end rate-input"
                          value="<?= old("items.$i.rate", $item['rate'] ?? '') ?>">
                      </td>

                      <!-- Amount -->
                      <td>
                        <input type="number" step="0.01"
                          name="items[<?= $i ?>][amount]"
                          class="form-control form-control-sm text-end amount-field"
                          readonly
                          value="<?= old("items.$i.amount", $item['amount'] ?? '') ?>">
                      </td>

                      <!-- MFG -->
                      <td>
                        <input type="date"
                          name="items[<?= $i ?>][mfg_date]"
                          class="form-control form-control-sm"
                          value="<?= old("items.$i.mfg_date", $item['mfg_date'] ?? '') ?>">
                      </td>

                      <!-- Expiry / Retest -->
                      <td>
                        <input type="date"
                          name="items[<?= $i ?>][expiry_or_retest]"
                          class="form-control form-control-sm expiry-date"
                          value="<?= old(
                                    "items.$i.expiry_or_retest",
                                    $item['retest_date'] ?? $item['expiry_date'] ?? ''
                                  ) ?>">
                      </td>

                      <!-- Storage Location -->

                      <td>
                        <select name="items[<?= $i ?>][storage_location_id]"
        class="form-select form-select-sm"
        required>
  <option value="">Select</option>

  <?php foreach ($storage_locations as $sl): ?>
    <option value="<?= $sl['id'] ?>"
      data-location="<?= $sl['location_id'] ?>"
      <?= old("items.$i.storage_location_id",
            $item['storage_location_id'] ?? '') == $sl['id']
            ? 'selected' : '' ?>>
      <?= esc($sl['name']) ?>
    </option>
  <?php endforeach; ?>
</select>

                      </td>

                      <!-- Remarks -->
                      <td>
                        <input type="text"
                          name="items[<?= $i ?>][remarks]"
                          class="form-control form-control-sm"
                          value="<?= old("items.$i.remarks", $item['remarks'] ?? '') ?>">
                      </td>

                      <!-- Remove -->
                      <td>
                        <button type="button"
                          class="btn btn-sm btn-outline-danger remove-row">
                          <i class="bx bx-trash"></i>
                        </button>
                      </td>

                    </tr>
                  <?php endforeach; ?>

                </tbody>

              </table>
            </div>
          </div>
        </div>




        <!-- Totals & Actions -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <a href="<?= base_url('/grn/list') ?>" class="btn btn-outline-secondary">Cancel</a>
          </div>
          <div class="text-end">
            <div class="mb-2">
              <span class="fw-semibold me-2">Grand Total:</span>
              <span id="grandTotal" class="fs-5 fw-bold">0.00</span>
            </div>
            <button type="submit" class="btn btn-primary px-4">
              <i class="bx bx-save me-1"></i> Save GRN
            </button>
          </div>
        </div>

      </form>
    </div>
  </div>

  <!-- Modals (unchanged) -->
  <!-- Add New Item Modal -->
  <div class="modal fade"
    id="addItemModal"
    tabindex="-1"
    aria-labelledby="addItemModalLabel"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false">

    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">

        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="addItemModalLabel">
            <i class="bx bx-plus-circle me-2"></i> Add New Item
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <?php if (session()->getFlashdata('item_error')): ?>
            <div class="alert alert-danger">
              <?= session()->getFlashdata('item_error') ?>
            </div>
          <?php endif; ?>

          <form id="addItemForm" method="post" action="<?= base_url('/items/store') ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
              <label class="form-label fw-semibold">Item Name</label>
              <input type="text" name="name" class="form-control"
                value="<?= old('name') ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Description</label>
              <textarea name="description" class="form-control"
                rows="2"><?= old('description') ?></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Unit</label>
              <div class="input-group">
                <select name="unit_id" class="form-select" required>
                  <option value="">Select Unit</option>
                  <?php foreach ($units as $u): ?>
                    <option value="<?= $u['id'] ?>"
                      <?= old('unit_id') == $u['id'] ? 'selected' : '' ?>>
                      <?= esc($u['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>

                <button type="button"
                  class="btn btn-outline-primary"
                  data-bs-toggle="modal"
                  data-bs-target="#addUnitModal">
                  <i class="bx bx-plus"></i>
                </button>
              </div>
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-primary px-3">
                <i class="bx bx-save"></i> Save Item
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>


  <!-- Add Unit Modal -->
  <div class="modal fade" id="addUnitModal" tabindex="-1" aria-labelledby="addUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="addUnitModalLabel">
            <i class="bx bx-ruler me-2"></i> Add New Unit
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="addUnitForm">
            <?= csrf_field() ?>
            <div class="mb-3">
              <label class="form-label fw-semibold">Unit Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Symbol (optional)</label>
              <input type="text" name="symbol" class="form-control">
            </div>
            <div class="text-end">
              <button type="submit" class="btn btn-success px-3">
                <i class="bx bx-save"></i> Save Unit
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Supplier Modal -->
  <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title" id="addSupplierModalLabel">
            <i class="bx bx-user-plus me-2"></i> Add New Supplier
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <form id="addSupplierForm">
            <?= csrf_field() ?>
            <div class="mb-3">
              <label class="form-label fw-semibold">Supplier Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Contact Person</label>
              <input type="text" name="contact_person" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Phone</label>
              <input type="text" name="phone" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Email</label>
              <input type="email" name="email" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Address</label>
              <textarea name="address" class="form-control" rows="2"></textarea>
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-info px-3 text-white">
                <i class="bx bx-save"></i> Save Supplier
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Category Modal -->
  <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="addCategoryModalLabel">
            <i class="bx bx-layer-plus me-2"></i> Add GRN Category
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="addCategoryForm">
            <?= csrf_field() ?>
            <div class="mb-3">
              <label class="form-label fw-semibold">Category Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Description</label>
              <textarea name="description" class="form-control" rows="2"></textarea>
            </div>
            <div class="text-end">
              <button type="submit" class="btn btn-primary px-3">
                <i class="bx bx-save"></i> Save Category
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>


  <!-- Toast & Loader -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
    <div id="statusToast" class="toast align-items-center text-white border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex align-items-center">
        <i id="toastIcon" class="bx fs-4 ms-3 me-2"></i>
        <div class="toast-body fw-semibold"></div>
        <button type="button" class="btn-close btn-close-white me-3" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

  <div id="loaderOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center" style="background: rgba(255,255,255,0.8); z-index: 2000;">
    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>

</div>

<style>
  /* Card + page spacing */
  .content {
    max-width: 1200px;
    margin: 0 auto;
  }

  .card {
    border-radius: 10px;
  }

  .card-header i {
    font-size: 1.05rem;
  }

  .fw-semibold {
    font-weight: 600;
  }

  /* Form inputs */
  .form-control,
  .form-select {
    border-radius: 8px;
    height: 40px;
    padding: 0.4rem 0.75rem;
  }

  label.form-label {
    font-size: 0.9rem;
  }

  /* Buttons */
  .btn-outline-primary {
    border-radius: 8px;
  }

  .btn-primary,
  .btn-success {
    border-radius: 8px;
  }

  /* Table */
  .table th {
    font-weight: 600;
    font-size: 0.92rem;
    background: #f8fafc;
  }

  .table td {
    vertical-align: middle;
    font-size: 0.92rem;
  }

  .table .remove-row {
    padding: 6px 9px;
  }

  /* Modals */
  .modal-content {
    border-radius: 10px;
  }

  /* Toast & loader */
  .toast .toast-body {
    font-size: 0.95rem;
  }

  /* Subtle hover */
  .card:hover {
    box-shadow: 0 6px 20px rgba(16, 24, 40, 0.06);
    transition: 0.25s ease;
  }

  /* Modal stacking fix */
  .modal-backdrop.show:nth-of-type(2) {
    opacity: 0.35 !important;
  }

  /* small helper */
  .input-group .form-select {
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
  }

  .input-group .btn {
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
  }

  /* Responsive tweak */
  @media (max-width: 576px) {

    .form-control,
    .form-select {
      height: 44px;
    }
  }

  /* Fix alignment and look of the Unit input-group */
  .table .input-group .form-select,
  .table .input-group .btn {
    height: 38px;
    border-radius: 0;
  }

  .table .input-group .form-select {
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
  }

  .table .input-group .btn {
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 10px;
  }

  .table .input-group .btn i {
    font-size: 1rem;
  }

  /* Optional: fix hover color for the + button */
  .table .input-group .btn-outline-success:hover {
    background-color: #28a745;
    color: white;
  }

  /* --- Uniform Add (+) button style like Purchase Form --- */
  .btn-outline-primary.rounded-circle,
  .btn-outline-success.rounded-circle {
    width: 36px;
    height: 36px;
    padding: 0;
    border-radius: 50% !important;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .btn-outline-primary.rounded-circle i,
  .btn-outline-success.rounded-circle i {
    font-size: 1rem;
  }

  /* Hover effects */
  .btn-outline-primary.rounded-circle:hover {
    background-color: #0d6efd;
    color: #fff;
  }

  .btn-outline-success.rounded-circle:hover {
    background-color: #198754;
    color: #fff;
  }

  /* --- Square Add (+) Buttons like Purchase Form --- */
  .btn-square {
    width: 38px;
    height: 38px;
    padding: 0;
    border-radius: 8px !important;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .btn-square i {
    font-size: 1.2rem;
  }

  /* Hover styles */
  .btn-outline-primary.btn-square:hover {
    background-color: #0d6efd;
    color: #fff;
  }

  .btn-outline-success.btn-square:hover {
    background-color: #198754;
    color: #fff;
  }

  .input-group .btn-square {
    margin-left: 4px;
  }

  /* Fix: Keep + button inline with Select2 supplier dropdown */
  #supplierSelect+.select2-container {
    flex: 1 1 auto;
  }

  .input-group .select2-container {
    flex: 1 1 auto !important;
    width: 1% !important;
  }

  .input-group .btn-square {
    flex: 0 0 auto;
    height: 32px;
  }

  #itemTable th,
  #itemTable td {
    white-space: nowrap;
    vertical-align: middle;
  }

  #itemTable th:nth-child(1),
  #itemTable td:nth-child(1) {
    min-width: 220px;
    /* Item */
  }

  #itemTable th:nth-child(2),
  #itemTable th:nth-child(3),
  #itemTable td:nth-child(2),
  #itemTable td:nth-child(3) {
    min-width: 110px;
    /* Batch / Lot */
  }

  #itemTable th:nth-child(4),
  #itemTable th:nth-child(5),
  #itemTable th:nth-child(6),
  #itemTable td:nth-child(4),
  #itemTable td:nth-child(5),
  #itemTable td:nth-child(6) {
    min-width: 80px;
    /* Capacity / NoC / Qty */
  }

  #itemTable th:nth-child(7),
  #itemTable td:nth-child(7) {
    min-width: 120px;
    /* Unit */
  }

  #itemTable th:nth-child(8),
  #itemTable th:nth-child(9),
  #itemTable td:nth-child(8),
  #itemTable td:nth-child(9) {
    min-width: 90px;
    /* Rate / Amount */
  }

  #itemTable th:nth-child(10),
  #itemTable th:nth-child(11),
  #itemTable td:nth-child(10),
  #itemTable td:nth-child(11) {
    min-width: 130px;
    /* Dates */
  }

  .amount-field {
    background-color: #f8fafc;
    font-weight: 600;
  }

  /* Allow browser to auto-calculate column widths */
  #itemTable {
    table-layout: auto;
    width: 100%;
  }

  /* Ensure inputs expand to column width */
  #itemTable input,
  #itemTable select {
    width: 100%;
    min-width: 80px;
  }

  /* Numeric columns should align right */
  #itemTable td:nth-child(4),
  #itemTable td:nth-child(5),
  #itemTable td:nth-child(6),
  #itemTable td:nth-child(8),
  #itemTable td:nth-child(9) {
    text-align: right;
  }

  /* Improve readability of numbers */
  #itemTable input[type="number"] {
    font-variant-numeric: tabular-nums;
  }

  /* Sticky header for better horizontal scrolling */
  #itemTable thead th {
    position: sticky;
    top: 0;
    z-index: 2;
    background: #f8fafc;
  }

  /* === FIX: Amount value getting clipped === */
  #itemTable td {
    overflow: visible !important;
  }

  #itemTable .amount-field {
    text-align: right;
    padding-right: 12px;
    /* critical */
    overflow: visible;
    /* critical */
    text-overflow: unset;
    white-space: nowrap;
  }

  /* Slightly widen Amount column */
  #itemTable th:nth-child(9),
  #itemTable td:nth-child(9) {
    min-width: 120px;
  }
</style>

<script>
  const STORAGE_LOCATIONS = <?= json_encode($storage_locations ?? []) ?>;
</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('#itemTable tbody');
    const addRowBtn = document.getElementById('addRow');
    const grandTotalEl = document.getElementById('grandTotal');
    const headerSelect = document.getElementById('headerExpiryType');
    const grnForm = document.getElementById('grnForm');

    // ? Toast Notification
    const showToast = (message, type = 'success') => {
      const toastEl = document.getElementById('statusToast');
      const toastBody = toastEl.querySelector('.toast-body');
      const toastIcon = document.getElementById('toastIcon');
      toastEl.className = 'toast align-items-center text-white border-0 shadow-sm';
      toastIcon.className = 'bx fs-4 ms-3 me-2';
      if (type === 'success') toastEl.classList.add('bg-success');
      else if (type === 'error') toastEl.classList.add('bg-danger');
      else if (type === 'warning') toastEl.classList.add('bg-warning', 'text-dark');
      toastBody.textContent = message;
      new bootstrap.Toast(toastEl, {
        delay: 2500
      }).show();
    };

    // ? Hidden input for expiry/retest type (so it?s sent in form)
    const hiddenType = document.createElement('input');
    hiddenType.type = 'hidden';
    hiddenType.name = 'expiry_type';
    hiddenType.value = headerSelect.value;
    grnForm.appendChild(hiddenType);

    headerSelect.addEventListener('change', () => {
      hiddenType.value = headerSelect.value;
      // Optional: color feedback
      headerSelect.classList.toggle('text-warning', headerSelect.value === 'retest');
    });

    // ? Reindex form row names
    function reindexRows() {
      tableBody.querySelectorAll('tr').forEach((row, i) => {
        row.querySelectorAll('input, select').forEach(input => {
          const oldName = input.name;
          input.name = oldName.replace(/\[\d+\]/, `[${i}]`);
        });
      });
    }

    // ? Update grand total
    function updateGrandTotal() {
      let total = 0;
      tableBody.querySelectorAll('.amount-field').forEach(f => total += parseFloat(f.value) || 0);
      grandTotalEl.textContent = total.toFixed(2);
    }

    function buildStorageLocationOptions() {
  let html = '<option value="">Select</option>';

  STORAGE_LOCATIONS.forEach(sl => {
    html += `<option value="${sl.id}">${sl.name}</option>`;
  });

  return html;
}


    // ? Calculate row totals + auto-fill quantity if blank
    function calculateRowAmount(row) {
      const qtyInput = row.querySelector('.qty-input');
      const rateInput = row.querySelector('.rate-input');
      const capacityInput = row.querySelector('input[name*="[capacity]"]');
      const nocInput = row.querySelector('input[name*="[noc]"]');
      const amountField = row.querySelector('.amount-field');

      const capacity = parseFloat(capacityInput?.value) || 0;
      const noc = parseFloat(nocInput?.value) || 0;
      const rate = parseFloat(rateInput?.value) || 0;
      let qty = parseFloat(qtyInput?.value) || 0;

      // Auto-fill Qty = Capacity � NoC if blank or editing capacity/NOC
      const calcQty = capacity * noc;
      if ((capacity > 0 || noc > 0) && (qty === 0 || document.activeElement === capacityInput || document.activeElement === nocInput)) {
        qty = calcQty;
        qtyInput.value = qty.toFixed(2);
      }

      const amount = (qty * rate).toFixed(2);
      amountField.value = amount;
      updateGrandTotal();
    }

    // ? Listen to input changes
    tableBody.addEventListener('input', e => {
      if (e.target.matches('.qty-input, .rate-input, [name*="[capacity]"], [name*="[noc]"]')) {
        const row = e.target.closest('tr');
        calculateRowAmount(row);
      }
    });

    // ? Remove a row
    tableBody.addEventListener('click', e => {
      const btn = e.target.closest('.remove-row');
      if (!btn) return;
      const rows = tableBody.querySelectorAll('tr');
      if (rows.length > 1) {
        btn.closest('tr').remove();
        reindexRows();
        updateGrandTotal();
      } else {
        showToast('At least one item must remain!', 'warning');
      }
    });

    // ? Add new item row (with MFG + single Expiry/Retest Date)
    addRowBtn.addEventListener('click', () => {
      const idx = tableBody.querySelectorAll('tr').length;
      const unitOptions = `<?php foreach ($units as $u): ?><option value="<?= $u['id'] ?>"><?= esc($u['name']) ?></option><?php endforeach; ?>`;
      const itemOptions = `<?php foreach ($items as $it): ?><option value="<?= $it['id'] ?>"><?= esc($it['name']) ?></option><?php endforeach; ?>`;

      const tr = document.createElement('tr');
      tr.innerHTML = `
      <td class="text-start">
        <select name="items[${idx}][item_id]" class="form-select form-select-sm item-select" required>
          <option value="">Select</option>${itemOptions}
        </select>
      </td>
      <td><input type="text" name="items[${idx}][batch_no]" class="form-control form-control-sm"></td>
      <td><input type="text" name="items[${idx}][lot_no]" class="form-control form-control-sm"></td>
      <td><input type="number" step="0.01" name="items[${idx}][capacity]" class="form-control form-control-sm text-end"></td>
      <td><input type="number" step="1" name="items[${idx}][noc]" class="form-control form-control-sm text-end"></td>
      <td><input type="number" step="0.01" name="items[${idx}][quantity]" class="form-control form-control-sm text-end qty-input"></td>
      <td>
        <select name="items[${idx}][unit_id]" class="form-select form-select-sm unit-dropdown">
          <option value="">Select</option>${unitOptions}
        </select>
      </td>
      <td><input type="number" step="0.01" name="items[${idx}][rate]" class="form-control form-control-sm text-end rate-input"></td>
      <td><input type="number" step="0.01" name="items[${idx}][amount]" class="form-control form-control-sm text-end amount-field" readonly></td>
      <td><input type="date" name="items[${idx}][mfg_date]" class="form-control form-control-sm"></td>
      <td><input type="date" name="items[${idx}][expiry_or_retest]" class="form-control form-control-sm expiry-date"></td>
      <td>
  <select name="items[${idx}][storage_location_id]"
          class="form-select form-select-sm"
          required>
    ${buildStorageLocationOptions()}
  </select>
</td>

<td>
  <input type="text"
         name="items[${idx}][remarks]"
         class="form-control form-control-sm">
</td>

<td>
  <button type="button"
          class="btn btn-sm btn-outline-danger remove-row">
    <i class="bx bx-trash"></i>
  </button>
</td>

    `;
      tableBody.appendChild(tr);
      reindexRows();
      $('.item-select, .unit-dropdown').select2({
        width: '100%'
      });
    });

    // ? Initialize select2 dropdowns
    $('#supplierSelect, #poSelect').select2({
      width: '100%'
    });
    $('.item-select, .unit-dropdown').select2({
      width: '100%'
    });

    // ===============================
    // 🔹 PO → GRN AUTO-FETCH
    // ===============================
    $('#poSelect').on('change', function() {
      const poId = $(this).val();
      if (!poId) return;

      $('#loaderOverlay').removeClass('d-none');

      fetch(`<?= base_url('grn/info') ?>/${poId}`)
        .then(res => res.json())
        .then(data => {

          if (!data.success) {
            showToast('Failed to fetch PO data', 'error');
            return;
          }

          // 1️⃣ Supplier
          if (data.supplier && data.supplier.id) {
            $('#supplierSelect').val(data.supplier.id).trigger('change');
          }

          // 2️⃣ Clear items
          const tbody = document.querySelector('#itemTable tbody');
          tbody.innerHTML = '';

          // 3️⃣ Insert PO items
          data.items.forEach((item, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                    <td class="text-start">
                        <select name="items[${index}][item_id]" class="form-select form-select-sm item-select" required>
                            <option value="${item.item_id}" selected>${item.item_name}</option>
                        </select>
                    </td>
                    <td><input type="text" name="items[${index}][batch_no]" class="form-control form-control-sm"></td>
                    <td><input type="text" name="items[${index}][lot_no]" class="form-control form-control-sm"></td>
                    <td><input type="number" step="0.01" name="items[${index}][capacity]" class="form-control form-control-sm text-end"></td>
                    <td><input type="number" step="1" name="items[${index}][noc]" class="form-control form-control-sm text-end"></td>
                    <td><input type="number" step="0.01" name="items[${index}][quantity]" class="form-control form-control-sm text-end qty-input" value="${item.quantity}"></td>
                    <td>
                        <select name="items[${index}][unit_id]" class="form-select form-select-sm unit-dropdown">
                            <?php foreach ($units as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= esc($u['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="number" step="0.01" name="items[${index}][rate]" class="form-control form-control-sm text-end rate-input" value="${item.rate}"></td>
                    <td><input type="number" step="0.01" name="items[${index}][amount]" class="form-control form-control-sm text-end amount-field" readonly></td>
                    <td><input type="date" name="items[${index}][mfg_date]" class="form-control form-control-sm"></td>
                    <td><input type="date" name="items[${index}][expiry_or_retest]" class="form-control form-control-sm"></td>
                    <td>
  <select name="items[${index}][storage_location_id]"
          class="form-select form-select-sm"
          required>
    ${buildStorageLocationOptions()}
  </select>
</td>

<td>
  <input type="text"
         name="items[${index}][remarks]"
         class="form-control form-control-sm">
</td>

                    <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bx bx-trash"></i></button></td>
                `;
            tbody.appendChild(tr);
          });

          $('.item-select, .unit-dropdown').select2({
            width: '100%'
          });

          document.querySelectorAll('#itemTable tbody tr')
            .forEach(row => calculateRowAmount(row));

          updateGrandTotal();
          showToast('PO data loaded', 'success');
        })
        .catch(() => showToast('Error loading PO', 'error'))
        .finally(() => $('#loaderOverlay').addClass('d-none'));
    });


    // ? Initial calculation for existing rows
    document.querySelectorAll('#itemTable tbody tr').forEach(row => calculateRowAmount(row));
    updateGrandTotal();
  });

  // ? Update the date column label dynamically when switching between Expiry / Retest
  headerSelect.addEventListener('change', () => {
    hiddenType.value = headerSelect.value;

    // Change header text dynamically
    const dateHeader = headerSelect.closest('th');
    dateHeader.querySelector('option[value="expiry"]').textContent = 'EXP DT';
    dateHeader.querySelector('option[value="retest"]').textContent = 'RET DT';

    if (headerSelect.value === 'expiry') {
      headerSelect.classList.remove('text-warning');
      headerSelect.title = 'Expiry Date';
    } else {
      headerSelect.classList.add('text-warning');
      headerSelect.title = 'Retest Date';
    }
  });
</script>




<?= $this->include('layout/footer') ?>