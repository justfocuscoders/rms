<?php echo view('layout/header'); ?>
<?php echo view('layout/sidebar'); ?>

<!-- Content -->
<div class="content">
  <div class="row g-3">
    <div class="col-md-2">
      <div class="card-box bg-light">
        <p>Pending Sale Orders</p>
        <h5 class="text-primary">3</h5>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card-box bg-light">
        <p>Completed Sale Orders</p>
        <h5 class="text-success">0</h5>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card-box bg-light">
        <p>Payment Receivables</p>
        <h5 class="text-danger">42,100.00</h5>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card-box bg-light">
        <p>Payment Payables</p>
        <h5 class="text-warning">75,000.00</h5>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card-box bg-light">
        <p>Total Expense</p>
        <h5 class="text-danger">8,500.00</h5>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card-box bg-light">
        <p>Total Customers</p>
        <h5 class="text-warning">8</h5>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-6">
      <div class="card p-3 shadow-sm">
        <h6>Sale vs Purchase</h6>
        <canvas id="salePurchaseChart"></canvas>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card p-3 shadow-sm">
        <h6>Trending Items</h6>
        <canvas id="trendingChart"></canvas>
      </div>
    </div>
  </div>
</div>

<?php echo view('layout/footer'); ?>
