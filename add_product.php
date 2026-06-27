<?php
// ========================================
// ADD PRODUCT PAGE
// Admin only - with category selection
// ========================================

require_once 'config.php';
requireAdmin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string(trim($_POST['name'] ?? ''));
    $description = $conn->real_escape_string(trim($_POST['description'] ?? ''));
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $category_id = $_POST['category_id'] ?? '';
    $supplier_id = $_POST['supplier_id'] ?? '';
    
    // Image upload handling
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($image_file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image = $image_name;
            }
        }
    }
    
    if (empty($name) || empty($price)) {
        $message = "<div class='alert-error'>Name and price are required!</div>";
    } else {
        $added_by = $_SESSION['user_id'];
        
        $sql = "INSERT INTO products (name, description, price, stock, category_id, supplier_id, added_by, image)
                VALUES ('$name', '$description', $price, $stock, '$category_id', '$supplier_id', $added_by, '$image')";
        
        if ($conn->query($sql)) {
            // Log the activity
            $username = $_SESSION['username'];
            $product_id = $conn->insert_id;
            $log_sql = "INSERT INTO activity_log (user_id, username, action, table_name, record_id, details) 
                        VALUES ($added_by, '$username', 'ADD', 'products', $product_id, 'Added product: $name')";
            $conn->query($log_sql);
            
            header('Location: index.php?added=1');
            exit;
        } else {
            $message = "<div class='alert-error'>Error: " . $conn->error . "</div>";
        }
    }
}

$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
$suppliers = $conn->query("SELECT id, name FROM suppliers ORDER BY name");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Product - Sari-Sari Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="card form-page">
        <h1 class="page-title">Add New Product</h1>
        <p class="page-subtitle">Fill in the details to add a new product to inventory</p>
        
        <?= $message ?>
        
        <form method="POST" enctype="multipart/form-data">
            <!-- Product Name -->
            <label>Product Name <span class="required">*</span></label>
            <input type="text" name="name" placeholder="e.g. Coca-Cola 1.5L" required>
            
            <!-- Description -->
            <label>Description</label>
            <textarea name="description" rows="3" placeholder="Brief description of the product"></textarea>
            
            <!-- Category -->
            <label>Category <span class="required">*</span></label>
            <select name="category_id" required>
                <option value="">-- Select Category --</option>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endwhile; ?>
            </select>
            
            <!-- Price -->
            <label>Price (₱) <span class="required">*</span></label>
            <input type="number" name="price" step="0.01" min="0" placeholder="0.00" required>
            
            <!-- Stock -->
            <label>Stock Quantity</label>
            <input type="number" name="stock" min="0" value="0" placeholder="0">
            
            <!-- Supplier -->
            <label>Supplier</label>
            <select name="supplier_id">
                <option value="">-- Select Supplier --</option>
                <?php while ($sup = $suppliers->fetch_assoc()): ?>
                    <option value="<?= $sup['id'] ?>"><?= htmlspecialchars($sup['name']) ?></option>
                <?php endwhile; ?>
            </select>
            
            <!-- Product Image -->
            <label>Product Image</label>
            <input type="file" name="image" accept="image/*">
            <div class="hint">Supported: JPG, PNG, GIF, WebP (Max: 2MB)</div>
            
            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">Add Product</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>