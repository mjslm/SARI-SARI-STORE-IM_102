<?php
// ========================================
// EDIT SUPPLIER PAGE
// Admin only - No address field
// ========================================

require_once 'config.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$result = $conn->query("SELECT * FROM suppliers WHERE id = $id");
$supplier = $result->fetch_assoc();

if (!$supplier) {
    die("Supplier not found.");
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string(trim($_POST['name'] ?? ''));
    $contact_person = $conn->real_escape_string(trim($_POST['contact_person'] ?? ''));
    $phone = $conn->real_escape_string(trim($_POST['phone'] ?? ''));
    
    if (empty($name)) {
        $message = "<div class='alert-error'>Supplier name is required!</div>";
    } else {
        $sql = "UPDATE suppliers SET 
                name='$name', 
                contact_person='$contact_person', 
                phone='$phone' 
                WHERE id=$id";
        
        if ($conn->query($sql)) {
            // Log the activity
            $user_id = $_SESSION['user_id'];
            $username = $_SESSION['username'];
            $log_sql = "INSERT INTO activity_log (user_id, username, action, table_name, record_id, details) 
                        VALUES ($user_id, '$username', 'EDIT', 'suppliers', $id, 'Edited supplier: $name')";
            $conn->query($log_sql);
            
            header('Location: suppliers.php?updated=1');
            exit;
        } else {
            $message = "<div class='alert-error'>Error: " . $conn->error . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Supplier - Sari-Sari Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="register-page">
    <div class="register-container">
        <h2>Edit Supplier</h2>
        <p class="subtitle">Update supplier information</p>
        
        <?= $message ?>
        
        <form method="POST">
            <label>Supplier Name <span class="required">*</span></label>
            <input type="text" name="name" value="<?= htmlspecialchars($supplier['name']) ?>" required>
            
            <label>Contact Person</label>
            <input type="text" name="contact_person" value="<?= htmlspecialchars($supplier['contact_person']) ?>">
            
            <label>Phone Number</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($supplier['phone']) ?>">
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">Update Supplier</button>
                <a href="suppliers.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>