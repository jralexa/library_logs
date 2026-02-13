<?php
// Database connection and session helpers.
require_once '../config/database.php';
require_once '../config/session.php';

// Only admins can export data.
requireAdmin();

$filter_date = isset($_GET['filter_date']) ? trim((string)$_GET['filter_date']) : '';
$filter_month = isset($_GET['filter_month']) ? trim((string)$_GET['filter_month']) : '';
$filter_name = isset($_GET['filter_name']) ? trim((string)$_GET['filter_name']) : '';
$filter_client_type = isset($_GET['filter_client_type']) ? trim((string)$_GET['filter_client_type']) : '';
$filter_district_id = isset($_GET['filter_district_id']) ? trim((string)$_GET['filter_district_id']) : '';
$filter_school_id = isset($_GET['filter_school_id']) ? trim((string)$_GET['filter_school_id']) : '';
$filter_organization = isset($_GET['filter_organization']) ? trim((string)$_GET['filter_organization']) : '';

if ($filter_month !== '' && !preg_match('/^\d{4}\-(0[1-9]|1[0-2])$/', $filter_month)) {
    $filter_month = '';
}
if ($filter_district_id !== '' && !ctype_digit($filter_district_id)) {
    $filter_district_id = '';
}
if ($filter_school_id !== '' && !ctype_digit($filter_school_id)) {
    $filter_school_id = '';
}

$query =
    'SELECT le.date, le.time_in, le.name, le.client_type, le.position, d.name AS district_name,
            s.name AS school_name, le.organization_name, le.purpose
     FROM logbook_entries le
     LEFT JOIN districts d ON d.id = le.district_id
     LEFT JOIN schools s ON s.id = le.school_id
     WHERE 1=1';
$params = [];
$types = '';

if ($filter_date !== '') {
    $query .= ' AND le.date = ?';
    $params[] = $filter_date;
    $types .= 's';
} elseif ($filter_month !== '') {
    $query .= ' AND DATE_FORMAT(le.date, "%Y-%m") = ?';
    $params[] = $filter_month;
    $types .= 's';
}

if ($filter_name !== '') {
    $query .= ' AND le.name LIKE ?';
    $params[] = '%' . $filter_name . '%';
    $types .= 's';
}

if ($filter_client_type !== '') {
    $query .= ' AND le.client_type LIKE ?';
    $params[] = '%' . $filter_client_type . '%';
    $types .= 's';
}

if ($filter_district_id !== '') {
    $query .= ' AND le.district_id = ?';
    $params[] = (int)$filter_district_id;
    $types .= 'i';
}

if ($filter_school_id !== '') {
    $query .= ' AND le.school_id = ?';
    $params[] = (int)$filter_school_id;
    $types .= 'i';
}

if ($filter_organization !== '') {
    $query .= ' AND le.organization_name LIKE ?';
    $params[] = '%' . $filter_organization . '%';
    $types .= 's';
}

$query .= ' ORDER BY le.date DESC, le.time_in DESC';

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=library_logbook_' . date('Y-m-d_His') . '.csv');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

fputcsv($output, ['Date', 'Time In', 'Name', 'Client Type', 'Position', 'District', 'School', 'Other Organization', 'Purpose']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['date'],
        $row['time_in'],
        $row['name'],
        $row['client_type'],
        $row['position'],
        $row['district_name'],
        $row['school_name'],
        $row['organization_name'],
        $row['purpose'],
    ]);
}

fclose($output);
$stmt->close();
$conn->close();
exit();
?>
