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

// Handle form submission for new visitor log entries.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = post_value('name');
    $client_type = post_value('client_type');
    $position = post_value('position');
    $organization_type = post_value('organization_type');
    $purpose = post_value('purpose');
    $district_id = null;
    $school_id = null;
    $organization_name = null;
    $organization_valid = false;

    if ($name === 'Other') {
        $name = post_value('name_other');
    }

    if ($client_type === 'Other') {
        $client_type = post_value('client_type_other');
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

    // Require all fields before insert.
    if ($name && $client_type && $position && $purpose && $organization_valid) {
        $log_date = date('Y-m-d');
        $current_time = date('H:i:s');

        // Persist the log entry using a prepared statement.
        $stmt = $conn->prepare(
            'INSERT INTO logbook_entries (
                date, time_in, name, client_type, position, district_id, school_id, organization_name, purpose
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'sssssiiss',
            $log_date,
            $current_time,
            $name,
            $client_type,
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
            <img src="images/deped.jpg" alt="DepEd Logo" class="logo">
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
                <label for="name">Full Name <span class="required">*</span></label>
                <select id="name" name="name" required data-other-input="name_other" onchange="toggleOtherInput(this)">
                    <option value="" selected disabled>Select a name</option>
                    <option value="Maria Santos">Maria Santos</option>
                    <option value="Juan Dela Cruz">Juan Dela Cruz</option>
                    <option value="Ana Reyes">Ana Reyes</option>
                    <option value="Carlo Mendoza">Carlo Mendoza</option>
                    <option value="Fatima Lopez">Fatima Lopez</option>
                    <option value="Other">Other</option>
                </select>
                <input type="text" id="name_other" name="name_other" class="other-input is-hidden" placeholder="Enter full name">
            </div>
            
            <div class="form-group">
                <label for="client_type">Visitor Type <span class="required">*</span></label>
                <select id="client_type" name="client_type" required data-other-input="client_type_other" onchange="toggleOtherInput(this)">
                    <option value="" selected disabled>Select a client type</option>
                     <option value="Field">Field Personnel</option>
                     <option value="Division">Division Office Staff</option>
                     <option value="Visitor">External Visitor</option>
                    <option value="Other">Other</option>
                </select>
                <input type="text" id="client_type_other" name="client_type_other" class="other-input is-hidden" placeholder="Enter client type">
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
</script>
HTML;
$inline_script = str_replace(
    '__SCHOOL_MAP__',
    json_encode($schools_by_district, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
    $inline_script
);
echo $inline_script;
$scripts = ['js/main.js'];
require __DIR__ . '/includes/partials/document_end.php';
?>
