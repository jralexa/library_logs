<?php
declare(strict_types=1);
/**
 * Library Visitor Log System - Main Page
 * DepEd Southern Leyte Division Library
 */

// Bootstrap shared configuration, session, and helper utilities.
require_once __DIR__ . '/includes/bootstrap.php';

$districts = load_districts($conn);
$schools_by_district = load_schools_by_district($conn);
$client_types = load_client_types($conn);
$personnel_by_client_type = load_personnel_by_client_type($conn);

// Handle form submission for new visitor log entries.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = '';
    $personnel_id = null;
    $client_type = '';
    $client_type_id = 0;
    $position = post_value('position');
    $organization_type = post_value('organization_type');
    $purpose_choice = post_value('purpose_choice');
    $purpose = post_value('purpose');
    $district_id = null;
    $school_id = null;
    $organization_name = null;
    $organization_valid = false;

    $client_type_raw = post_value('client_type');
    if (ctype_digit($client_type_raw)) {
        $candidate_client_type_id = (int)$client_type_raw;
        $stmt = $conn->prepare(
            'SELECT id, label
             FROM client_types
             WHERE id = ? AND is_active = 1
             LIMIT 1'
        );
        $stmt->bind_param('i', $candidate_client_type_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row) {
            $client_type_id = (int)$row['id'];
            $client_type = $row['label'];
        }
        $stmt->close();
    }

    $personnel_choice = post_value('name');
    if ($personnel_choice === 'Other') {
        $name = post_value('name_other');
    } elseif ($client_type_id > 0 && ctype_digit($personnel_choice)) {
        $candidate_personnel_id = (int)$personnel_choice;
        $stmt = $conn->prepare(
            'SELECT id, full_name, position_title
             FROM personnel
             WHERE id = ? AND client_type_id = ? AND is_active = 1
             LIMIT 1'
        );
        $stmt->bind_param('ii', $candidate_personnel_id, $client_type_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row) {
            $personnel_id = (int)$row['id'];
            $name = $row['full_name'];
            if ($position === '' && !empty($row['position_title'])) {
                $position = $row['position_title'];
            }
        }
        $stmt->close();
    }

    if ($organization_type === 'district_school') {
        $district_raw = post_value('district_id');
        $school_raw = post_value('school_id');

        if (ctype_digit($district_raw) && ctype_digit($school_raw)) {
            $candidate_district_id = (int)$district_raw;
            $candidate_school_id = (int)$school_raw;

            // Validate district-school relationship to prevent tampered submissions.
            $stmt = $conn->prepare(
                'SELECT s.id
                 FROM schools s
                 INNER JOIN districts d ON d.id = s.district_id
                 WHERE s.id = ? AND s.district_id = ? AND s.is_active = 1 AND d.is_active = 1
                 LIMIT 1'
            );
            $stmt->bind_param('ii', $candidate_school_id, $candidate_district_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $district_id = $candidate_district_id;
                $school_id = $candidate_school_id;
                $organization_valid = true;
            }

            $stmt->close();
        }
    } elseif ($organization_type === 'division_office') {
        $organization_name = 'Division Office';
        $organization_valid = true;
    } elseif ($organization_type === 'other') {
        $organization_name = post_value('organization_name');
        $organization_valid = $organization_name !== '';
    }

    if ($purpose === '' && $purpose_choice !== '') {
        $purpose = $purpose_choice;
    }

    // Require all fields before insert.
    if ($name && $client_type && $client_type_id > 0 && $position && $purpose && $organization_valid) {
        $log_date = date('Y-m-d');
        $current_time = date('H:i:s');

        // Persist the log entry using a prepared statement.
        $stmt = $conn->prepare(
            'INSERT INTO logbook_entries (
                date, time_in, name, personnel_id, client_type, client_type_id, position, district_id, school_id, organization_name, purpose
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'sssisisiiss',
            $log_date,
            $current_time,
            $name,
            $personnel_id,
            $client_type,
            $client_type_id,
            $position,
            $district_id,
            $school_id,
            $organization_name,
            $purpose
        );

        // Store a flash message for the next request.
        if ($stmt->execute()) {
            set_flash("Visit logged successfully for {$name}", 'success');
        } else {
            set_flash('Error recording visit. Please try again.', 'error');
        }

        $stmt->close();
        // Redirect to avoid form resubmission on refresh.
        redirect_self();
    }

    // Missing required data.
    set_flash('Please complete all required fields.', 'error');
    redirect_self();
}

