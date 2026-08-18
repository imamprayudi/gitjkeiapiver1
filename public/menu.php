<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$level = $_SESSION['level'];

if ($level == 3) {
    $portalName = "Supplier Portal";
} else {
    $portalName = "JKEI Portal";
}

$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
$userName = htmlspecialchars($_SESSION['user'] ?? '', ENT_QUOTES, 'UTF-8');

if (!function_exists('menuIsActive')) {
    function menuIsActive($pages) {
        global $currentPage;
        return in_array($currentPage, (array)$pages, true);
    }
}

if (!function_exists('menuItemClass')) {
    function menuItemClass($page) {
        $cls = 'jkei-link';
        if (menuIsActive($page)) {
            $cls .= ' active';
        }
        return $cls;
    }
}

$forecastPages = ['forecast.php', 'forecastarc.php'];
$orderPages = ['dashboardmailpoall.php', 'pohist.php', 'mailportgl.php', 'mailpotgl.php', 'mailpoctgl.php', 'ob.php'];
$schedulePages = ['tds.php', 'bps.php'];
$materialPages = ['matsum.php', 'matrec.php', 'matiss.php'];
$soaPages = ['soa.php', 'soamid.php', 'soaend.php'];
$deliveryPages = ['diget.php', 'diedit.php', 'diview.php', 'barcodelist.php'];
?>

