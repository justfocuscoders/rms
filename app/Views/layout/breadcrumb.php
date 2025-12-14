<?php
// --- Get initial server time from DB (in IST) ---
$db = \Config\Database::connect();
$serverTime = $db->query("SELECT CONVERT_TZ(NOW(), '+00:00', '+05:30') AS ist_time")->getRow()->ist_time;
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
  <!-- 🧭 Breadcrumb Links -->
  <nav aria-label="breadcrumb" class="mb-0">
    <ol class="breadcrumb mb-0">
      <?php foreach ($breadcrumbs as $b): ?>
        <li class="breadcrumb-item">
          <?php if (!empty($b['url'])): ?>
            <a href="<?= base_url($b['url']) ?>"><?= esc($b['title']) ?></a>
          <?php else: ?>
            <?= esc($b['title']) ?>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ol>
  </nav>

  <!-- 🕒 Live Server Time (IST) -->
  
    
    <div class="text-end">
      <div id="serverTime" class="fw-semibold text-dark"><?= date('d M Y, h:i:s A', strtotime($serverTime)) ?></div>
      
    </div>
 
</div>



<!-- ⚡ Live ticking JS clock -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const timeEl = document.getElementById('serverTime');

  // Initial time from PHP
  let serverTime = new Date("<?= date('Y-m-d H:i:s', strtotime($serverTime)) ?>");

  function updateClock() {
    serverTime.setSeconds(serverTime.getSeconds() + 1);
    const options = {
      day: '2-digit', month: 'short', year: 'numeric',
      hour: '2-digit', minute: '2-digit', second: '2-digit',
      hour12: true,
      timeZone: 'Asia/Kolkata'
    };
    timeEl.textContent = new Intl.DateTimeFormat('en-IN', options).format(serverTime);
  }

  setInterval(updateClock, 1000);
});
</script>
