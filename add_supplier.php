<?php
// ========================================
// ADD SUPPLIER PAGE
// Admin only - No address field
// ========================================

require_once 'config.php';
requireAdmin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string(trim($_POST['name'] ?? ''));
    $contact_person = $conn->real_escape_string(trim($_POST['contact_person'] ?? ''));
    $phone = $conn->real_escape_string(trim($_POST['phone'] ?? ''));
    
    if (empty($name)) {
        $message = "<div class='alert-error'>Supplier name is required!</div>";
    } else {
        $sql = "INSERT INTO suppliers (name, contact_person, phone) 
                VALUES ('$name', '$contact_person', '$phone')";
        
        if ($conn->query($sql)) {
            // Log the activity
            $user_id = $_SESSION['user_id'];
            $username = $_SESSION['username'];
            $supplier_id = $conn->insert_id;
            $log_sql = "INSERT INTO activity_log (user_id, username, action, table_name, record_id, details) 
                        VALUES ($user_id, '$username', 'ADD', 'suppliers', $supplier_id, 'Added supplier: $name')";
            $conn->query($log_sql);
            
            header('Location: suppliers.php?added=1');
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
    <title>Add Supplier - Sari-Sari Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="register-page">
    <div class="register-container">
        <h2>Add New Supplier</h2>
        <p class="subtitle">Register a new supplier for your store</p>
        
        <?= $message ?>
        
        <form method="POST">
            <label>Supplier Name <span class="required">*</span></label>
            <input type="text" name="name" placeholder="e.g. Puregold Supplier" required>
            
            <label>Contact Person</label>
            <input type="text" name="contact_person" placeholder="Name of contact person">
            
            <label>Phone Number</label>
            <input type="text" name="phone" placeholder="e.g. 09123456789">
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">Add Supplier</button>
                <a href="suppliers.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>