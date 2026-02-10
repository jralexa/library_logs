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

$query = "SELECT id, date, time_in, name, client_type, position, district, purpose
          FROM logbook_entries" . $where . " ORDER BY date DESC, time_in DESC LIMIT ? OFFSET ?";

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
    <title>Admin Dashboard - DepEd Library</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        
        .navbar {
            background: #667eea;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar h1 {
            font-size: 20px;
        }
        
        .navbar-right {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .navbar-right span {
            font-size: 14px;
        }
        
        .btn-logout {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .btn-logout:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .container {
            max-width: 1600px;
            margin: 30px auto;
            padding: 0 24px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .card-header {
            padding: 20px 30px;
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .card-header h2 {
            color: #333;
            font-size: 20px;
        }
        
        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            margin-top: 15px;
            align-items: end;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .filter-group label {
            font-size: 12px;
            color: #666;
            font-weight: 600;
        }
        
        .filter-group input,
        .filter-group select {
            padding: 8px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .btn-filter {
            background: #667eea;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            align-self: flex-end;
            transition: background 0.3s;
        }
        
        .btn-filter:hover {
            background: #5568d3;
        }
        
        .btn-clear {
            background: #6c757d;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
        }
        
        .btn-clear:hover {
            background: #5a6268;
        }
        
        .card-body {
            padding: 0;
        }
        
        .table-wrapper {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }
        
        thead {
            background: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            font-size: 12px;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            border-bottom: 2px solid #e0e0e0;
            white-space: nowrap;
        }
        
        td {
            padding: 14px 15px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
            vertical-align: top;
        }
        
        tbody tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-field { background: #dcfce7; color: #166534; }
        .badge-osds { background: #dbeafe; color: #1e40af; }
        .badge-sgod { background: #fef3c7; color: #92400e; }
        .badge-cid { background: #e9d5ff; color: #6b21a8; }
        .badge-visitor { background: #fed7aa; color: #9a3412; }
        .badge-other { background: #e5e7eb; color: #374151; }
        
        .btn-delete {
            background: #ef4444;
            color: white;
            padding: 5px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .btn-delete:hover {
            background: #dc2626;
        }
        
        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #999;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .success-message {
            background: #d1fae5;
            color: #065f46;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 2px solid #10b981;
        }
        
        .export-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .btn-export {
            background: #10b981;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .btn-export:hover {
            background: #059669;
        }

        @media (min-width: 1200px) {
            .filters {
                grid-template-columns: 220px 1fr 220px 170px 140px;
            }
        }

        @media (max-width: 900px) {
            .navbar {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }

            .navbar-right {
                width: 100%;
                justify-content: space-between;
            }
        }

        @media (max-width: 768px) {
            .container {
                max-width: 100%;
                margin: 0;
                padding: 16px;
            }

            .filters {
                grid-template-columns: 1fr;
            }

            .export-buttons {
                width: 100%;
            }

            .card {
                border-radius: 0;
            }
        }

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: #f8f9fa;
            border-top: 1px solid #e0e0e0;
        }

        .pagination-info {
            font-size: 13px;
            color: #666;
        }

        .pagination-links {
            display: flex;
            gap: 8px;
        }

        .pagination-links a,
        .pagination-links span {
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 13px;
            text-decoration: none;
        }

        .pagination-links a {
            background: white;
            border: 1px solid #e0e0e0;
            color: #333;
        }

        .pagination-links a:hover {
            border-color: #667eea;
            color: #667eea;
        }

        .pagination-links .current {
            background: #667eea;
            color: white;
            border: 1px solid #667eea;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>📚 DepEd Southern Leyte Division Library - Admin Dashboard</h1>
        <div class="navbar-right">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <?php if (isset($_GET['deleted'])): ?>
            <div class="success-message">
                Log entry deleted successfully.
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h2>Library Logbook Entries</h2>
                
                <form method="GET" action="">
                    <div class="filters">
                        <div class="filter-group">
                            <label>Date</label>
                            <input type="date" name="filter_date" value="<?php echo $filter_date; ?>">
                        </div>
                        
                        <div class="filter-group">
                            <label>Name</label>
                            <input type="text" name="filter_name" value="<?php echo htmlspecialchars($filter_name); ?>" placeholder="Search by name...">
                        </div>
                        
                        <div class="filter-group">
                            <label>Client Type</label>
                            <input type="text" name="filter_client_type" value="<?php echo htmlspecialchars($filter_client_type); ?>" placeholder="Search by client type...">
                        </div>
                        
                        <div class="filter-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn-filter">Apply Filters</button>
                        </div>
                        
                        <div class="filter-group">
                            <label>&nbsp;</label>
                            <a href="dashboard.php" class="btn-clear">Clear Filters</a>
                        </div>
                    </div>
                </form>
                
                <div class="export-buttons">
                    <a href="export.php?<?php echo http_build_query($_GET); ?>" class="btn-export">📥 Export to CSV</a>
                </div>
            </div>
            
            <div class="card-body">
                <div class="table-wrapper">
                    <?php if ($result->num_rows > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Time In</th>
                                    <th>Name</th>
                                    <th>Client Type</th>
                                    <th>Position</th>
                                    <th>District</th>
                                    <th>Purpose</th>
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
                                                <?php echo $row['client_type']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['position'] ?: '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['district']); ?></td>
                                        <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <div>📋</div>
                            <p>No library logs found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($total_rows > 0): ?>
                <div class="pagination">
                    <div class="pagination-info">
                        Showing <?php echo number_format($offset + 1); ?>–
                        <?php echo number_format(min($offset + $page_size, $total_rows)); ?>
                        of <?php echo number_format($total_rows); ?> entries
                    </div>
                    <div class="pagination-links">
                        <?php
                            $query_params = $_GET;
                            if ($page > 1) {
                                $query_params['page'] = $page - 1;
                                echo '<a href="?' . htmlspecialchars(http_build_query($query_params)) . '">Previous</a>';
                            }

                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);

                            for ($p = $start_page; $p <= $end_page; $p++) {
                                $query_params['page'] = $p;
                                if ($p === $page) {
                                    echo '<span class="current">' . $p . '</span>';
                                } else {
                                    echo '<a href="?' . htmlspecialchars(http_build_query($query_params)) . '">' . $p . '</a>';
                                }
                            }

                            if ($page < $total_pages) {
                                $query_params['page'] = $page + 1;
                                echo '<a href="?' . htmlspecialchars(http_build_query($query_params)) . '">Next</a>';
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
