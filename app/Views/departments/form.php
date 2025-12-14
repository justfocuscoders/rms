<?= $this->include('layout/header') ?>
<div class="container-fluid pt-3 pb-5">
<div class="content p-5">
  <h3><?= $department ? 'Edit Department' : 'Add Department' ?></h3>

  <form method="post" action="<?= base_url('/departments/save'.($department['id'] ?? '')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $department['id'] ?? '' ?>">

    <div class="mb-3">
      <label>Name</label>
      <input type="text" name="name" class="form-control"
             value="<?= esc($department['name'] ?? '') ?>" required>
    </div>

    <button type="submit" class="btn btn-success">Save</button>
    <a href="<?= base_url('/departments/list') ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</div>
<?= $this->include('layout/footer') ?>
