<?php // app/Views/Pager/bootstrap_full.php
/**
 * Bootstrap 5 pagination template for CodeIgniter Pager
 *
 * Usage: $pager->links('default', 'bootstrap_full')
 *
 * Shows a sliding window of up to 5 page numbers (current page centered when possible).
 */

if ($pager->getPageCount() <= 1) {
    return;
}

// show 2 links on either side -> total up to 5 numbered links
$pager->setSurroundCount(2);
?>
<nav aria-label="Page navigation">
  <ul class="pagination mb-0 align-items-center">

    <!-- First / Prev -->
    <?php if ($pager->hasPrevious()): ?>
      <li class="page-item">
        <a class="page-link" href="<?= $pager->getFirst() ?>" aria-label="First" title="First">
          <span aria-hidden="true">&laquo;&laquo;</span>
        </a>
      </li>
      <li class="page-item">
        <a class="page-link" href="<?= $pager->getPrevious() ?>" aria-label="Previous" title="Previous">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>
    <?php else: ?>
      <li class="page-item disabled"><span class="page-link">&laquo;&laquo;</span></li>
      <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
    <?php endif; ?>

    <!-- Numbered links (window is handled by setSurroundCount) -->
    <?php foreach ($pager->links() as $link): ?>
      <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
        <?php if ($link['active']): ?>
          <span class="page-link"><?= $link['title'] ?></span>
        <?php else: ?>
          <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>

    <!-- Next / Last -->
    <?php if ($pager->hasNext()): ?>
      <li class="page-item">
        <a class="page-link" href="<?= $pager->getNext() ?>" aria-label="Next" title="Next">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>
      <li class="page-item">
        <a class="page-link" href="<?= $pager->getLast() ?>" aria-label="Last" title="Last">
          <span aria-hidden="true">&raquo;&raquo;</span>
        </a>
      </li>
    <?php else: ?>
      <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
      <li class="page-item disabled"><span class="page-link">&raquo;&raquo;</span></li>
    <?php endif; ?>

  </ul>
</nav>

<style>
/* small style improvements */
.pagination .page-link { border-radius: 999px; padding: .35rem .65rem; min-width:40px; text-align:center; }
.pagination .page-item.active .page-link { background: linear-gradient(90deg,#0d6efd,#6610f2); border-color: transparent; color:#fff; }
</style>
