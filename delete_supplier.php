<?php
// ========================================
// DELETE SUPPLIER PAGE
// Admin only - with confirmation
// ========================================

require_once 'config.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $conn->query("SELECT name FROM suppliers WHERE id = $id");
    $supplier = $result->fetch_assoc();
    
    $conn->query("DELETE FROM suppliers WHERE id = $id");
    
    // Log the activity
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $log_sql = "INSERT INTO activity_log (user_id, username, action, table_name, record_id, details) 
                VALUES ($user_id, '$username', 'DELETE', 'suppliers', $id, 'Deleted supplier: " . $supplier['name'] . "')";
    $conn->query($log_sql);
    
    header('Location: suppliers.php?deleted=1');
    exit;
}

$result = $conn->query("SELECT id, name, contact_person, phone FROM suppliers WHERE id = $id");
$supplier = $result->fetch_assoc();

if (!$supplier) {
    die("Supplier not found.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Supplier - Sari-Sari Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container delete-container">
    <h1>Delete Supplier</h1>
    
    <p style="font-size:1em; margin:25px 0 10px 0; color:#555;">Are you sure you want to delete:</p>
    
    <p class="delete-name">
        <?= htmlspecialchars($supplier['name']) ?>
    </p>
    
    <p class="delete-details">
        Contact: <?= htmlspecialchars($supplier['contact_person'] ?? 'N/A') ?>
        | Phone: <?= htmlspecialchars($supplier['phone'] ?? 'N/A') ?>
    </p>
    
    <p class="delete-warning">This action cannot be undone!</p>

    <form method="POST" style="display:inline;">
        <button type="submit" class="btn-delete-yes">Yes, Delete</button>
    </form>
    <a href="suppliers.php" class="btn-delete-cancel">Cancel</a>
</div>
</body>
</html>