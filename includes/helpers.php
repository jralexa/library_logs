<?php
declare(strict_types=1);
// Generic helper utilities used across pages.

function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function post_value(string $key): string
{
    return isset($_POST[$key]) ? trim((string)$_POST[$key]) : '';
}

function get_value(string $key): string
{
    return isset($_GET[$key]) ? trim((string)$_GET[$key]) : '';
}

function set_flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type,
    ];
}

function consume_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $flash;
}

function redirect_to(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function redirect_self(): void
{
    $path = strtok($_SERVER['REQUEST_URI'], '?');
    redirect_to($path ?: '/');
}

function client_type_badge_class(string $value): string
{
    $normalized = strtolower(trim($value));
    $normalized = str_replace([' ', '-'], '_', $normalized);

    if (in_array($normalized, ['field', 'field_personnel'], true)) {
        return 'badge-field';
    }

    if (in_array($normalized, ['division', 'division_office_staff', 'division_office_personnel'], true)) {
        return 'badge-staff';
    }

    if ($normalized === 'visitor') {
        return 'badge-visitor';
    }

    return 'badge-other';
}

function load_client_types(mysqli $conn): array
{
    $rows = [];
    $result = $conn->query(
        'SELECT id, code, label
         FROM client_types
         WHERE is_active = 1
         ORDER BY label ASC'
    );

    if ($result instanceof mysqli_result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = [
                'id' => (int)$row['id'],
                'code' => $row['code'],
                'label' => $row['label'],
            ];
        }
        $result->free();
    }

    return $rows;
}

function load_personnel_by_client_type(mysqli $conn): array
{
    $map = [];
    $result = $conn->query(
        'SELECT p.id, p.full_name, p.position_title, ct.id AS client_type_id
         FROM personnel p
         INNER JOIN client_types ct ON ct.id = p.client_type_id
         WHERE p.is_active = 1 AND ct.is_active = 1
         ORDER BY ct.label ASC, p.full_name ASC'
    );

    if ($result instanceof mysqli_result) {
        while ($row = $result->fetch_assoc()) {
            $clientTypeId = (string)$row['client_type_id'];
            if (!isset($map[$clientTypeId])) {
                $map[$clientTypeId] = [];
            }

            $map[$clientTypeId][] = [
                'id' => (int)$row['id'],
                'full_name' => $row['full_name'],
                'position_title' => $row['position_title'],
            ];
        }
        $result->free();
    }

    return $map;
}

function load_districts(mysqli $conn): array
{
    $rows = [];
    $result = $conn->query('SELECT id, name FROM districts WHERE is_active = 1 ORDER BY name ASC');
    if ($result instanceof mysqli_result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $result->free();
    }

    return $rows;
}

function load_schools_by_district(mysqli $conn): array
{
    $map = [];
    $result = $conn->query(
        'SELECT id, district_id, name
         FROM schools
         WHERE is_active = 1
         ORDER BY district_id ASC, name ASC'
    );

    if ($result instanceof mysqli_result) {
        while ($row = $result->fetch_assoc()) {
            $districtId = (int)$row['district_id'];
            if (!isset($map[$districtId])) {
                $map[$districtId] = [];
            }

            $map[$districtId][] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
            ];
        }
        $result->free();
    }

    return $map;
}

function organization_label(?string $districtName, ?string $schoolName, ?string $organizationName): string
{
    if ($schoolName && $districtName) {
        return $schoolName . ', ' . $districtName;
    }

    if ($schoolName) {
        return $schoolName;
    }

    if ($districtName) {
        return $districtName;
    }

    if ($organizationName) {
        return $organizationName;
    }

    return '-';
}
