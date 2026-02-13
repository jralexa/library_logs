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
    $stmt = $conn->prepare('DELETE FROM logbook_entries WHERE id = ?');
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        set_flash('Log entry deleted successfully.', 'success');
    } else {
        set_flash('Unable to delete log entry.', 'error');
    }

    $stmt->close();

    $query = build_query([], ['delete']);
    $redirect = 'dashboard.php' . ($query ? ('?' . $query) : '');
    redirect_to($redirect);
}

$districts = load_districts($conn);
$schools_by_district = load_schools_by_district($conn);

$school_options = [];
foreach ($schools_by_district as $district_id => $district_schools) {
    foreach ($district_schools as $school) {
        $school_options[] = [
            'id' => $school['id'],
            'district_id' => (int)$district_id,
            'name' => $school['name'],
        ];
    }
}

// Read filter inputs.
$filter_date = get_value('filter_date');
$filter_month = get_value('filter_month');
$filter_name = get_value('filter_name');
$filter_client_type = get_value('filter_client_type');
$filter_district_id = get_value('filter_district_id');
$filter_school_id = get_value('filter_school_id');
$filter_organization = get_value('filter_organization');

if ($filter_month !== '' && !preg_match('/^\d{4}\-(0[1-9]|1[0-2])$/', $filter_month)) {
    $filter_month = '';
}
if ($filter_district_id !== '' && !ctype_digit($filter_district_id)) {
    $filter_district_id = '';
}
if ($filter_school_id !== '' && !ctype_digit($filter_school_id)) {
    $filter_school_id = '';
}

// Build a parameterized WHERE clause based on filters.
$where = ' WHERE 1=1';
$params = [];
$types = '';

if ($filter_date !== '') {
    $where .= ' AND le.date = ?';
    $params[] = $filter_date;
    $types .= 's';
} elseif ($filter_month !== '') {
    $where .= ' AND DATE_FORMAT(le.date, "%Y-%m") = ?';
    $params[] = $filter_month;
    $types .= 's';
}

if ($filter_name !== '') {
    $where .= ' AND le.name LIKE ?';
    $params[] = '%' . $filter_name . '%';
    $types .= 's';
}

if ($filter_client_type !== '') {
    $where .= ' AND le.client_type LIKE ?';
    $params[] = '%' . $filter_client_type . '%';
    $types .= 's';
}

if ($filter_district_id !== '') {
    $where .= ' AND le.district_id = ?';
    $params[] = (int)$filter_district_id;
    $types .= 'i';
}

if ($filter_school_id !== '') {
    $where .= ' AND le.school_id = ?';
    $params[] = (int)$filter_school_id;
    $types .= 'i';
}

if ($filter_organization !== '') {
    $where .= ' AND le.organization_name LIKE ?';
    $params[] = '%' . $filter_organization . '%';
    $types .= 's';
}

$from_clause =
    ' FROM logbook_entries le
      LEFT JOIN districts d ON d.id = le.district_id
      LEFT JOIN schools s ON s.id = le.school_id';

// Pagination settings.
$page = get_value('page');
$page = ($page !== '' && ctype_digit($page)) ? max(1, (int)$page) : 1;
$page_size = 50;
$offset = ($page - 1) * $page_size;

// Count total rows for pagination.
$count_query = 'SELECT COUNT(*) AS total' . $from_clause . $where;
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
$query =
    'SELECT le.*, d.name AS district_name, s.name AS school_name' .
    $from_clause .
    $where .
    ' ORDER BY le.date DESC, le.time_in DESC LIMIT ? OFFSET ?';

$params_with_page = $params;
$types_with_page = $types . 'ii';
$params_with_page[] = $page_size;
$params_with_page[] = $offset;

$stmt = $conn->prepare($query);
$stmt->bind_param($types_with_page, ...$params_with_page);
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
            <a href="dashboard.php" class="btn-nav-link is-active">Dashboard</a>
            <a href="master_data.php" class="btn-nav-link">Master Data</a>
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
                            <label for="filter_month">Month</label>
                            <input type="month" id="filter_month" name="filter_month" value="<?php echo h($filter_month); ?>">
                        </div>

                        <div class="filter-group">
                            <label for="filter_name">Name</label>
                            <input type="text" id="filter_name" name="filter_name" value="<?php echo h($filter_name); ?>" placeholder="Search name...">
                        </div>

                        <div class="filter-group">
                            <label for="filter_client_type">Client Type</label>
                            <input type="text" id="filter_client_type" name="filter_client_type" value="<?php echo h($filter_client_type); ?>" placeholder="Search type...">
                        </div>

                        <div class="filter-group">
                            <label for="filter_district_id">District</label>
                            <select id="filter_district_id" name="filter_district_id">
                                <option value="">All districts</option>
                                <?php foreach ($districts as $district): ?>
                                    <option value="<?php echo h((string)$district['id']); ?>" <?php echo ((string)$district['id'] === $filter_district_id) ? 'selected' : ''; ?>>
                                        <?php echo h($district['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="filter_school_id">School</label>
                            <select id="filter_school_id" name="filter_school_id">
                                <option value="">All schools</option>
                                <?php foreach ($school_options as $school): ?>
                                    <option
                                        value="<?php echo h((string)$school['id']); ?>"
                                        data-district-id="<?php echo h((string)$school['district_id']); ?>"
                                        <?php echo ((string)$school['id'] === $filter_school_id) ? 'selected' : ''; ?>
                                    >
                                        <?php echo h($school['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="filter_organization">Other Organization</label>
                            <input type="text" id="filter_organization" name="filter_organization" value="<?php echo h($filter_organization); ?>" placeholder="Division Office, NGO, etc.">
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
                                <th>Organization</th>
                                <th>Purpose</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo h(date('M d, Y', strtotime((string)$row['date']))); ?></td>
                                    <td><?php echo h(date('h:i A', strtotime((string)$row['time_in']))); ?></td>
                                    <td><?php echo h($row['name']); ?></td>
                                    <td>
                                        <span class="badge <?php echo h(client_type_badge_class((string)$row['client_type'])); ?>">
                                            <?php echo h($row['client_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo h($row['position'] ?: '-'); ?></td>
                                    <td><?php echo h(organization_label($row['district_name'], $row['school_name'], $row['organization_name'])); ?></td>
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

    <script>
    (function () {
        var districtSelect = document.getElementById('filter_district_id');
        var schoolSelect = document.getElementById('filter_school_id');

        if (!districtSelect || !schoolSelect) {
            return;
        }

        function syncSchoolFilter() {
            var districtValue = districtSelect.value;
            var options = schoolSelect.querySelectorAll('option[data-district-id]');
            var selectedStillVisible = false;

            for (var i = 0; i < options.length; i += 1) {
                var option = options[i];
                var matchesDistrict = !districtValue || option.getAttribute('data-district-id') === districtValue;
                option.hidden = !matchesDistrict;

                if (matchesDistrict && option.selected) {
                    selectedStillVisible = true;
                }
            }

            if (!selectedStillVisible && schoolSelect.value !== '') {
                schoolSelect.value = '';
            }
        }

        districtSelect.addEventListener('change', syncSchoolFilter);
        syncSchoolFilter();
    })();
    </script>
<?php
$stmt->close();
$conn->close();
require __DIR__ . '/../includes/partials/document_end.php';
?>
