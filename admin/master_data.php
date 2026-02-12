<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
requireAdmin();

function is_valid_client_type_code(string $code): bool
{
    return (bool)preg_match('/^[a-z0-9_]+$/', $code);
}

function normalize_status(string $raw): int
{
    return $raw === '1' ? 1 : 0;
}

function parse_required_id(string $raw): ?int
{
    if (!ctype_digit($raw)) {
        return null;
    }

    $id = (int)$raw;
    return $id > 0 ? $id : null;
}

function parse_optional_id(string $raw): ?int
{
    if ($raw === '') {
        return null;
    }

    return parse_required_id($raw);
}

function district_exists(mysqli $conn, int $district_id): bool
{
    $stmt = $conn->prepare('SELECT id FROM districts WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $district_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows === 1;
    $stmt->close();

    return $exists;
}

function build_master_query(array $overrides = [], array $remove = []): string
{
    $query = $_GET;

    foreach ($remove as $key) {
        unset($query[$key]);
    }

    foreach ($overrides as $key => $value) {
        $query[$key] = (string)$value;
    }

    return http_build_query($query);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = post_value('action');

    if ($action === 'add_district') {
        $name = post_value('name');
        $is_active = normalize_status(post_value('is_active'));

        if ($name === '') {
            set_flash('District name is required.', 'error');
            redirect_to('master_data.php');
        }

        $stmt = $conn->prepare('INSERT INTO districts (name, is_active) VALUES (?, ?)');
        $stmt->bind_param('si', $name, $is_active);
        if ($stmt->execute()) {
            set_flash('District added.', 'success');
        } else {
            set_flash('Unable to add district (name may already exist).', 'error');
        }
        $stmt->close();
        redirect_to('master_data.php');
    }

    if ($action === 'update_district') {
        $district_id = parse_required_id(post_value('district_id'));
        $name = post_value('name');
        $is_active = normalize_status(post_value('is_active'));

        if ($district_id === null || $name === '') {
            set_flash('Invalid district update payload.', 'error');
            redirect_to('master_data.php');
        }

        $stmt = $conn->prepare(
            'UPDATE districts
             SET name = ?, is_active = ?
             WHERE id = ?'
        );
        $stmt->bind_param('sii', $name, $is_active, $district_id);
        if ($stmt->execute()) {
            set_flash('District updated.', 'success');
        } else {
            set_flash('Unable to update district.', 'error');
        }
        $stmt->close();
        redirect_to('master_data.php');
    }

    if ($action === 'add_school') {
        $district_id = parse_required_id(post_value('district_id'));
        $name = post_value('name');
        $is_active = normalize_status(post_value('is_active'));

        if ($district_id === null || $name === '' || !district_exists($conn, $district_id)) {
            set_flash('Invalid school input.', 'error');
            redirect_to('master_data.php');
        }

        $stmt = $conn->prepare('INSERT INTO schools (district_id, name, is_active) VALUES (?, ?, ?)');
        $stmt->bind_param('isi', $district_id, $name, $is_active);
        if ($stmt->execute()) {
            set_flash('School added.', 'success');
        } else {
            set_flash('Unable to add school (district+school may already exist).', 'error');
        }
        $stmt->close();
        redirect_to('master_data.php');
    }

    if ($action === 'update_school') {
        $school_id = parse_required_id(post_value('school_id'));
        $district_id = parse_required_id(post_value('district_id'));
        $name = post_value('name');
        $is_active = normalize_status(post_value('is_active'));

        if (
            $school_id === null ||
            $district_id === null ||
            $name === '' ||
            !district_exists($conn, $district_id)
        ) {
            set_flash('Invalid school update payload.', 'error');
            redirect_to('master_data.php');
        }

        $stmt = $conn->prepare(
            'UPDATE schools
             SET district_id = ?,
                 name = ?,
                 is_active = ?
             WHERE id = ?'
        );
        $stmt->bind_param('isii', $district_id, $name, $is_active, $school_id);
        if ($stmt->execute()) {
            set_flash('School updated.', 'success');
        } else {
            set_flash('Unable to update school.', 'error');
        }
        $stmt->close();
        redirect_to('master_data.php');
    }

    if ($action === 'add_client_type') {
        $code = strtolower(post_value('code'));
        $label = post_value('label');
        $is_active = normalize_status(post_value('is_active'));

        if (!is_valid_client_type_code($code) || $label === '') {
            set_flash('Invalid client type input.', 'error');
            redirect_to('master_data.php');
        }

        $stmt = $conn->prepare(
            'INSERT INTO client_types (code, label, is_active) VALUES (?, ?, ?)'
        );
        $stmt->bind_param('ssi', $code, $label, $is_active);
        if ($stmt->execute()) {
            set_flash('Client type added.', 'success');
        } else {
            set_flash('Unable to add client type (code/label may already exist).', 'error');
        }
        $stmt->close();
        redirect_to('master_data.php');
    }

    if ($action === 'update_client_type') {
        $client_type_id = parse_required_id(post_value('client_type_id'));
        $code = strtolower(post_value('code'));
        $label = post_value('label');
        $is_active = normalize_status(post_value('is_active'));

        if ($client_type_id === null || !is_valid_client_type_code($code) || $label === '') {
            set_flash('Invalid client type update payload.', 'error');
            redirect_to('master_data.php');
        }

        $stmt = $conn->prepare(
            'UPDATE client_types
             SET code = ?, label = ?, is_active = ?
             WHERE id = ?'
        );
        $stmt->bind_param('ssii', $code, $label, $is_active, $client_type_id);
        if ($stmt->execute()) {
            set_flash('Client type updated.', 'success');
        } else {
            set_flash('Unable to update client type.', 'error');
        }
        $stmt->close();
        redirect_to('master_data.php');
    }

    if ($action === 'add_personnel') {
        $full_name = post_value('full_name');
        $position_title = post_value('position_title');
        $district_id = parse_optional_id(post_value('district_id'));
        $area = post_value('area');
        $client_type_id = parse_required_id(post_value('client_type_id'));
        $is_active = normalize_status(post_value('is_active'));

        if ($full_name === '' || $client_type_id === null) {
            set_flash('Invalid personnel input.', 'error');
            redirect_to('master_data.php');
        }

        if ($district_id !== null && !district_exists($conn, $district_id)) {
            set_flash('Invalid district selection.', 'error');
            redirect_to('master_data.php');
        }

        $stmt = $conn->prepare(
            'INSERT INTO personnel (full_name, position_title, district_id, area, client_type_id, is_active)
             VALUES (?, NULLIF(?, \'\'), ?, NULLIF(?, \'\'), ?, ?)'
        );
        $stmt->bind_param('ssisii', $full_name, $position_title, $district_id, $area, $client_type_id, $is_active);
        if ($stmt->execute()) {
            set_flash('Personnel added.', 'success');
        } else {
            set_flash('Unable to add personnel (name+client type may already exist).', 'error');
        }
        $stmt->close();
        redirect_to('master_data.php');
    }

    if ($action === 'update_personnel') {
        $personnel_id = parse_required_id(post_value('personnel_id'));
        $full_name = post_value('full_name');
        $position_title = post_value('position_title');
        $district_id = parse_optional_id(post_value('district_id'));
        $area = post_value('area');
        $client_type_id = parse_required_id(post_value('client_type_id'));
        $is_active = normalize_status(post_value('is_active'));

        if ($personnel_id === null || $full_name === '' || $client_type_id === null) {
            set_flash('Invalid personnel update payload.', 'error');
            redirect_to('master_data.php');
        }

        if ($district_id !== null && !district_exists($conn, $district_id)) {
            set_flash('Invalid district selection.', 'error');
            redirect_to('master_data.php');
        }

        $stmt = $conn->prepare(
            'UPDATE personnel
             SET full_name = ?,
                 position_title = NULLIF(?, \'\'),
                 district_id = ?,
                 area = NULLIF(?, \'\'),
                 client_type_id = ?,
                 is_active = ?
             WHERE id = ?'
        );
        $stmt->bind_param(
            'ssisiii',
            $full_name,
            $position_title,
            $district_id,
            $area,
            $client_type_id,
            $is_active,
            $personnel_id
        );
        if ($stmt->execute()) {
            set_flash('Personnel updated.', 'success');
        } else {
            set_flash('Unable to update personnel.', 'error');
        }
        $stmt->close();
        redirect_to('master_data.php');
    }
}

$district_q = get_value('district_q');
$district_status = get_value('district_status');
if (!in_array($district_status, ['', '1', '0'], true)) {
    $district_status = '';
}
$district_page_raw = get_value('district_page');
$district_page = ($district_page_raw !== '' && ctype_digit($district_page_raw)) ? max(1, (int)$district_page_raw) : 1;
$district_page_size = 20;

$school_q = get_value('school_q');
$school_status = get_value('school_status');
if (!in_array($school_status, ['', '1', '0'], true)) {
    $school_status = '';
}
$school_district_id = get_value('school_district_id');
if ($school_district_id !== '' && !ctype_digit($school_district_id)) {
    $school_district_id = '';
}
$school_page_raw = get_value('school_page');
$school_page = ($school_page_raw !== '' && ctype_digit($school_page_raw)) ? max(1, (int)$school_page_raw) : 1;
$school_page_size = 30;

$district_options = [];
$result = $conn->query(
    'SELECT id, name, is_active
     FROM districts
     ORDER BY name ASC'
);
if ($result instanceof mysqli_result) {
    while ($row = $result->fetch_assoc()) {
        $district_options[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'is_active' => (int)$row['is_active'],
        ];
    }
    $result->free();
}

$district_rows = [];
$district_where = ' WHERE 1=1';
$district_params = [];
$district_types = '';

if ($district_q !== '') {
    $district_where .= ' AND d.name LIKE ?';
    $district_params[] = '%' . $district_q . '%';
    $district_types .= 's';
}
if ($district_status !== '') {
    $district_where .= ' AND d.is_active = ?';
    $district_params[] = (int)$district_status;
    $district_types .= 'i';
}

$district_count_query = 'SELECT COUNT(*) AS total FROM districts d' . $district_where;
$district_count_stmt = $conn->prepare($district_count_query);
if (!empty($district_params)) {
    $district_count_stmt->bind_param($district_types, ...$district_params);
}
$district_count_stmt->execute();
$district_count_result = $district_count_stmt->get_result();
$district_total_rows = (int)$district_count_result->fetch_assoc()['total'];
$district_count_stmt->close();

$district_total_pages = max(1, (int)ceil($district_total_rows / $district_page_size));
$district_page = min($district_page, $district_total_pages);
$district_offset = ($district_page - 1) * $district_page_size;

$district_query =
    'SELECT d.id, d.name, d.is_active,
            COUNT(DISTINCT s.id) AS school_count,
            COUNT(DISTINCT p.id) AS personnel_count
     FROM districts d
     LEFT JOIN schools s ON s.district_id = d.id
     LEFT JOIN personnel p ON p.district_id = d.id' .
    $district_where .
    ' GROUP BY d.id, d.name, d.is_active
      ORDER BY d.name ASC
      LIMIT ? OFFSET ?';
$district_params_with_page = $district_params;
$district_types_with_page = $district_types . 'ii';
$district_params_with_page[] = $district_page_size;
$district_params_with_page[] = $district_offset;

$district_stmt = $conn->prepare($district_query);
$district_stmt->bind_param($district_types_with_page, ...$district_params_with_page);
$district_stmt->execute();
$district_result = $district_stmt->get_result();
if ($district_result instanceof mysqli_result) {
    while ($row = $district_result->fetch_assoc()) {
        $district_rows[] = $row;
    }
}
$district_stmt->close();

$school_rows = [];
$school_where = ' WHERE 1=1';
$school_params = [];
$school_types = '';

if ($school_q !== '') {
    $school_where .= ' AND s.name LIKE ?';
    $school_params[] = '%' . $school_q . '%';
    $school_types .= 's';
}
if ($school_status !== '') {
    $school_where .= ' AND s.is_active = ?';
    $school_params[] = (int)$school_status;
    $school_types .= 'i';
}
if ($school_district_id !== '') {
    $school_where .= ' AND s.district_id = ?';
    $school_params[] = (int)$school_district_id;
    $school_types .= 'i';
}

$school_count_query =
    'SELECT COUNT(*) AS total
     FROM schools s
     INNER JOIN districts d ON d.id = s.district_id' .
    $school_where;
$school_count_stmt = $conn->prepare($school_count_query);
if (!empty($school_params)) {
    $school_count_stmt->bind_param($school_types, ...$school_params);
}
$school_count_stmt->execute();
$school_count_result = $school_count_stmt->get_result();
$school_total_rows = (int)$school_count_result->fetch_assoc()['total'];
$school_count_stmt->close();

$school_total_pages = max(1, (int)ceil($school_total_rows / $school_page_size));
$school_page = min($school_page, $school_total_pages);
$school_offset = ($school_page - 1) * $school_page_size;

$school_query =
    'SELECT s.id, s.name, s.district_id, s.is_active, d.name AS district_name
     FROM schools s
     INNER JOIN districts d ON d.id = s.district_id' .
    $school_where .
    ' ORDER BY d.name ASC, s.name ASC
      LIMIT ? OFFSET ?';
$school_params_with_page = $school_params;
$school_types_with_page = $school_types . 'ii';
$school_params_with_page[] = $school_page_size;
$school_params_with_page[] = $school_offset;

$school_stmt = $conn->prepare($school_query);
$school_stmt->bind_param($school_types_with_page, ...$school_params_with_page);
$school_stmt->execute();
$school_result = $school_stmt->get_result();
if ($school_result instanceof mysqli_result) {
    while ($row = $school_result->fetch_assoc()) {
        $school_rows[] = $row;
    }
}
$school_stmt->close();

$client_types = [];
$result = $conn->query(
    'SELECT ct.id, ct.code, ct.label, ct.is_active, COUNT(p.id) AS personnel_count
     FROM client_types ct
     LEFT JOIN personnel p ON p.client_type_id = ct.id
     GROUP BY ct.id, ct.code, ct.label, ct.is_active
     ORDER BY ct.label ASC'
);
if ($result instanceof mysqli_result) {
    while ($row = $result->fetch_assoc()) {
        $client_types[] = $row;
    }
    $result->free();
}

$personnel_rows = [];
$result = $conn->query(
    'SELECT p.id, p.full_name, p.position_title, p.area, p.is_active,
            p.client_type_id, ct.label AS client_type_label, p.district_id, d.name AS district_name
     FROM personnel p
     INNER JOIN client_types ct ON ct.id = p.client_type_id
     LEFT JOIN districts d ON d.id = p.district_id
     ORDER BY p.full_name ASC
     LIMIT 800'
);
if ($result instanceof mysqli_result) {
    while ($row = $result->fetch_assoc()) {
        $personnel_rows[] = $row;
    }
    $result->free();
}

$page_title = 'Master Data Management';
$styles = ['../css/admin.css'];
require __DIR__ . '/../includes/partials/document_start.php';
?>
    <div class="navbar">
        <h1>Library - DepEd Southern Leyte</h1>
        <div class="navbar-right">
            <a href="dashboard.php" class="btn-nav-link">Dashboard</a>
            <a href="master_data.php" class="btn-nav-link is-active">Master Data</a>
            <span><?php echo h($_SESSION['username']); ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>

    <div class="container">
        <?php require __DIR__ . '/../includes/partials/flash.php'; ?>

        <div class="card mb-16">
            <div class="card-header">
                <h2>Districts (<?php echo number_format($district_total_rows); ?>)</h2>
                <form method="POST" action="" class="inline-form-grid">
                    <input type="hidden" name="action" value="add_district">
                    <div class="filter-group">
                        <label for="new_district_name">District Name</label>
                        <input id="new_district_name" type="text" name="name" placeholder="e.g., San Juan" required>
                    </div>
                    <div class="filter-group">
                        <label for="new_district_active">Status</label>
                        <select id="new_district_active" name="is_active">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="filter-group align-end">
                        <button type="submit" class="btn btn-primary">Add District</button>
                    </div>
                </form>
                <form method="GET" action="" class="filters filters-master">
                    <div class="filter-group">
                        <label for="district_q">Search District</label>
                        <input id="district_q" type="text" name="district_q" value="<?php echo h($district_q); ?>" placeholder="Type district name">
                    </div>
                    <div class="filter-group">
                        <label for="district_status">Status</label>
                        <select id="district_status" name="district_status">
                            <option value="">All statuses</option>
                            <option value="1" <?php echo ($district_status === '1') ? 'selected' : ''; ?>>Active</option>
                            <option value="0" <?php echo ($district_status === '0') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="filter-group align-end">
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="master_data.php?<?php echo h(build_master_query([], ['district_q', 'district_status', 'district_page'])); ?>" class="btn btn-secondary">Clear</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Schools</th>
                            <th>Personnel</th>
                            <th>Status</th>
                            <th>Save</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($district_rows as $district): ?>
                            <?php $district_form_id = 'district_form_' . (int)$district['id']; ?>
                            <tr>
                                <td><?php echo h($district['id']); ?></td>
                                <td><input type="text" name="name" value="<?php echo h($district['name']); ?>" form="<?php echo h($district_form_id); ?>" required></td>
                                <td><?php echo h($district['school_count']); ?></td>
                                <td><?php echo h($district['personnel_count']); ?></td>
                                <td>
                                    <select name="is_active" form="<?php echo h($district_form_id); ?>">
                                        <option value="1" <?php echo ((int)$district['is_active'] === 1) ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ((int)$district['is_active'] === 0) ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </td>
                                <td>
                                    <form id="<?php echo h($district_form_id); ?>" method="POST" action="">
                                        <input type="hidden" name="action" value="update_district">
                                        <input type="hidden" name="district_id" value="<?php echo h((string)$district['id']); ?>">
                                    </form>
                                    <button type="submit" form="<?php echo h($district_form_id); ?>" class="btn btn-secondary">Update</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($district_total_rows > 0): ?>
                <div class="pagination">
                    <div class="pagination-info">
                        Showing <?php echo number_format($district_offset + 1); ?>&ndash;<?php echo number_format(min($district_offset + $district_page_size, $district_total_rows)); ?> of <?php echo number_format($district_total_rows); ?>
                    </div>
                    <div class="pagination-links">
                        <?php if ($district_page > 1): ?>
                            <a href="master_data.php?<?php echo h(build_master_query(['district_page' => $district_page - 1], [])); ?>">&larr; Prev</a>
                        <?php endif; ?>
                        <?php
                        $district_start_page = max(1, $district_page - 2);
                        $district_end_page = min($district_total_pages, $district_page + 2);
                        for ($p = $district_start_page; $p <= $district_end_page; $p++):
                        ?>
                            <?php if ($p === $district_page): ?>
                                <span class="current"><?php echo $p; ?></span>
                            <?php else: ?>
                                <a href="master_data.php?<?php echo h(build_master_query(['district_page' => $p], [])); ?>"><?php echo $p; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <?php if ($district_page < $district_total_pages): ?>
                            <a href="master_data.php?<?php echo h(build_master_query(['district_page' => $district_page + 1], [])); ?>">Next &rarr;</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="card mb-16">
            <div class="card-header">
                <h2>Schools (<?php echo number_format($school_total_rows); ?>)</h2>
                <form method="POST" action="" class="inline-form-grid">
                    <input type="hidden" name="action" value="add_school">
                    <div class="filter-group">
                        <label for="new_school_district_id">District</label>
                        <select id="new_school_district_id" name="district_id" required>
                            <option value="" selected disabled>Select district</option>
                            <?php foreach ($district_options as $district): ?>
                                <option value="<?php echo h((string)$district['id']); ?>">
                                    <?php echo h($district['name'] . (((int)$district['is_active'] === 1) ? '' : ' (Inactive)')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="new_school_name">School Name</label>
                        <input id="new_school_name" type="text" name="name" placeholder="e.g., Libagon NHS" required>
                    </div>
                    <div class="filter-group">
                        <label for="new_school_active">Status</label>
                        <select id="new_school_active" name="is_active">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="filter-group align-end">
                        <button type="submit" class="btn btn-primary">Add School</button>
                    </div>
                </form>
                <form method="GET" action="" class="filters filters-master">
                    <div class="filter-group">
                        <label for="school_q">Search School</label>
                        <input id="school_q" type="text" name="school_q" value="<?php echo h($school_q); ?>" placeholder="Type school name">
                    </div>
                    <div class="filter-group">
                        <label for="school_district_id">District</label>
                        <select id="school_district_id" name="school_district_id">
                            <option value="">All districts</option>
                            <?php foreach ($district_options as $district): ?>
                                <option value="<?php echo h((string)$district['id']); ?>" <?php echo ((string)$district['id'] === $school_district_id) ? 'selected' : ''; ?>>
                                    <?php echo h($district['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="school_status">Status</label>
                        <select id="school_status" name="school_status">
                            <option value="">All statuses</option>
                            <option value="1" <?php echo ($school_status === '1') ? 'selected' : ''; ?>>Active</option>
                            <option value="0" <?php echo ($school_status === '0') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="filter-group align-end">
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="master_data.php?<?php echo h(build_master_query([], ['school_q', 'school_status', 'school_district_id', 'school_page'])); ?>" class="btn btn-secondary">Clear</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>District</th>
                            <th>School</th>
                            <th>Status</th>
                            <th>Save</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($school_rows as $school): ?>
                            <?php $school_form_id = 'school_form_' . (int)$school['id']; ?>
                            <tr>
                                <td><?php echo h($school['id']); ?></td>
                                <td>
                                    <select name="district_id" form="<?php echo h($school_form_id); ?>" required>
                                        <?php foreach ($district_options as $district): ?>
                                            <option
                                                value="<?php echo h((string)$district['id']); ?>"
                                                <?php echo ((int)$district['id'] === (int)$school['district_id']) ? 'selected' : ''; ?>
                                            >
                                                <?php echo h($district['name'] . (((int)$district['is_active'] === 1) ? '' : ' (Inactive)')); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="text" name="name" value="<?php echo h($school['name']); ?>" form="<?php echo h($school_form_id); ?>" required></td>
                                <td>
                                    <select name="is_active" form="<?php echo h($school_form_id); ?>">
                                        <option value="1" <?php echo ((int)$school['is_active'] === 1) ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ((int)$school['is_active'] === 0) ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </td>
                                <td>
                                    <form id="<?php echo h($school_form_id); ?>" method="POST" action="">
                                        <input type="hidden" name="action" value="update_school">
                                        <input type="hidden" name="school_id" value="<?php echo h((string)$school['id']); ?>">
                                    </form>
                                    <button type="submit" form="<?php echo h($school_form_id); ?>" class="btn btn-secondary">Update</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($school_total_rows > 0): ?>
                <div class="pagination">
                    <div class="pagination-info">
                        Showing <?php echo number_format($school_offset + 1); ?>&ndash;<?php echo number_format(min($school_offset + $school_page_size, $school_total_rows)); ?> of <?php echo number_format($school_total_rows); ?>
                    </div>
                    <div class="pagination-links">
                        <?php if ($school_page > 1): ?>
                            <a href="master_data.php?<?php echo h(build_master_query(['school_page' => $school_page - 1], [])); ?>">&larr; Prev</a>
                        <?php endif; ?>
                        <?php
                        $school_start_page = max(1, $school_page - 2);
                        $school_end_page = min($school_total_pages, $school_page + 2);
                        for ($p = $school_start_page; $p <= $school_end_page; $p++):
                        ?>
                            <?php if ($p === $school_page): ?>
                                <span class="current"><?php echo $p; ?></span>
                            <?php else: ?>
                                <a href="master_data.php?<?php echo h(build_master_query(['school_page' => $p], [])); ?>"><?php echo $p; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <?php if ($school_page < $school_total_pages): ?>
                            <a href="master_data.php?<?php echo h(build_master_query(['school_page' => $school_page + 1], [])); ?>">Next &rarr;</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="card mb-16">
            <div class="card-header">
                <h2>Client Types</h2>
                <form method="POST" action="" class="inline-form-grid">
                    <input type="hidden" name="action" value="add_client_type">
                    <div class="filter-group">
                        <label for="new_code">Code</label>
                        <input id="new_code" type="text" name="code" placeholder="division_office_personnel" required>
                    </div>
                    <div class="filter-group">
                        <label for="new_label">Label</label>
                        <input id="new_label" type="text" name="label" placeholder="Division Office Personnel" required>
                    </div>
                    <div class="filter-group">
                        <label for="new_client_type_active">Status</label>
                        <select id="new_client_type_active" name="is_active">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="filter-group align-end">
                        <button type="submit" class="btn btn-primary">Add Client Type</button>
                    </div>
                </form>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Label</th>
                            <th>Personnel Count</th>
                            <th>Status</th>
                            <th>Save</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($client_types as $type): ?>
                            <?php $client_form_id = 'client_type_form_' . (int)$type['id']; ?>
                            <tr>
                                <td><?php echo h($type['id']); ?></td>
                                <td><input type="text" name="code" value="<?php echo h($type['code']); ?>" form="<?php echo h($client_form_id); ?>" required></td>
                                <td><input type="text" name="label" value="<?php echo h($type['label']); ?>" form="<?php echo h($client_form_id); ?>" required></td>
                                <td><?php echo h($type['personnel_count']); ?></td>
                                <td>
                                    <select name="is_active" form="<?php echo h($client_form_id); ?>">
                                        <option value="1" <?php echo ((int)$type['is_active'] === 1) ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ((int)$type['is_active'] === 0) ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </td>
                                <td>
                                    <form id="<?php echo h($client_form_id); ?>" method="POST" action="">
                                        <input type="hidden" name="action" value="update_client_type">
                                        <input type="hidden" name="client_type_id" value="<?php echo h((string)$type['id']); ?>">
                                    </form>
                                    <button type="submit" form="<?php echo h($client_form_id); ?>" class="btn btn-secondary">Update</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Personnel</h2>
                <form method="POST" action="" class="inline-form-grid inline-form-grid-personnel">
                    <input type="hidden" name="action" value="add_personnel">
                    <div class="filter-group">
                        <label for="new_full_name">Full Name</label>
                        <input id="new_full_name" type="text" name="full_name" required>
                    </div>
                    <div class="filter-group">
                        <label for="new_position_title">Position</label>
                        <input id="new_position_title" type="text" name="position_title" placeholder="Optional">
                    </div>
                    <div class="filter-group">
                        <label for="new_client_type_id">Client Type</label>
                        <select id="new_client_type_id" name="client_type_id" required>
                            <?php foreach ($client_types as $type): ?>
                                <option value="<?php echo h((string)$type['id']); ?>"><?php echo h($type['label']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="new_district_id">District</label>
                        <select id="new_district_id" name="district_id">
                            <option value="">None</option>
                            <?php foreach ($district_options as $district): ?>
                                <option value="<?php echo h((string)$district['id']); ?>">
                                    <?php echo h($district['name'] . (((int)$district['is_active'] === 1) ? '' : ' (Inactive)')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="new_area">Area</label>
                        <input id="new_area" type="text" name="area" placeholder="Optional">
                    </div>
                    <div class="filter-group">
                        <label for="new_personnel_active">Status</label>
                        <select id="new_personnel_active" name="is_active">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="filter-group align-end">
                        <button type="submit" class="btn btn-primary">Add Personnel</button>
                    </div>
                </form>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Position</th>
                            <th>Client Type</th>
                            <th>District</th>
                            <th>Area</th>
                            <th>Status</th>
                            <th>Save</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($personnel_rows as $person): ?>
                            <?php $person_form_id = 'personnel_form_' . (int)$person['id']; ?>
                            <tr>
                                <td><?php echo h($person['id']); ?></td>
                                <td><input type="text" name="full_name" value="<?php echo h($person['full_name']); ?>" form="<?php echo h($person_form_id); ?>" required></td>
                                <td><input type="text" name="position_title" value="<?php echo h((string)$person['position_title']); ?>" form="<?php echo h($person_form_id); ?>"></td>
                                <td>
                                    <select name="client_type_id" form="<?php echo h($person_form_id); ?>" required>
                                        <?php foreach ($client_types as $type): ?>
                                            <option
                                                value="<?php echo h((string)$type['id']); ?>"
                                                <?php echo ((int)$type['id'] === (int)$person['client_type_id']) ? 'selected' : ''; ?>
                                            >
                                                <?php echo h($type['label']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="district_id" form="<?php echo h($person_form_id); ?>">
                                        <option value="">None</option>
                                        <?php foreach ($district_options as $district): ?>
                                            <option
                                                value="<?php echo h((string)$district['id']); ?>"
                                                <?php echo ((int)$district['id'] === (int)$person['district_id']) ? 'selected' : ''; ?>
                                            >
                                                <?php echo h($district['name'] . (((int)$district['is_active'] === 1) ? '' : ' (Inactive)')); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="text" name="area" value="<?php echo h((string)$person['area']); ?>" form="<?php echo h($person_form_id); ?>"></td>
                                <td>
                                    <select name="is_active" form="<?php echo h($person_form_id); ?>">
                                        <option value="1" <?php echo ((int)$person['is_active'] === 1) ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ((int)$person['is_active'] === 0) ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </td>
                                <td>
                                    <form id="<?php echo h($person_form_id); ?>" method="POST" action="">
                                        <input type="hidden" name="action" value="update_personnel">
                                        <input type="hidden" name="personnel_id" value="<?php echo h((string)$person['id']); ?>">
                                    </form>
                                    <button type="submit" form="<?php echo h($person_form_id); ?>" class="btn btn-secondary">Update</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php
$conn->close();
require __DIR__ . '/../includes/partials/document_end.php';
?>
