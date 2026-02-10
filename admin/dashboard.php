<?php
require_once '../config/database.php';
require_once '../config/session.php';

requireAdmin();

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM logbook_entries WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: dashboard.php?deleted=1');
    exit();
}

// Get filter parameters
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';
$filter_name = isset($_GET['filter_name']) ? $_GET['filter_name'] : '';
$filter_client_type = isset($_GET['filter_client_type']) ? $_GET['filter_client_type'] : '';

// Build query with filters
$where = " WHERE 1=1";
$params = [];
$types = "";

if ($filter_date) {
    $where .= " AND date = ?";
    $params[] = $filter_date;
    $types .= "s";
}

if ($filter_name) {
    $where .= " AND name LIKE ?";
    $params[] = "%$filter_name%";
    $types .= "s";
}

if ($filter_client_type) {
    $where .= " AND client_type LIKE ?";
    $params[] = "%$filter_client_type%";
    $types .= "s";
}

// Pagination
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$page_size = 50;
$offset = ($page - 1) * $page_size;

// Count total records
$count_query = "SELECT COUNT(*) AS total FROM logbook_entries" . $where;
$count_stmt = $conn->prepare($count_query);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_rows = (int)$count_result->fetch_assoc()['total'];
$count_stmt->close();

$total_pages = max(1, (int)ceil($total_rows / $page_size));
$page = min($page, $total_pages);
$offset = ($page - 1) * $page_size;

// Get records for current page
$query = "SELECT * FROM logbook_entries" . $where . " ORDER BY date DESC, time_in DESC LIMIT ? OFFSET ?";

$params_with_page = $params;
$types_with_page = $types . "ii";
$params_with_page[] = $page_size;
$params_with_page[] = $offset;

