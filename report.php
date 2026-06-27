<?php
// ========================================
// REPORT PAGE
// Shows inventory statistics and breakdowns
// ========================================

require_once 'config.php';
requireLogin();

$username = getUsername();

// ========================================
// OVERALL SUMMARY
// ========================================
$summary = $conn->query("
    SELECT
        COUNT(*) AS total_products,
        SUM(stock) AS total_stock,
        SUM(price * stock) AS total_value,
        AVG(price) AS avg_price,
        SUM(CASE WHEN stock < 20 THEN 1 ELSE 0 END) AS low_stock
    FROM products
")->fetch_assoc();

// ========================================
// PER-CATEGORY BREAKDOWN
// ========================================
$by_category = $conn->query("
    SELECT
        c.name AS category,
        COUNT(p.id) AS product_count,
        COALESCE(SUM(p.stock), 0) AS total_stock,
        COALESCE(SUM(p.price * p.stock), 0) AS total_value,
        COALESCE(AVG(p.price), 0) AS avg_price
    FROM categories c
    LEFT JOIN products p ON c.id = p.category_id
    GROUP BY c.id, c.name
    ORDER BY total_value DESC
");

// ========================================
// PER-SUPPLIER BREAKDOWN
// ========================================
$by_supplier = $conn->query("
    SELECT
        s.name AS supplier,
        s.contact_person,
        s.phone,
        COUNT(p.id) AS product_count,
        COALESCE(SUM(p.stock), 0) AS total_stock,
        COALESCE(SUM(p.price * p.stock), 0) AS total_value
    FROM suppliers s
    LEFT JOIN products p ON s.id = p.supplier_id
    GROUP BY s.id, s.name, s.contact_person, s.phone
    ORDER BY total_value DESC
");

// ========================================
// RECENT PRODUCTS (Last 5 added)
// ========================================
$recent_products = $conn->query("
    SELECT p.name, p.price, p.stock, c.name as category, u.username as added_by
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN users u ON p.added_by = u.id
    ORDER BY p.id DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reports - Marynissa Sari-Sari Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="card">
        <h1 class="page-title">Inventory Reports</h1>
        <p class="page-subtitle">Overview of your inventory statistics and breakdowns</p>
        
        <!-- ========================================
             REPORT SUMMARY CARDS
             ======================================== -->
        <div class="report-summary">
            <div class="stat-box green">
                <div class="number"><?= $summary['total_products'] ?? 0 ?></div>
                <div class="label">Total Products</div>
            </div>
            <div class="stat-box blue">
                <div class="number"><?= number_format($summary['total_stock'] ?? 0) ?></div>
                <div class="label">Total Stock</div>
            </div>
            <div class="stat-box orange">
                <div class="number">₱<?= number_format($summary['total_value'] ?? 0, 2) ?></div>
                <div class="label">Inventory Value</div>
            </div>
            <div class="stat-box purple">
                <div class="number">₱<?= number_format($summary['avg_price'] ?? 0, 2) ?></div>
                <div class="label">Average Price</div>
            </div>
            <div class="stat-box red">
                <div class="number"><?= $summary['low_stock'] ?? 0 ?></div>
                <div class="label">Low Stock Items (&lt;20)</div>
            </div>
        </div>
        
        <!-- ========================================
             CATEGORY BREAKDOWN
             ======================================== -->
        <div class="report-section">
            <h2>By Category</h2>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Products</th>
                        <th>Total Stock</th>
                        <th>Avg Price</th>
                        <th>Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($by_category->num_rows > 0): ?>
                        <?php while ($row = $by_category->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['category']) ?></strong></td>
                            <td><?= $row['product_count'] ?></td>
                            <td><?= number_format($row['total_stock']) ?></td>
                            <td>₱<?= number_format($row['avg_price'], 2) ?></td>
                            <td class="value">₱<?= number_format($row['total_value'], 2) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="no-data">No data available</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- ========================================
             SUPPLIER BREAKDOWN
             ======================================== -->
        <div class="report-section">
            <h2>By Supplier</h2>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Products</th>
                        <th>Total Stock</th>
                        <th>Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($by_supplier->num_rows > 0): ?>
                        <?php while ($row = $by_supplier->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['supplier']) ?></strong></td>
                            <td><?= htmlspecialchars($row['contact_person']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= $row['product_count'] ?></td>
                            <td><?= number_format($row['total_stock']) ?></td>
                            <td class="value">₱<?= number_format($row['total_value'], 2) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="no-data">No data available</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- ========================================
             RECENT PRODUCTS
             ======================================== -->
        <div class="report-section">
            <h2>Recently Added Products</h2>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Added By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent_products->num_rows > 0): ?>
                        <?php while ($row = $recent_products->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                            <td><?= htmlspecialchars($row['category'] ?? 'Uncategorized') ?></td>
                            <td>₱<?= number_format($row['price'], 2) ?></td>
                            <td><?= $row['stock'] ?></td>
                            <td><?= htmlspecialchars($row['added_by'] ?? 'Unknown') ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="no-data">No products added yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- ========================================
             FOOTER WITH BACK BUTTON
             ======================================== -->
        <div class="report-footer">
            <a href="index.php" class="btn-back">← Back to Dashboard</a>
        </div>
    </div>
</div>
</body>
</html>