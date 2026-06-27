<?php
// ========================================
// DASHBOARD - MAIN PAGE
// With LOW STOCK INDICATORS & FILTER
// ========================================

require_once 'config.php';
requireLogin();

// Get search and filter parameters
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$low_stock_filter = $_GET['stock'] ?? '';

// ========================================
// QUERY: Get products with category, supplier, and user info
// ========================================
$sql = "SELECT p.*, 
               c.name as category_name,
               s.name as supplier_name, 
               u.username as added_by_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN suppliers s ON p.supplier_id = s.id
        LEFT JOIN users u ON p.added_by = u.id
        WHERE 1=1";

// Add search filter
if (!empty($search)) {
    $sql .= " AND (p.name LIKE '%" . $conn->real_escape_string($search) . "%' 
              OR p.description LIKE '%" . $conn->real_escape_string($search) . "%')";
}

// Add category filter
if (!empty($category_filter)) {
    $sql .= " AND c.name = '" . $conn->real_escape_string($category_filter) . "'";
}

// Add low stock filter - shows only products with stock < 20
if (!empty($low_stock_filter) && $low_stock_filter == 'low') {
    $sql .= " AND p.stock < 20";
}

// Order by ID ascending
$sql .= " ORDER BY p.id ASC";
$result = $conn->query($sql);

// ========================================
// QUERY: Get all categories for filter
// ========================================
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");

// ========================================
// QUERY: Get statistics for dashboard
// ========================================
$stats = $conn->query("
    SELECT 
        COUNT(*) as total_products,
        SUM(stock) as total_stock,
        SUM(price * stock) as total_value,
        SUM(CASE WHEN stock < 20 THEN 1 ELSE 0 END) as low_stock_count,
        SUM(CASE WHEN stock < 5 THEN 1 ELSE 0 END) as critical_stock_count
    FROM products
")->fetch_assoc();

$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_suppliers = $conn->query("SELECT COUNT(*) as count FROM suppliers")->fetch_assoc()['count'];
$total_categories = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];

