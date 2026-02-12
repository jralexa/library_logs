<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
requireAdmin();

function is_valid_client_type_code(string $code): bool
{
    return (bool)preg_match('/^[a-z0-9_]+$/', $code);
}

function resolve_nullable_district_id(string $raw): ?int
{
    if ($raw === '') {
        return null;
    }

    if (!ctype_digit($raw)) {
        return null;
    }

    return (int)$raw;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = post_value('action');

    if ($action === 'add_client_type') {
        $code = strtolower(post_value('code'));
        $label = post_value('label');
        $is_active = post_value('is_active') === '1' ? 1 : 0;

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
        $id_raw = post_value('client_type_id');
        $code = strtolower(post_value('code'));
        $label = post_value('label');
        $is_active = post_value('is_active') === '1' ? 1 : 0;

        if (!ctype_digit($id_raw) || !is_valid_client_type_code($code) || $label === '') {
            set_flash('Invalid client type update payload.', 'error');
            redirect_to('master_data.php');
        }

        $id = (int)$id_raw;
        $stmt = $conn->prepare(
            'UPDATE client_types
             SET code = ?, label = ?, is_active = ?
             WHERE id = ?'
        );
        $stmt->bind_param('ssii', $code, $label, $is_active, $id);
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
        $district_raw = post_value('district_id');
        $district_id = resolve_nullable_district_id($district_raw);
        $area = post_value('area');
        $client_type_raw = post_value('client_type_id');
        $is_active = post_value('is_active') === '1' ? 1 : 0;

        if ($full_name === '' || !ctype_digit($client_type_raw)) {
            set_flash('Invalid personnel input.', 'error');
            redirect_to('master_data.php');
        }

        $client_type_id = (int)$client_type_raw;
        if ($district_raw !== '' && $district_id === null) {
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
        $id_raw = post_value('personnel_id');
        $full_name = post_value('full_name');
        $position_title = post_value('position_title');
        $district_raw = post_value('district_id');
        $district_id = resolve_nullable_district_id($district_raw);
        $area = post_value('area');
        $client_type_raw = post_value('client_type_id');
        $is_active = post_value('is_active') === '1' ? 1 : 0;

        if (!ctype_digit($id_raw) || $full_name === '' || !ctype_digit($client_type_raw)) {
            set_flash('Invalid personnel update payload.', 'error');
            redirect_to('master_data.php');
        }

        if ($district_raw !== '' && $district_id === null) {
            set_flash('Invalid district selection.', 'error');
            redirect_to('master_data.php');
        }

        $id = (int)$id_raw;
        $client_type_id = (int)$client_type_raw;

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
        $stmt->bind_param('ssisiii', $full_name, $position_title, $district_id, $area, $client_type_id, $is_active, $id);
        if ($stmt->execute()) {
            set_flash('Personnel updated.', 'success');
        } else {
            set_flash('Unable to update personnel.', 'error');
        }
        $stmt->close();
        redirect_to('master_data.php');
    }
}

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

$districts = load_districts($conn);

$personnel_rows = [];
$result = $conn->query(
    'SELECT p.id, p.full_name, p.position_title, p.area, p.is_active,
            p.client_type_id, ct.label AS client_type_label, p.district_id, d.name AS district_name
     FROM personnel p
     INNER JOIN client_types ct ON ct.id = p.client_type_id
     LEFT JOIN districts d ON d.id = p.district_id
     ORDER BY p.full_name ASC
     LIMIT 500'
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
                            <?php foreach ($districts as $district): ?>
                                <option value="<?php echo h((string)$district['id']); ?>"><?php echo h($district['name']); ?></option>
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
                                        <?php foreach ($districts as $district): ?>
                                            <option
                                                value="<?php echo h((string)$district['id']); ?>"
                                                <?php echo ((int)$district['id'] === (int)$person['district_id']) ? 'selected' : ''; ?>
                                            >
                                                <?php echo h($district['name']); ?>
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
