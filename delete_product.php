<?php
// ========================================
// DELETE PRODUCT PAGE
// Admin only - with confirmation
// ========================================

require_once 'config.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $conn->query("SELECT image, name FROM products WHERE id = $id");
    $product = $result->fetch_assoc();
    
    if ($product['image'] && file_exists("uploads/" . $product['image'])) {
        unlink("uploads/" . $product['image']);
    }
    
    $conn->query("DELETE FROM products WHERE id = $id");
    
    // Log the activity
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $log_sql = "INSERT INTO activity_log (user_id, username, action, table_name, record_id, details) 
                VALUES ($user_id, '$username', 'DELETE', 'products', $id, 'Deleted product: " . $product['name'] . "')";
    $conn->query($log_sql);
    
    header('Location: index.php?deleted=1');
    exit;
}

$result = $conn->query("SELECT p.*, c.name as category_name FROM products p 
                        LEFT JOIN categories c ON p.category_id = c.id 
                        WHERE p.id = $id");
$product = $result->fetch_assoc();

if (!$product) {
    die("Product not found.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Product - Sari-Sari Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="card" style="text-align:center; max-width:500px; margin:0 auto;">
        <h1 class="page-title">Delete Product</h1>
        
        <p style="font-size:1.1em; margin:25px 0 10px; color:#555;">
            Are you sure you want to delete:
        </p>
        <p style="font-size:1.6em; font-weight:bold; color:#1a472a;">
            <?= htmlspecialchars($product['name']) ?>
        </p>
        
        <?php if ($product['image']): ?>
            <img src="uploads/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width:100px; height:100px; object-fit:cover; border-radius:8px; margin:10px 0;">
        <?php endif; ?>
        
        <p style="color:#888; font-size:0.95em;">
            Category: <?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?>
            | Price: ₱<?= number_format($product['price'], 2) ?> 
            | Stock: <?= $product['stock'] ?>
        </p>
        
        <p style="color:#d32f2f; font-weight:bold; margin:25px 0;">
            This action cannot be undone!
        </p>
        
        <form method="POST" style="display:inline;">
            <button type="submit" style="padding:12px 35px; background:#d32f2f; color:white; border:none; border-radius:8px; cursor:pointer; font-size:1em; font-weight:bold;">
                Yes, Delete
            </button>
        </form>
        <a href="index.php" style="display:inline-block; padding:12px 35px; background:#e0e0e0; color:#555; text-decoration:none; border-radius:8px; margin-left:10px; font-weight:600;">
            Cancel
        </a>
    </div>
</div>
</body>
</html>