<style>
.jkei-topbar{
    position:fixed;
    top:0;
    left:0;
    right:0;
    height:56px;
    z-index:1050;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 14px;
    background:#fff;
    box-shadow:0 2px 12px rgba(15,23,42,.08);
    font-family:Inter,system-ui,-apple-system,sans-serif;
}
.jkei-topbar-left{
    display:flex;
    align-items:center;
    gap:10px;
}
.jkei-toggle{
    width:38px;
    height:38px;
    border:0;
    border-radius:8px;
    background:#e0f4ff;
    color:#0077b6;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
}
.jkei-toggle:hover{
    background:#0077b6;
    color:#fff;
}
.jkei-brand{
    display:flex;
    align-items:center;
    gap:8px;
    text-decoration:none;
    color:#0f172a;
    font-weight:600;
}
.jkei-brand:hover{
    color:#0077b6;
}
.jkei-user{
    display:flex;
    align-items:center;
    gap:8px;
    text-decoration:none;
    color:#334155;
    font-weight:500;
    font-size:14px;
    padding:4px 10px 4px 4px;
    border-radius:999px;
}
.jkei-user:hover{
    background:#f1f5f9;
    color:#0077b6;
}
.jkei-user-avatar{
    width:28px;
    height:28px;
    border-radius:50%;
    background:#0077b6;
    display:inline-flex;
    align-items:center;
    justify-content:center;
}
.jkei-sidebar{
    position:fixed;
    top:56px;
    left:0;
    width:260px;
    height:calc(100vh - 56px);
    z-index:1040;
    background:linear-gradient(180deg,#006994 0%,#0077b6 55%,#0390c8 100%);
    color:#fff;
    overflow-y:auto;
    transform:translateX(-100%);
    transition:transform .25s ease;
    font-family:Inter,system-ui,-apple-system,sans-serif;
    padding:12px 10px 24px;
}
body.jkei-sidebar-open .jkei-sidebar{
    transform:translateX(0);
}
body{
    padding-top:56px;
    transition:padding-left .25s ease;
}
@media (min-width:992px){
    body.jkei-sidebar-open{
        padding-left:260px;
    }
}
.jkei-sidebar-backdrop{
    display:none;
    position:fixed;
    inset:56px 0 0 0;
    background:rgba(15,23,42,.4);
    z-index:1035;
}
@media (max-width:991.98px){
    body.jkei-sidebar-open .jkei-sidebar-backdrop{
        display:block;
    }
}
.jkei-link{
    display:block;
    color:rgba(255,255,255,.9);
    text-decoration:none;
    font-size:13px;
    font-weight:500;
    padding:8px 12px;
    border-radius:8px;
    margin:2px 0;
}
.jkei-link:hover,
.jkei-link.active{
    background:rgba(255,255,255,.18);
    color:#fff;
}
.jkei-group{
    margin:4px 0;
}
.jkei-group > summary{
    list-style:none;
    cursor:pointer;
    font-size:13px;
    font-weight:600;
    padding:9px 12px;
    border-radius:8px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    color:#fff;
}
.jkei-group > summary::-webkit-details-marker{
    display:none;
}
.jkei-group > summary:hover,
.jkei-group[open] > summary{
    background:rgba(255,255,255,.12);
}
.jkei-group > summary::after{
    content:"";
    width:6px;
    height:6px;
    border-right:2px solid rgba(255,255,255,.8);
    border-bottom:2px solid rgba(255,255,255,.8);
    transform:rotate(-45deg);
    transition:transform .15s ease;
}
.jkei-group[open] > summary::after{
    transform:rotate(45deg);
}
.jkei-group-body{
    padding:2px 0 6px 8px;
}
.jkei-topbar .dropdown-menu{
    border:0;
    border-radius:10px;
    box-shadow:0 12px 30px rgba(15,23,42,.16);
    padding:8px;
}
.jkei-topbar .dropdown-item{
    border-radius:6px;
    font-size:13px;
}
.jkei-topbar .dropdown-item:hover,
.jkei-topbar .dropdown-item.active{
    background:#e0f4ff;
    color:#0077b6;
}
.jkei-topbar .dropdown-item.text-danger:hover{
    background:#fee2e2;
    color:#dc3545;
}
</style>

<header class="jkei-topbar">
  <div class="jkei-topbar-left">
    <button type="button" class="jkei-toggle" id="jkeiSidebarToggle" aria-label="Toggle menu">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 010-1h11a.5.5 0 010 1h-11zm0-4a.5.5 0 010-1h11a.5.5 0 010 1h-11zm0-4a.5.5 0 010-1h11a.5.5 0 010 1h-11z"/>
      </svg>
    </button>
    <a class="jkei-brand" href="dashboard.php">
      <img src="assets/gambar/g-green.png" width="28" height="28" alt="">
      <?php echo $portalName; ?>
    </a>
  </div>

  <div class="dropdown">
    <a class="jkei-user dropdown-toggle" href="#" data-bs-toggle="dropdown">
      <span class="jkei-user-avatar">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="white" viewBox="0 0 16 16">
          <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 100-6 3 3 0 000 6z"/>
        </svg>
      </span>
      <?php echo $userName; ?>
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item<?php echo menuIsActive('profile.php') ? ' active' : ''; ?>" href="profile.php">Profile</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item text-danger" href="logoff.php">Logoff</a></li>
    </ul>
  </div>
</header>

<aside class="jkei-sidebar" id="jkeiSidebar">
  <a class="<?php echo menuItemClass('dashboard.php'); ?>" href="dashboard.php">Home</a>

  <details class="jkei-group" <?php echo menuIsActive($forecastPages) ? 'open' : ''; ?>>
    <summary>Forecast</summary>
    <div class="jkei-group-body">
      <a class="<?php echo menuItemClass('forecast.php'); ?>" href="forecast.php">Forecast</a>
      <a class="<?php echo menuItemClass('forecastarc.php'); ?>" href="forecastarc.php">Forecast Archived</a>
    </div>
  </details>

  <details class="jkei-group" <?php echo menuIsActive($orderPages) ? 'open' : ''; ?>>
    <summary>Orders</summary>
    <div class="jkei-group-body">
      <a class="<?php echo menuItemClass('dashboardmailpoall.php'); ?>" href="dashboardmailpoall.php">Purchase Order Dashboard</a>
      <a class="<?php echo menuItemClass('pohist.php'); ?>" href="pohist.php">Find Purchase Order History</a>
      <a class="<?php echo menuItemClass('mailportgl.php'); ?>" href="mailportgl.php">Purchase Order Change History</a>
      <a class="<?php echo menuItemClass('mailpotgl.php'); ?>" href="mailpotgl.php">Purchase Order</a>
      <a class="<?php echo menuItemClass('mailpoctgl.php'); ?>" href="mailpoctgl.php">Purchase Order Change</a>
      <a class="<?php echo menuItemClass('ob.php'); ?>" href="ob.php">Order Balance</a>
    </div>
  </details>

  <details class="jkei-group" <?php echo menuIsActive($schedulePages) ? 'open' : ''; ?>>
    <summary>Schedule</summary>
    <div class="jkei-group-body">
      <a class="<?php echo menuItemClass('tds.php'); ?>" href="tds.php">Time Delivery Schedule</a>
      <a class="<?php echo menuItemClass('bps.php'); ?>" href="bps.php">Big Part Schedule</a>
    </div>
  </details>

  <details class="jkei-group" <?php echo menuIsActive($materialPages) ? 'open' : ''; ?>>
    <summary>Material</summary>
    <div class="jkei-group-body">
      <a class="<?php echo menuItemClass('matsum.php'); ?>" href="matsum.php">Summary</a>
      <a class="<?php echo menuItemClass('matrec.php'); ?>" href="matrec.php">Received Detail</a>
      <a class="<?php echo menuItemClass('matiss.php'); ?>" href="matiss.php">Issued Detail</a>
    </div>
  </details>

  <details class="jkei-group" <?php echo menuIsActive($soaPages) ? 'open' : ''; ?>>
    <summary>Statement of Account</summary>
    <div class="jkei-group-body">
      <a class="<?php echo menuItemClass('soa.php'); ?>" href="soa.php">Detail</a>
      <a class="<?php echo menuItemClass('soamid.php'); ?>" href="soamid.php">Mid</a>
      <a class="<?php echo menuItemClass('soaend.php'); ?>" href="soaend.php">End</a>
    </div>
  </details>

  <details class="jkei-group" <?php echo menuIsActive($deliveryPages) ? 'open' : ''; ?>>
    <summary>Delivery</summary>
    <div class="jkei-group-body">
      <a class="<?php echo menuItemClass('diget.php'); ?>" href="diget.php">Get Delivery Instructions</a>
      <a class="<?php echo menuItemClass('diedit.php'); ?>" href="diedit.php">Edit Delivery Instructions</a>
      <a class="<?php echo menuItemClass('diview.php'); ?>" href="diview.php">View Delivery Instructions</a>
      <a class="jkei-link" href="#">Standard Packing Maintenance</a>
      <a class="<?php echo menuItemClass('barcodelist.php'); ?>" href="barcodelist.php">Print Barcode Label</a>
    </div>
  </details>
</aside>

<div class="jkei-sidebar-backdrop" id="jkeiSidebarBackdrop"></div>

<script>
(function () {
  var body = document.body;
  var storageKey = "jkeiSidebarHidden";

  function isMobile() {
    return window.innerWidth < 992;
  }

  function setOpen(open) {
    body.classList.toggle("jkei-sidebar-open", open);
    if (!isMobile()) {
      localStorage.setItem(storageKey, open ? "0" : "1");
    }
  }

  function init() {
    if (isMobile()) {
      setOpen(false);
    } else {
      setOpen(localStorage.getItem(storageKey) !== "1");
    }
  }

  init();

  var toggle = document.getElementById("jkeiSidebarToggle");
  var backdrop = document.getElementById("jkeiSidebarBackdrop");
  if (toggle) {
    toggle.addEventListener("click", function () {
      setOpen(!body.classList.contains("jkei-sidebar-open"));
    });
  }
  if (backdrop) {
    backdrop.addEventListener("click", function () {
      setOpen(false);
    });
  }
  window.addEventListener("resize", function () {
    if (isMobile()) {
      body.classList.remove("jkei-sidebar-open");
    } else if (localStorage.getItem(storageKey) !== "1") {
      body.classList.add("jkei-sidebar-open");
    }
  });
})();
</script>
