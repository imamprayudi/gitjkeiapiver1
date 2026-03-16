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
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container-fluid">

    <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
      <img src="assets/gambar/g-green.png" width="30" height="30" class="me-2">
      <?php echo $portalName; ?>
    </a>

    <!-- BUTTON MOBILE -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">

      <!-- MENU KIRI -->
      <ul class="navbar-nav">

        <li class="nav-item">
          <a class="nav-link active" href="dashboard.php">Home</a>
        </li>

        <!-- Forecast -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
            Forecast
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="forecast.php">Forecast</a></li>
            <li><a class="dropdown-item" href="forecastarc.php">Forecast Archived</a></li>
          </ul>
        </li>

        <!-- Orders -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
            Orders
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="mailpotgl.php">Purchase Order</a></li>
            <li><a class="dropdown-item" href="mailpoctgl.php">Purchase Order Change</a></li>
            <li><a class="dropdown-item" href="ob.php">Order Balance</a></li>
          </ul>
        </li>

        <!-- Schedule -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
            Schedule
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="tds.php">Time Delivery Schedule</a></li>
            <li><a class="dropdown-item" href="bps.php">Big Part Schedule</a></li>
          </ul>
        </li>

        <!-- Material -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
            Material
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="matsum.php">Summary</a></li>
            <li><a class="dropdown-item" href="matrec.php">Received Detail</a></li>
            <li><a class="dropdown-item" href="matiss.php">Issued Detail</a></li>
          </ul>
        </li>

        <!-- SOA -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
            Statement of Account
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="soa.php">Detail</a></li>
            <li><a class="dropdown-item" href="soamid.php">Mid</a></li>
            <li><a class="dropdown-item" href="soaend.php">End</a></li>
          </ul>
        </li>

        <!-- Delivery -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
            Delivery
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Delivery Instructions</a></li>
            <li><a class="dropdown-item" href="#">Standard Packing Maintenance</a></li>
            <li><a class="dropdown-item" href="barcodelist.php">Print Barcode Label</a></li>
          </ul>
        </li>

      </ul>

      <!-- USER MENU KANAN -->
      <ul class="navbar-nav ms-auto">

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">

            <!-- ICON USER -->
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white" class="me-2" viewBox="0 0 16 16">
              <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 100-6 3 3 0 000 6z"/>
            </svg>

            <?php echo $_SESSION['user']; ?>

          </a>

          <ul class="dropdown-menu dropdown-menu-end">

            <li>
              <a class="dropdown-item" href="#">
                Profile
              </a>
            </li>

            <li><hr class="dropdown-divider"></li>

            <li>
              <a class="dropdown-item text-danger" href="logoff.php">
                Logoff
              </a>
            </li>

          </ul>

        </li>

      </ul>

    </div>
  </div>
</nav>