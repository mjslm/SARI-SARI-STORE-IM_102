<?php
// ========================================
// NAVIGATION BAR
// This file is included in all pages
// Shows different links based on user role
// ========================================

// Get current page name to highlight active link
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar">
    <!-- Store Brand / Logo -->
    <div class="brand">
        Marynissa Sari-Sari Store
    </div>
    
    <!-- Navigation Links -->
    <div class="nav-links">
        <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Products</a>
        <a href="suppliers.php" class="<?= $current_page == 'suppliers.php' ? 'active' : '' ?>">Suppliers</a>
        <a href="report.php" class="<?= $current_page == 'report.php' ? 'active' : '' ?>">Reports</a>

        <!-- ========================================
             ADMIN ONLY LINKS
             These links are only visible to admin users
             Staff users cannot see these links
             ======================================== -->
        <?php if (isAdmin()): ?>
            <a href="add_product.php" class="<?= $current_page == 'add_product.php' ? 'active' : '' ?>">Add Product</a>
            <a href="add_supplier.php" class="<?= $current_page == 'add_supplier.php' ? 'active' : '' ?>">Add Supplier</a>
            <a href="users.php" class="<?= $current_page == 'users.php' ? 'active' : '' ?>">Users</a>
        <?php endif; ?>
    </div>
    
    <!-- User Info Section -->
    <div class="user-section">
        <span><?= getUsername() ?></span>
        <span class="role-badge <?= isAdmin() ? 'admin' : 'staff' ?>">
            <?= $_SESSION['role'] ?>
        </span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</nav>