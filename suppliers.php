<?php
// ========================================
// SUPPLIERS LIST PAGE
// Shows all suppliers with search - ORDER BY id ASC
// ========================================

require_once 'config.php';
requireLogin();

$search = $_GET['search'] ?? '';

$sql = "SELECT id, name, contact_person, phone FROM suppliers";
if (!empty($search)) {
    $sql .= " WHERE name LIKE '%" . $conn->real_escape_string($search) . "%' 
              OR contact_person LIKE '%" . $conn->real_escape_string($search) . "%'";
}
$sql .= " ORDER BY id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Suppliers - Sari-Sari Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="card">
        <div class="top-bar">
            <h1>Suppliers</h1>
            <?php if (isAdmin()): ?>
                <a href="add_supplier.php" class="btn-add">+ Add Supplier</a>
            <?php endif; ?>
        </div>
        
        <form class="search-bar" method="GET">
            <input type="text" name="search" placeholder="Search suppliers by name or contact person..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn-search">Search</button>
            <?php if (!empty($search)): ?>
                <a href="suppliers.php" class="btn-reset">Reset</a>
            <?php endif; ?>
        </form>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Supplier Name</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                            <td><?= htmlspecialchars($row['contact_person']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td>
                                <?php if (isAdmin()): ?>
                                    <div class="action-buttons">
                                        <a href="edit_supplier.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                                        <a href="delete_supplier.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Delete this supplier?')">Delete</a>
                                    </div>
                                <?php else: ?>
                                    <span class="view-only">View only</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding:40px; color:#999;">
                                No suppliers found.
                                <?php if (isAdmin()): ?>
                                    <br><a href="add_supplier.php" style="color:#4CAF50;">Add your first supplier</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p class="count">Total: <?= $result->num_rows ?> supplier(s)</p>
    </div>
</div>
</body>
</html>