$stmt = $conn->prepare($query);
if (!empty($params_with_page)) {
    $stmt->bind_param($types_with_page, ...$params_with_page);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f5f5f5;
            font-size: 14px;
        }
        
        .navbar {
            position: fixed;        /* 🔴 makes it fixed */
            top: 0;                 /* stick to top */
            left: 0;
            width: 100%;
            z-index: 1000;          /* stay above content */

            background: #2563eb;
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;

            box-shadow: 0 2px 6px rgba(0,0,0,0.15); /* optional shadow */
        }

        .navbar h1 { font-size: 16px; font-weight: 600; }
        .navbar-right { display: flex; gap: 12px; align-items: center; font-size: 13px; }
        
        .btn-logout {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 6px 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
        }
        
        .btn-logout:hover { background: rgba(255,255,255,0.3); }
        
        .container {
            max-width: 1600px;
            margin: 16px auto;
            padding: 0 16px;
        }
        
        .alert {
            background: #d1fae5;
            color: #065f46;
            padding: 10px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 13px;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .card-header {
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .card-header h2 { font-size: 16px; font-weight: 600; margin-bottom: 12px; }
        
        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 8px;
            margin-bottom: 12px;
        }
        
        .filter-group label {
            display: block;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 4px;
            font-weight: 500;
        }
        
        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 6px 10px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 13px;
        }
        
        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #2563eb;
        }
        
        .filter-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 6px 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary { background: #2563eb; color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary { background: #6b7280; color: white; }
        .btn-secondary:hover { background: #4b5563; }
        .btn-success { background: #059669; color: white; }
        .btn-success:hover { background: #047857; }
        .btn-danger { background: #dc2626; color: white; font-size: 12px; padding: 4px 10px; }
        .btn-danger:hover { background: #b91c1c; }
        
        .table-wrapper { overflow-x: auto; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }
        
        thead { background: #f9fafb; }
        
        th {
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            font-size: 12px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }
        
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 13px;
        }
        
        tbody tr:hover { background: #f9fafb; }
        
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .badge-student { background: #dbeafe; color: #1e40af; }
        .badge-teacher { background: #dcfce7; color: #166534; }
        .badge-staff { background: #fef3c7; color: #92400e; }
        .badge-field { background: #fed7aa; color: #9a3412; }
        .badge-visitor { background: #e9d5ff; color: #6b21a8; }
        .badge-other { background: #e5e7eb; color: #374151; }
        
        .empty-state {
            padding: 48px 20px;
            text-align: center;
            color: #9ca3af;
            font-size: 14px;
        }
        
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            font-size: 13px;
        }
        
        .pagination-info { color: #6b7280; }
        
        .pagination-links {
            display: flex;
            gap: 4px;
        }
        
        .pagination-links a,
        .pagination-links span {
            padding: 4px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
        }
        
        .pagination-links a {
            background: white;
            border: 1px solid #d1d5db;
            color: #374151;
        }
        
        .pagination-links a:hover {
            border-color: #2563eb;
            color: #2563eb;
        }
        
        .pagination-links .current {
            background: #2563eb;
            color: white;
            border: 1px solid #2563eb;
        }
        
        @media (max-width: 768px) {
            .container { margin: 0; padding: 0; }
            .card { border-radius: 0; }
            .navbar { flex-direction: column; gap: 8px; }
            .navbar-right { width: 100%; justify-content: space-between; }
            .filters { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Library  - DepEd Southern Leyte</h1>
        <div class="navbar-right">
            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert">✓ Log entry deleted successfully</div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header sticky-filters">
                <h2>Library Logbook (<?php echo number_format($total_rows); ?> entries)</h2>
                
                <form method="GET" action="">
                    <div class="filters">
                        <div class="filter-group">
                            <label>Date</label>
                            <input type="date" name="filter_date" value="<?php echo htmlspecialchars($filter_date); ?>">
                        </div>
                        
                        <div class="filter-group">
                            <label>Name</label>
                            <input type="text" name="filter_name" value="<?php echo htmlspecialchars($filter_name); ?>" placeholder="Search name...">
                        </div>
                        
                        <div class="filter-group">
                            <label>Client Type</label>
                            <input type="text" name="filter_client_type" value="<?php echo htmlspecialchars($filter_client_type); ?>" placeholder="Search type...">
                        </div>
                    </div>
                    
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="dashboard.php" class="btn btn-secondary">Clear</a>
                        <a href="export.php?<?php echo http_build_query($_GET); ?>" class="btn btn-success">📥 Export CSV</a>
                    </div>
                </form>
            </div>
            
            <div class="table-wrapper">
                <?php if ($result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Position</th>
                                <th>District</th>
                                <th>Purpose</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['date'])); ?></td>
                                    <td><?php echo date('h:i A', strtotime($row['time_in'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($row['client_type']); ?>">
                                            <?php echo htmlspecialchars($row['client_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['position'] ?: '-'); ?></td>
                                    <td><?php echo htmlspecialchars($row['district']); ?></td>
                                    <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                                    <td>
                                        <a href="?delete=<?php echo $row['id']; ?>&<?php echo http_build_query(array_diff_key($_GET, ['delete' => ''])); ?>" 
                                           class="btn btn-danger" 
                                           onclick="return confirm('Delete this entry?')">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <div style="font-size: 32px; margin-bottom: 8px;">📋</div>
                        <p>No entries found</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($total_rows > 0): ?>
                <div class="pagination">
                    <div class="pagination-info">
                        Showing <?php echo number_format($offset + 1); ?>–<?php echo number_format(min($offset + $page_size, $total_rows)); ?> of <?php echo number_format($total_rows); ?>
                    </div>
                    <div class="pagination-links">
                        <?php
                            $query_params = $_GET;
                            
                            // Previous button
                            if ($page > 1) {
                                $query_params['page'] = $page - 1;
                                echo '<a href="?' . http_build_query($query_params) . '">← Prev</a>';
                            }

                            // Page numbers
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);

                            if ($start_page > 1) {
                                $query_params['page'] = 1;
                                echo '<a href="?' . http_build_query($query_params) . '">1</a>';
                                if ($start_page > 2) echo '<span>...</span>';
                            }

                            for ($p = $start_page; $p <= $end_page; $p++) {
                                $query_params['page'] = $p;
                                if ($p === $page) {
                                    echo '<span class="current">' . $p . '</span>';
                                } else {
                                    echo '<a href="?' . http_build_query($query_params) . '">' . $p . '</a>';
                                }
                            }

                            if ($end_page < $total_pages) {
                                if ($end_page < $total_pages - 1) echo '<span>...</span>';
                                $query_params['page'] = $total_pages;
                                echo '<a href="?' . http_build_query($query_params) . '">' . $total_pages . '</a>';
                            }

                            // Next button
                            if ($page < $total_pages) {
                                $query_params['page'] = $page + 1;
                                echo '<a href="?' . http_build_query($query_params) . '">Next →</a>';
                            }
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>