<?php
declare(strict_types=1);

// Load shared config, session, and helper functions.
require_once __DIR__ . '/../includes/bootstrap.php';

// Guard the page so only admin users can access it.
requireAdmin();

// Build a query string from current filters with overrides/exclusions.
function build_query(array $overrides = [], array $exclude = []): string
{
    $query = $_GET;
    foreach ($exclude as $key) {
        unset($query[$key]);
    }
    foreach ($overrides as $key => $value) {
        $query[$key] = $value;
    }

    return http_build_query($query);
}

// Handle delete actions via query string parameter.
$delete_id = get_value('delete');
if ($delete_id !== '' && ctype_digit($delete_id)) {
    $id = (int)$delete_id;
    // Delete the selected log entry.
    $stmt = $conn->prepare('DELETE FROM logbook_entries WHERE id = ?');
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        set_flash('Log entry deleted successfully.', 'success');
    } else {
        set_flash('Unable to delete log entry.', 'error');
    }

    $stmt->close();

    // Redirect back to the list view without the delete parameter.
    $query = build_query([], ['delete']);
    $redirect = 'dashboard.php' . ($query ? ('?' . $query) : '');
    redirect_to($redirect);
}

// Read filter inputs.
$filter_date = get_value('filter_date');
$filter_name = get_value('filter_name');
$filter_client_type = get_value('filter_client_type');

// Build a parameterized WHERE clause based on filters.
$where = ' WHERE 1=1';
$params = [];
$types = '';

if ($filter_date) {
    $where .= ' AND date = ?';
    $params[] = $filter_date;
    $types .= 's';
}

if ($filter_name) {
    $where .= ' AND name LIKE ?';
    $params[] = '%' . $filter_name . '%';
    $types .= 's';
}

if ($filter_client_type) {
    $where .= ' AND client_type LIKE ?';
    $params[] = '%' . $filter_client_type . '%';
    $types .= 's';
}

// Pagination settings.
$page = get_value('page');
$page = ($page !== '' && ctype_digit($page)) ? max(1, (int)$page) : 1;
$page_size = 50;
$offset = ($page - 1) * $page_size;

// Count total rows for pagination.
$count_query = 'SELECT COUNT(*) AS total FROM logbook_entries' . $where;
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

// Fetch current page of records.
$query = 'SELECT * FROM logbook_entries' . $where . ' ORDER BY date DESC, time_in DESC LIMIT ? OFFSET ?';

$params_with_page = $params;
$types_with_page = $types . 'ii';
$params_with_page[] = $page_size;
$params_with_page[] = $offset;

$stmt = $conn->prepare($query);
if (!empty($params_with_page)) {
    $stmt->bind_param($types_with_page, ...$params_with_page);
}
$stmt->execute();
$result = $stmt->get_result();

// Page metadata and assets.
$page_title = 'Admin Dashboard';
$styles = ['../css/admin.css'];
require __DIR__ . '/../includes/partials/document_start.php';
?>
    <div class="navbar">
        <h1>Library - DepEd Southern Leyte</h1>
        <div class="navbar-right">
            <span><?php echo h($_SESSION['username']); ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>

    <div class="container">
        <?php require __DIR__ . '/../includes/partials/flash.php'; ?>

        <div class="card">
            <div class="card-header">
                <h2>Library Logbook (<?php echo number_format($total_rows); ?> entries)</h2>

                <form method="GET" action="">
                    <div class="filters">
                        <div class="filter-group">
                            <label for="filter_date">Date</label>
                            <input type="date" id="filter_date" name="filter_date" value="<?php echo h($filter_date); ?>">
                        </div>

                        <div class="filter-group">
                            <label for="filter_name">Name</label>
                            <input type="text" id="filter_name" name="filter_name" value="<?php echo h($filter_name); ?>" placeholder="Search name...">
                        </div>

                        <div class="filter-group">
                            <label for="filter_client_type">Client Type</label>
                            <input type="text" id="filter_client_type" name="filter_client_type" value="<?php echo h($filter_client_type); ?>" placeholder="Search type...">
                        </div>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="dashboard.php" class="btn btn-secondary">Clear</a>
                        <a href="export.php?<?php echo build_query([], ['delete']); ?>" class="btn btn-success">Export CSV</a>
                    </div>
                </form>
            </div>

            <div class="table-wrapper">
                <?php if ($result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Position</th>
                                <th>District</th>
                                <th>Purpose</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo h(date('M d, Y', strtotime($row['date']))); ?></td>
                                    <td><?php echo h(date('h:i A', strtotime($row['time_in']))); ?></td>
                                    <td><?php echo h($row['name']); ?></td>
                                    <td>
                                        <span class="badge <?php echo h(client_type_badge_class((string)$row['client_type'])); ?>">
                                            <?php echo h($row['client_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo h($row['position'] ?: '-'); ?></td>
                                    <td><?php echo h($row['district']); ?></td>
                                    <td><?php echo h($row['purpose']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No entries found.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($total_rows > 0): ?>
                <div class="pagination">
                    <div class="pagination-info">
                        Showing <?php echo number_format($offset + 1); ?>&ndash;<?php echo number_format(min($offset + $page_size, $total_rows)); ?> of <?php echo number_format($total_rows); ?>
                    </div>
                    <div class="pagination-links">
                        <?php
                        if ($page > 1) {
                            echo '<a href="?' . build_query(['page' => $page - 1]) . '">&larr; Prev</a>';
                        }

                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);

                        if ($start_page > 1) {
                            echo '<a href="?' . build_query(['page' => 1]) . '">1</a>';
                            if ($start_page > 2) {
                                echo '<span>...</span>';
                            }
                        }

                        for ($p = $start_page; $p <= $end_page; $p++) {
                            if ($p === $page) {
                                echo '<span class="current">' . $p . '</span>';
                            } else {
                                echo '<a href="?' . build_query(['page' => $p]) . '">' . $p . '</a>';
                            }
                        }

                        if ($end_page < $total_pages) {
                            if ($end_page < $total_pages - 1) {
                                echo '<span>...</span>';
                            }
                            echo '<a href="?' . build_query(['page' => $total_pages]) . '">' . $total_pages . '</a>';
                        }

                        if ($page < $total_pages) {
                            echo '<a href="?' . build_query(['page' => $page + 1]) . '">Next &rarr;</a>';
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php
$stmt->close();
$conn->close();
require __DIR__ . '/../includes/partials/document_end.php';
?>