// Page metadata and assets.
$page_title = 'DepEd Southern Leyte Division Library';
$styles = ['css/style.css'];
require __DIR__ . '/includes/partials/document_start.php';
?>
    <div class="container">
        <!-- Header with logo and text -->
        <div class="header">
            <a href="admin/login.php" aria-label="Admin Login">
                <img src="images/deped.jpg" alt="DepEd Logo" class="logo">
            </a>
            <div class="header-text">
                <h1>DepEd Southern Leyte Division Library</h1>
                <p>Library Visitor Log System</p>
            </div>
        </div>

        <!-- Date display -->
        <div class="datetime-display">
            <div class="date" id="currentDate"></div>
        </div>

        <!-- Flash message -->
        <?php require __DIR__ . '/includes/partials/flash.php'; ?>

        <!-- Log entry form -->
        <form method="POST" action="" id="logForm" class="form-grid">
            <div class="form-group">
                <label for="client_type">Client Categories <span class="required">*</span></label>
                <select id="client_type" name="client_type" required>
                    <option value="" selected disabled>Select a client type</option>
                    <?php if (!empty($client_types)): ?>
                        <?php foreach ($client_types as $client_type_option): ?>
                            <option value="<?php echo h((string)$client_type_option['id']); ?>" data-client-type-code="<?php echo h($client_type_option['code']); ?>">
                                <?php echo h($client_type_option['label']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No client types configured</option>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="name">Full Name <span class="required">*</span></label>
                <select id="name" name="name" required data-other-input="name_other" onchange="toggleOtherInput(this)">
                    <option value="" selected disabled>Select a name</option>
                    <option value="Other">Other</option>
                </select>
                <input type="text" id="name_other" name="name_other" class="other-input is-hidden" placeholder="Enter full name">
            </div>
            
            <div class="form-group">
                <label for="position">Position/Designation <span class="required">*</span></label>
                <input type="text" id="position" name="position" required placeholder="e.g., AO, Head Teacher, Utility, ADAS, etc.">
            </div>
            
            <div class="form-group">
                <label for="organization_type">Organization Category <span class="required">*</span></label>
                <select id="organization_type" name="organization_type" required>
                    <option value="" selected disabled>Select organization category</option>
                    <option value="district_school">School in a District</option>
                    <option value="division_office">Division Office</option>
                    <option value="other">Other Organization</option>
                </select>
            </div>

            <div class="form-group is-hidden" id="districtGroup">
                <label for="district_id">District <span class="required">*</span></label>
                <select id="district_id" name="district_id" data-role="district-select" disabled>
                    <option value="" selected disabled>Select a district</option>
                    <?php foreach ($districts as $district): ?>
                        <option value="<?php echo h((string)$district['id']); ?>"><?php echo h($district['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group is-hidden" id="schoolGroup">
                <label for="school_id">School <span class="required">*</span></label>
                <select id="school_id" name="school_id" data-role="school-select" disabled>
                    <option value="" selected disabled>Select a school</option>
                </select>
            </div>

            <div class="form-group is-hidden" id="organizationNameGroup">
                <label for="organization_name">Organization Name <span class="required">*</span></label>
                <input type="text" id="organization_name" name="organization_name" class="other-input" placeholder="Enter organization name">
            </div>
            
            <div class="form-group full">
                <label for="purpose_choice">Purpose of Visit (Quick Select)</label>
                <select id="purpose_choice" name="purpose_choice">
                    <option value="" selected>Choose from common purposes</option>
                    <option value="To access information, knowledge, and educational resources.">To access information, knowledge, and educational resources.</option>
                    <option value="To use technology resources (access to computers, internet, and digital resources).">To use technology resources (access to computers, internet, and digital resources).</option>
                    <option value="To conduct research on reliable and credible sources for in-depth study.">To conduct research on reliable and credible sources for in-depth study.</option>
                    <option value="To develop reading habits (regular reading for knowledge and enjoyment).">To develop reading habits (regular reading for knowledge and enjoyment).</option>
                    <option value="To borrow books and access other educational materials for academic purposes.">To borrow books and access other educational materials for academic purposes.</option>
                    <option value="To return borrowed books and other educational materials.">To return borrowed books and other educational materials.</option>
                    <option value="For clearance (retirement, maternity leave, travel abroad, medical reasons).">For clearance (retirement, maternity leave, travel abroad, medical reasons).</option>
                    <option value="To use the library as a venue for conferences, meetings, demonstrations, and other academic or educational activities.">To use the library as a venue for conferences, meetings, demonstrations, and other academic or educational activities.</option>
                    <option value="To meet with the librarian regarding">To meet with the librarian regarding</option>
                    <option value="Other">Other (type in textarea)</option>
                </select>
            </div>

            <div class="form-group full">
                <label for="purpose">Purpose of Visit <span class="required">*</span></label>
                <textarea id="purpose" name="purpose" required placeholder="e.g., Use computer, Visit, Reading books, etc."></textarea>
            </div>
            
            <button type="submit" class="btn-time-in form-group full">
                SUBMIT LOG
            </button>
        </form>
    </div>

<?php
$inline_script = <<<'HTML'
<script>
window.schoolMap = __SCHOOL_MAP__;
window.personnelMap = __PERSONNEL_MAP__;
</script>
HTML;
$inline_script = str_replace(
    '__SCHOOL_MAP__',
    json_encode($schools_by_district, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
    $inline_script
);
$inline_script = str_replace(
    '__PERSONNEL_MAP__',
    json_encode($personnel_by_client_type, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
    $inline_script
);
echo $inline_script;
$scripts = ['js/main.js'];
require __DIR__ . '/includes/partials/document_end.php';
?>
