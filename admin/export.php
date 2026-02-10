<?php
// Database connection and session helpers.
require_once '../config/database.php';
require_once '../config/session.php';

// Only admins can export data.
requireAdmin();

// Get filter parameters.
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';
$filter_name = isset($_GET['filter_name']) ? $_GET['filter_name'] : '';
$filter_client_type = isset($_GET['filter_client_type']) ? $_GET['filter_client_type'] : '';

// Build query with optional filters.
$query = "SELECT id, date, time_in, name, client_type, position, district, purpose FROM logbook_entries WHERE 1=1";
$params = [];
$types = "";

if ($filter_date) {
    $query .= " AND date = ?";
    $params[] = $filter_date;
    $types .= "s";
}

if ($filter_name) {
    $query .= " AND name LIKE ?";
    $params[] = "%$filter_name%";
    $types .= "s";
}

if ($filter_client_type) {
    $query .= " AND client_type LIKE ?";
    $params[] = "%$filter_client_type%";
    $types .= "s";
}

$query .= " ORDER BY date DESC, time_in DESC";

// Execute the query.
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Set headers for CSV download.
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=library_logbook_' . date('Y-m-d_His') . '.csv');

// Create output stream.
$output = fopen('php://output', 'w');

// Add BOM for Excel UTF-8 compatibility.
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add column headers.
fputcsv($output, ['Date', 'Time In', 'Name', 'Client Type', 'Position', 'District', 'Purpose']);

// Add data rows.
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['date'],
        $row['time_in'],
        $row['name'],
        $row['client_type'],
        $row['position'],
        $row['district'],
        $row['purpose']
    ]);
}

// Finish the response.
fclose($output);
$stmt->close();
$conn->close();
exit();
?>
