<?php
// ========================================
// ACTIVITY LOG PAGE
// Shows all user actions with filters
// ========================================

require_once 'config.php';
requireAdmin();

// Get filter parameters
$action_filter = $_GET['action'] ?? '';
$table_filter = $_GET['table'] ?? '';
$user_filter = $_GET['user'] ?? '';
$sort_order = $_GET['sort'] ?? 'DESC';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

// Build query
$sql = "SELECT * FROM activity_log WHERE 1=1";

if (!empty($action_filter)) {
    $sql .= " AND action = '" . $conn->real_escape_string($action_filter) . "'";
}

if (!empty($table_filter)) {
    $sql .= " AND table_name = '" . $conn->real_escape_string($table_filter) . "'";
}

if (!empty($user_filter)) {
    $sql .= " AND username LIKE '%" . $conn->real_escape_string($user_filter) . "%'";
}

if (!empty($date_from)) {
    $sql .= " AND DATE(created_at) >= '" . $conn->real_escape_string($date_from) . "'";
}

if (!empty($date_to)) {
    $sql .= " AND DATE(created_at) <= '" . $conn->real_escape_string($date_to) . "'";
}

// Sort order - ASC for oldest first, DESC for latest first
$sort_order = ($sort_order == 'ASC') ? 'ASC' : 'DESC';
$sql .= " ORDER BY created_at " . $sort_order;

$logs = $conn->query($sql);

// Get all unique actions for filter
$actions = $conn->query("SELECT DISTINCT action FROM activity_log ORDER BY action");

// Get all unique tables for filter
$tables = $conn->query("SELECT DISTINCT table_name FROM activity_log ORDER BY table_name");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Activity Log - Sari-Sari Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="card">
        <div class="top-bar">
            <h1>Activity Log</h1>
            <a href="index.php" class="btn-back" style="margin-top:0;">Back to Dashboard</a>
        </div>
        <p class="page-subtitle">Complete history of all user actions in the system</p>
        
        <!-- Filter Bar -->
        <div class="search-bar" style="margin-bottom:20px;">
            <form method="GET" style="display:flex; gap:12px; flex-wrap:wrap; width:100%; align-items:center;">
                <!-- Action Filter -->
                <select name="action" style="padding:10px 16px; border:2px solid #e0e0e0; border-radius:8px; font-size:0.95em; background:#fff; min-width:130px;">
                    <option value="">All Actions</option>
                    <?php while ($act = $actions->fetch_assoc()): ?>
                        <option value="<?= $act['action'] ?>" <?= ($action_filter == $act['action']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($act['action']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <!-- Table Filter -->
                <select name="table" style="padding:10px 16px; border:2px solid #e0e0e0; border-radius:8px; font-size:0.95em; background:#fff; min-width:130px;">
                    <option value="">All Tables</option>
                    <?php while ($tbl = $tables->fetch_assoc()): ?>
                        <option value="<?= $tbl['table_name'] ?>" <?= ($table_filter == $tbl['table_name']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tbl['table_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <!-- User Search -->
                <input type="text" name="user" placeholder="Search by username..." value="<?= htmlspecialchars($user_filter) ?>" style="padding:10px 16px; border:2px solid #e0e0e0; border-radius:8px; font-size:0.95em; min-width:150px;">
                
                <!-- Date From -->
                <input type="date" name="date_from" value="<?= htmlspecialchars($date_from) ?>" style="padding:10px 16px; border:2px solid #e0e0e0; border-radius:8px; font-size:0.95em; min-width:150px;">
                
                <!-- Date To -->
                <input type="date" name="date_to" value="<?= htmlspecialchars($date_to) ?>" style="padding:10px 16px; border:2px solid #e0e0e0; border-radius:8px; font-size:0.95em; min-width:150px;">
                
                <!-- Sort Order -->
                <select name="sort" style="padding:10px 16px; border:2px solid #e0e0e0; border-radius:8px; font-size:0.95em; background:#fff; min-width:130px;">
                    <option value="DESC" <?= ($sort_order == 'DESC') ? 'selected' : '' ?>>Latest First</option>
                    <option value="ASC" <?= ($sort_order == 'ASC') ? 'selected' : '' ?>>Oldest First</option>
                </select>
                
                <button type="submit" class="btn-search">Filter</button>
                <?php if (!empty($action_filter) || !empty($table_filter) || !empty($user_filter) || !empty($date_from) || !empty($date_to) || !empty($sort_order)): ?>
                    <a href="activity_log.php" class="btn-reset">Reset</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Logs Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Table</th>
                        <th>Details</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($logs->num_rows > 0): ?>
                        <?php while ($log = $logs->fetch_assoc()): ?>
                        <tr>
                            <td><?= $log['id'] ?></td>
                            <td><strong><?= htmlspecialchars($log['username']) ?></strong></td>
                            <td>
                                <span style="padding:2px 12px; border-radius:12px; font-size:0.75em; font-weight:600; 
                                    <?= $log['action'] == 'ADD' ? 'background:#e8f5e9; color:#1a472a;' : '' ?>
                                    <?= $log['action'] == 'EDIT' ? 'background:#fff3e0; color:#e65100;' : '' ?>
                                    <?= $log['action'] == 'DELETE' ? 'background:#ffebee; color:#c62828;' : '' ?>
                                    <?= $log['action'] == 'LOGIN' ? 'background:#e3f2fd; color:#0d47a1;' : '' ?>
                                    <?= $log['action'] == 'LOGOUT' ? 'background:#f3e5f5; color:#4a148c;' : '' ?>
                                ">
                                    <?= htmlspecialchars($log['action']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($log['table_name']) ?></td>
                            <td style="font-size:0.85em; color:#666; max-width:350px; word-wrap:break-word;">
                                <?= htmlspecialchars($log['details'] ?? '') ?>
                            </td>
                            <td style="font-size:0.75em; color:#888; white-space:nowrap;">
                                <?= date('M d, Y h:i:s A', strtotime($log['created_at'])) ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding:40px; color:#999;">
                                No activity logs found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p class="count">Total: <?= $logs->num_rows ?> log entries</p>
    </div>
</div>
</body>
</html>