<?php
// ========================================
// USER MANAGEMENT PAGE
// Admin only - Shows all users with links to CRUD operations
// ========================================

require_once 'config.php';
requireAdmin();

// ========================================
// GET USERS WITH PRODUCT COUNT
// ========================================
$users = $conn->query("
    SELECT u.*, COUNT(p.id) AS product_count 
    FROM users u 
    LEFT JOIN products p ON u.id = p.added_by 
    GROUP BY u.id 
    ORDER BY u.id
");
?>  
<!DOCTYPE html>
<html>
<head>
    <title>User Management - Sari-Sari Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="card">
        <div class="top-bar">
            <h1>User Management</h1>
            <a href="add_user.php" class="btn-add">+ Add User</a>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if (isset($_GET['added'])): ?>
            <div class="success-msg">User added successfully!</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['deleted'])): ?>
            <div class="success-msg">User deleted successfully!</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['updated'])): ?>
            <div class="success-msg">User updated successfully!</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error']) && $_GET['error'] == 'self'): ?>
            <div class="error-msg">You cannot delete your own account!</div>
        <?php endif; ?>
        
        <!-- ========================================
             USERS TABLE
             ======================================== -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Products Added</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users->num_rows > 0): ?>
                        <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><strong><?= htmlspecialchars($user['username']) ?></strong></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span style="background: <?= $user['role'] == 'admin' ? '#FF9800' : '#4CAF50'; ?>; color:white; padding:3px 14px; border-radius:15px; font-size:0.8em; font-weight:600;">
                                    <?= htmlspecialchars($user['role']) ?>
                                </span>
                            </td>
                            <td><?= $user['product_count'] ?></td>
                            <td><?= $user['created_at'] ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn-edit-user">Edit</a>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn-delete-user" onclick="return confirm('Delete this user? This action cannot be undone!')">Delete</a>
                                    <?php else: ?>
                                        <span style="color:#999; font-size:0.8em;">(You)</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:40px; color:#999;">
                                No users found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p class="count">Total: <?= $users->num_rows ?> user(s)</p>
    </div>
</div>
</body>
</html>