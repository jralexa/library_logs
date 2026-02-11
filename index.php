<?php
declare(strict_types=1);
/**
 * Library Visitor Log System - Main Page
 * DepEd Southern Leyte Division Library
 */

// Bootstrap shared configuration, session, and helper utilities.
require_once __DIR__ . '/includes/bootstrap.php';

// Handle form submission for new visitor log entries.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = post_value('name');
    $client_type = post_value('client_type');
    $position = post_value('position');
    $district = post_value('district');
    $purpose = post_value('purpose');

    if ($name === 'Other') {
        $name = post_value('name_other');
    }

    if ($client_type === 'Other') {
        $client_type = post_value('client_type_other');
    }

    if ($district === 'Other') {
        $district = post_value('district_other');
    }

    // Require all fields before insert.
    if ($name && $client_type && $position && $district && $purpose) {
        $log_date = date('Y-m-d');
        $current_time = date('H:i:s');

        // Persist the log entry using a prepared statement.
        $stmt = $conn->prepare(
            "INSERT INTO logbook_entries (date, time_in, name, client_type, position, district, purpose)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssssss", $log_date, $current_time, $name, $client_type, $position, $district, $purpose);

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
                <input type="text" id="name_other" name="name_other" class="other-input is-hidden" placeholder="Enter full name" style="display: none;">
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
                <input type="text" id="client_type_other" name="client_type_other" class="other-input is-hidden" placeholder="Enter client type" style="display: none;">
            </div>
            
            <div class="form-group">
                <label for="position">Position/Designation <span class="required">*</span></label>
                <input type="text" id="position" name="position" required placeholder="e.g., AO, Head Teacher, Utility, ADAS, etc.">
            </div>
            
            <div class="form-group">
                <label for="district">Organization <span class="required">*</span></label>
                <select id="district" name="district" required data-other-input="district_other" onchange="toggleOtherInput(this)">
                    <option value="" selected disabled>Select a Organization</option>
                    <option value="Division Office">Division Office</option>
                    <option value="Maasin City District">Maasin City District</option>
                    <option value="Bontoc I District">Bontoc I District</option>
                    <option value="Bontoc II District">Bontoc II District</option>
                    <option value="Hinunangan District">Hinunangan District</option>
                    <option value="Hinundayan District">Hinundayan District</option>
                    <option value="Sogod District">Sogod District</option>
                    <option value="Libagon District">Libagon District</option>
                    <option value="Limasawa District">Limasawa District</option>
                    <option value="Macrohon District">Macrohon District</option>
                    <option value="Malitbog District">Malitbog District</option>
                    <option value="Padre Burgos District">Padre Burgos District</option>
                    <option value="Pintuyan District">Pintuyan District</option>
                    <option value="San Francisco District">San Francisco District</option>
                    <option value="San Juan District">San Juan District</option>
                    <option value="Anahawan District">Anahawan District</option>
                    <option value="Silago District">Silago District</option>
                    <option value="St. Bernard District">St. Bernard District</option>
                    <option value="Tomas Oppus District">Tomas Oppus District</option>
                    <option value="Other">Other</option>

                </select>
                <input type="text" id="district_other" name="district_other" class="other-input is-hidden" placeholder="Enter district/school/office" style="display: none;">
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
function toggleOtherInput(selectEl) {
    var otherInputId = selectEl.getAttribute('data-other-input');
    if (!otherInputId) {
        return;
    }

    var otherInput = document.getElementById(otherInputId);
    if (!otherInput) {
        return;
    }

    var isOther = selectEl.value === 'Other';
    otherInput.classList.toggle('is-hidden', !isOther);
    otherInput.style.display = isOther ? 'block' : 'none';
    otherInput.required = isOther;

    if (!isOther) {
        otherInput.value = '';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    var selects = document.querySelectorAll('select[data-other-input]');
    for (var i = 0; i < selects.length; i += 1) {
        toggleOtherInput(selects[i]);
    }
});
</script>
HTML;
echo $inline_script;
$scripts = ['js/main.js'];
require __DIR__ . '/includes/partials/document_end.php';
?>
