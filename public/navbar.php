<?php
// Get the current script name to toggle active class
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-light bg-warning-subtle shadow-sm">
    <div class="container-fluid ">
        <a class="navbar-brand fw-bold text-light" href="../public/menus.php">
            <!-- <img src="../public/assets/logo.png" alt="Mini Cafe POS" height="40" class="d-inline-block align-text-top"> -->
            Cafe Devo
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-light <?php echo ($current_page == 'menus.php') ? 'active' : ''; ?>" href="../public/menus.php">Menus</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light <?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>" href="../public/orders.php">Orders</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['username'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-light" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Welcome, <?php echo $_SESSION['username']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="../admin/logout.php">Log Out</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<style>
    .navbar {
        background-color: #453e3b !important;
        border-bottom: 2px solid #eee;
    }
    .navbar-brand img {
        margin-right: 10px;
    }
    .navbar-brand {
        font-size: 1.5rem;
        font-weight: bold;
    }
    .nav-link {
        font-size: 1.1rem;
        padding-right: 20px;
    }
    .nav-link.active {
        color: #007bff !important;
        font-weight: bold;
    }
    .nav-item:hover .nav-link {
        color: #0056b3 !important;
    }
    .dropdown-toggle::after {
        margin-left: 0.3rem;
    }
    .dropdown-menu {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>