// Get category count for each category
$category_counts = [];
$cat_count_result = $conn->query("
    SELECT c.name, COUNT(p.id) as count 
    FROM categories c 
    LEFT JOIN products p ON c.id = p.category_id 
    GROUP BY c.id
");
while ($row = $cat_count_result->fetch_assoc()) {
    $category_counts[$row['name']] = $row['count'];
}

// ========================================
// QUERY: Get recent activity log
// ========================================
$recent_activity = $conn->query("
    SELECT username, action, table_name, record_id, details, created_at
    FROM activity_log
    ORDER BY created_at DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Marynissa Sari-Sari Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <h1>Welcome, <?= htmlspecialchars(getUsername()) ?>!</h1>
        <p>Manage your inventory efficiently</p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="icon"></span>
            <div class="number"><?= $stats['total_products'] ?? 0 ?></div>
            <div class="label">Total Products</div>
        </div>
        <div class="stat-card">
            <span class="icon"></span>
            <div class="number"><?= $total_categories ?></div>
            <div class="label">Categories</div>
        </div>
        <div class="stat-card">
            <span class="icon"></span>
            <div class="number"><?= number_format($stats['total_stock'] ?? 0) ?></div>
            <div class="label">Total Stock</div>
        </div>
        <div class="stat-card">
            <span class="icon"></span>
            <div class="number">₱<?= number_format($stats['total_value'] ?? 0, 2) ?></div>
            <div class="label">Inventory Value</div>
        </div>
        <div class="stat-card">
            <span class="icon"></span>
            <div class="number"><?= $total_suppliers ?></div>
            <div class="label">Suppliers</div>
        </div>
        <div class="stat-card <?= ($stats['low_stock_count'] > 0) ? 'low-stock-warning' : '' ?>">
            <span class="icon"></span>
            <div class="number" style="color: <?= ($stats['critical_stock_count'] > 0) ? '#d32f2f' : '#ef5350' ?>;">
                <?= $stats['low_stock_count'] ?? 0 ?>
            </div>
            <div class="label">
                Low Stock Items 
                <?php if ($stats['critical_stock_count'] > 0): ?>
                    <span style="color:#d32f2f; font-weight:bold;">(<?= $stats['critical_stock_count'] ?> Critical!)</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions (Admin Only) -->
    <?php if (isAdmin()): ?>
    <div class="quick-actions">
        <a href="add_product.php">
            <span class="icon"></span>
            <span class="label">Add Product</span>
        </a>
        <a href="add_supplier.php">
            <span class="icon"></span>
            <span class="label">Add Supplier</span>
        </a>
        <a href="users.php">
            <span class="icon"></span>
            <span class="label">Manage Users</span>
        </a>
        <a href="activity_log.php">
            <span class="icon"></span>
            <span class="label">Activity Log</span>
        </a>
    </div>
    <?php endif; ?>
    
    <!-- Products Section -->
    <div class="card">
        <div class="top-bar">
            <h1>
                Products
                <?php if (!empty($low_stock_filter) && $low_stock_filter == 'low'): ?>
                    <span style="font-size:0.6em; background:#ef5350; color:#fff; padding:2px 14px; border-radius:20px; margin-left:10px;">
                        Low Stock Filter
                    </span>
                <?php endif; ?>
                <?php if ($stats['low_stock_count'] > 0 && empty($low_stock_filter)): ?>
                    <span style="font-size:0.6em; background:#ef5350; color:#fff; padding:2px 14px; border-radius:20px; margin-left:10px;">
                        <?= $stats['low_stock_count'] ?> low stock
                    </span>
                <?php endif; ?>
            </h1>
            <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                <?php if (isAdmin()): ?>
                    <a href="add_product.php" class="btn-add">+ Add Product</a>
                <?php endif; ?>
                <!-- Low Stock Button - Separated from categories -->
                <?php if ($stats['low_stock_count'] > 0): ?>
                    <a href="?stock=low" class="btn-low-stock <?= (!empty($low_stock_filter) && $low_stock_filter == 'low') ? 'active' : '' ?>">
                        Low Stock (<?= $stats['low_stock_count'] ?>)
                    </a>
                <?php endif; ?>
                <?php if (!empty($low_stock_filter) && $low_stock_filter == 'low'): ?>
                    <a href="index.php" class="btn-clear-low-stock">Clear Filter</a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Category Filter - NO Low Stock Button Here -->
        <div class="category-filter-section">
            <span class="label">Filter by Category:</span>
            <div class="category-list">
                <!-- All Categories Button -->
                <a href="index.php" class="category-btn <?= (empty($category_filter) && empty($low_stock_filter)) ? 'active' : '' ?>">
                    All
                    <span class="count"><?= $stats['total_products'] ?? 0 ?></span>
                </a>
                
                <?php 
                $cat_count_result = $conn->query("
                    SELECT c.name, COUNT(p.id) as count 
                    FROM categories c 
                    LEFT JOIN products p ON c.id = p.category_id 
                    GROUP BY c.id
                ");
                $category_counts = [];
                while ($row = $cat_count_result->fetch_assoc()) {
                    $category_counts[$row['name']] = $row['count'];
                }
                ?>
                
                <?php 
                $categories->data_seek(0); 
                while ($cat = $categories->fetch_assoc()): 
                    $count = $category_counts[$cat['name']] ?? 0;
                ?>
                    <a href="?category=<?= urlencode($cat['name']) ?>" 
                       class="category-btn <?= ($category_filter == $cat['name']) ? 'active' : '' ?>">
                        <?= htmlspecialchars($cat['name']) ?>
                        <span class="count"><?= $count ?></span>
                    </a>
                <?php endwhile; ?>
            </div>
            
            <?php if (!empty($category_filter)): ?>
                <a href="index.php" class="clear-filter">Clear Category</a>
            <?php endif; ?>
        </div>
        
        <!-- Search Bar -->
        <form class="search-bar" method="GET" action="index.php">
            <input type="text" name="search" placeholder="Search products by name or description..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn-search">Search</button>
            <?php if (!empty($search) || !empty($category_filter) || !empty($low_stock_filter)): ?>
                <a href="index.php" class="btn-reset">Reset All</a>
            <?php endif; ?>
        </form>
        
        <!-- Products Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Supplier</th>
                        <th>Added By</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): 
                            $is_low_stock = $row['stock'] < 20;
                            $is_critical = $row['stock'] < 5;
                        ?>
                        <tr class="<?= $is_low_stock ? 'low-stock' : '' ?>">
                            <td><?= $row['id'] ?></td>
                            <td>
                                <?php if ($row['image']): ?>
                                    <img src="uploads/<?= $row['image'] ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="product-img">
                                <?php else: ?>
                                    <span style="color:#ccc; font-size:0.8em;">No image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($row['name']) ?></strong>
                                <?php if ($is_critical): ?>
                                    <span style="display:inline-block; background:#d32f2f; color:#fff; padding:1px 10px; border-radius:12px; font-size:0.65em; font-weight:bold; margin-left:5px;">
                                        CRITICAL
                                    </span>
                                <?php elseif ($is_low_stock): ?>
                                    <span style="display:inline-block; background:#ef5350; color:#fff; padding:1px 10px; border-radius:12px; font-size:0.65em; font-weight:bold; margin-left:5px;">
                                        LOW STOCK
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="max-width:200px; white-space:normal; word-wrap:break-word; font-size:0.85em; color:#666;">
                                <?= htmlspecialchars($row['description'] ?? '') ?>
                            </td>
                            <td>
                                <span class="category-badge" style="background: <?= 
                                    $row['category_name'] == 'Beverages' ? '#2196F3' :
                                    ($row['category_name'] == 'Snacks' ? '#FF9800' :
                                    ($row['category_name'] == 'Canned Goods' ? '#f44336' :
                                    ($row['category_name'] == 'Rice & Grains' ? '#8D6E63' :
                                    ($row['category_name'] == 'Cooking Essentials' ? '#4CAF50' :
                                    ($row['category_name'] == 'Household' ? '#9C27B0' :
                                    ($row['category_name'] == 'Personal Care' ? '#E91E63' : '#888')))))) ?>">
                                    <?= htmlspecialchars($row['category_name'] ?? 'Uncategorized') ?>
                                </span>
                            </td>
                            <td>₱<?= number_format($row['price'], 2) ?></td>
                            <td>
                                <?php if ($is_critical): ?>
                                    <span class="stock-badge critical"><?= $row['stock'] ?></span>
                                <?php elseif ($is_low_stock): ?>
                                    <span class="stock-badge low"><?= $row['stock'] ?></span>
                                <?php else: ?>
                                    <span class="stock-badge normal"><?= $row['stock'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['supplier_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['added_by_name'] ?? 'Unknown') ?></td>
                            <td style="font-size:0.8em; color:#888;">
                                <?= date('M d, Y h:i A', strtotime($row['created_at'])) ?>
                            </td>
                            <td>
                                <?php if (isAdmin()): ?>
                                    <div class="action-buttons">
                                        <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                                        <a href="delete_product.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Delete this product?')">Delete</a>
                                    </div>
                                <?php else: ?>
                                    <span class="view-only">View only</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" style="text-align:center; padding:40px; color:#999;">
                                <?php if (!empty($low_stock_filter) && $low_stock_filter == 'low'): ?>
                                    No low stock items. All products have sufficient stock.
                                <?php else: ?>
                                    No products found.
                                <?php endif; ?>
                                <?php if (isAdmin()): ?>
                                    <br><a href="add_product.php" style="color:#4CAF50;">Add a new product</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p class="count">Total: <?= $result->num_rows ?> product(s) 
            <?php if (!empty($low_stock_filter) && $low_stock_filter == 'low'): ?>
                <span style="color:#ef5350; font-weight:bold; margin-left:15px;">
                    Showing low stock items only
                </span>
            <?php elseif ($stats['low_stock_count'] > 0): ?>
                <span style="color:#ef5350; font-weight:bold; margin-left:15px;">
                    <?= $stats['low_stock_count'] ?> low stock
                    <?php if ($stats['critical_stock_count'] > 0): ?>
                        <span style="color:#d32f2f;">(<?= $stats['critical_stock_count'] ?> critical!)</span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </p>
    </div>
    
    <!-- ========================================
         RECENT ACTIVITY LOG SECTION
         ======================================== -->
    <?php if (isAdmin() && $recent_activity->num_rows > 0): ?>
    <div class="card">
        <div class="top-bar">
            <h1>Recent Activity</h1>
            <a href="activity_log.php" class="btn-add">View All</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Table</th>
                        <th>Details</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($log = $recent_activity->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($log['username']) ?></strong></td>
                        <td>
                            <span style="padding:2px 12px; border-radius:12px; font-size:0.75em; font-weight:600; 
                                <?= $log['action'] == 'ADD' ? 'background:#e8f5e9; color:#1a472a;' : '' ?>
                                <?= $log['action'] == 'EDIT' ? 'background:#fff3e0; color:#e65100;' : '' ?>
                                <?= $log['action'] == 'DELETE' ? 'background:#ffebee; color:#c62828;' : '' ?>
                                <?= $log['action'] == 'LOGIN' ? 'background:#e3f2fd; color:#0d47a1;' : '' ?>
                            ">
                                <?= htmlspecialchars($log['action']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($log['table_name']) ?></td>
                        <td style="font-size:0.85em; color:#666;">
                            <?= htmlspecialchars($log['details'] ?? '') ?>
                        </td>
                        <td style="font-size:0.75em; color:#888;">
                            <?= date('M d, Y h:i A', strtotime($log['created_at'])) ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- ========================================
     JAVASCRIPT: Save and restore scroll position
     ======================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Restore scroll position if saved
    const savedScroll = sessionStorage.getItem('scrollPosition');
    if (savedScroll) {
        window.scrollTo(0, parseInt(savedScroll));
        sessionStorage.removeItem('scrollPosition');
    }
    
    const categoryLinks = document.querySelectorAll('.category-btn, .clear-filter, .btn-low-stock, .btn-clear-low-stock');
    
    categoryLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            // Save current scroll position
            sessionStorage.setItem('scrollPosition', window.scrollY);
            window.location.href = this.getAttribute('href');
        });
    });
});
</script>

</body>
</html>