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
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="client_type">Client Type <span class="required">*</span></label>
                <input type="text" id="client_type" name="client_type" required placeholder="e.g., Field, OSDS, Visitor, etc.">
            </div>
            
            <div class="form-group">
                <label for="position">Position/Designation <span class="required">*</span></label>
                <input type="text" id="position" name="position" required placeholder="e.g., Principal, Head Teacher, Utility, ADAS, etc.">
            </div>
            
            <div class="form-group">
                <label for="district">District/School/Office <span class="required">*</span></label>
                <input type="text" id="district" name="district" required placeholder="e.g., Malibog, Division Office, Bontoc II-District, etc.">
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
$scripts = ['js/main.js'];
require __DIR__ . '/includes/partials/document_end.php';
?>
