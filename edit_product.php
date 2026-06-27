<?php
// ========================================
// EDIT PRODUCT PAGE
// Admin only - with category selection
// ========================================

require_once 'config.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);

$result = $conn->query("SELECT * FROM products WHERE id = $id");
$product = $result->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string(trim($_POST['name'] ?? ''));
    $description = $conn->real_escape_string(trim($_POST['description'] ?? ''));
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $category_id = $_POST['category_id'] ?? '';
    $supplier_id = $_POST['supplier_id'] ?? '';
    
    $image = $product['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        if ($product['image'] && file_exists($target_dir . $product['image'])) {
            unlink($target_dir . $product['image']);
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
        $sql = "UPDATE products SET 
                name='$name', 
                description='$description', 
                price=$price, 
                stock=$stock, 
                category_id='$category_id',
                supplier_id='$supplier_id',
                image='$image'
                WHERE id=$id";
        
        if ($conn->query($sql)) {
            // Log the activity
            $user_id = $_SESSION['user_id'];
            $username = $_SESSION['username'];
            $log_sql = "INSERT INTO activity_log (user_id, username, action, table_name, record_id, details) 
                        VALUES ($user_id, '$username', 'EDIT', 'products', $id, 'Edited product: $name (Price: $price, Stock: $stock)')";
            $conn->query($log_sql);
            
            header('Location: index.php?updated=1');
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
    <title>Edit Product - Sari-Sari Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="card form-page">
        <h1 class="page-title">Edit Product</h1>
        <p class="page-subtitle">Update product information</p>
        
        <?= $message ?>
        
        <form method="POST" enctype="multipart/form-data">
            <label>Product Name <span class="required">*</span></label>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            
            <label>Description</label>
            <textarea name="description" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
            
            <label>Category <span class="required">*</span></label>
            <select name="category_id" required>
                <option value="">-- Select Category --</option>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $product['category_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            
            <label>Price (₱) <span class="required">*</span></label>
            <input type="number" name="price" step="0.01" min="0" value="<?= $product['price'] ?>" required>
            
            <label>Stock</label>
            <input type="number" name="stock" min="0" value="<?= $product['stock'] ?>">
            
            <label>Supplier</label>
            <select name="supplier_id">
                <option value="">-- Select Supplier --</option>
                <?php while ($sup = $suppliers->fetch_assoc()): ?>
                    <option value="<?= $sup['id'] ?>" <?= ($sup['id'] == $product['supplier_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sup['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            
            <label>Product Image</label>
            <?php if ($product['image']): ?>
                <div style="margin:10px 0;">
                    <img src="uploads/<?= $product['image'] ?>" alt="Current image" style="width:100px; height:100px; object-fit:cover; border-radius:8px;">
                    <p style="color:#888; font-size:0.8em;">Current image</p>
                </div>
            <?php endif; ?>
            <input type="file" name="image" accept="image/*">
            <div class="hint">Leave empty to keep current image</div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">Update Product</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>