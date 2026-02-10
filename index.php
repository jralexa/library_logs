<?php
// Load database connection and session helpers.
require_once 'config/database.php';
require_once 'config/session.php';

// Message state for form feedback.
$message = '';
$messageType = '';

// Helper to read sanitized POST values.
function post_value($key) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

// Handle form submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read form inputs.
    $name = post_value('name');
    $client_type = post_value('client_type');
    $position = post_value('position');
    $district = post_value('district');
    $purpose = post_value('purpose');

    // Get current date and time (uses server timezone).
    $log_date = date('Y-m-d');
    $current_time = date('H:i:s');

    // Validate required fields before insert.
    if ($name && $client_type && $position && $district && $purpose) {
        // Insert new log entry.
        $stmt = $conn->prepare(
            "INSERT INTO logbook_entries (date, time_in, name, client_type, position, district, purpose)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssssss", $log_date, $current_time, $name, $client_type, $position, $district, $purpose);

        // Set success or error message after DB insert.
        if ($stmt->execute()) {
            $safe_name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
            $message = "Visit logged successfully for {$safe_name} at " . date('h:i A');
            $messageType = 'success';
        } else {
            $message = "Error recording visit. Please try again.";
            $messageType = 'error';
        }
        $stmt->close();
    } else {
        // Missing required fields.
        $message = "Please complete all required fields.";
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DepEd Southern Leyte Division Library</title>
    <style>
        /* Import a clean, modern font. */
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap');

        /* Design tokens for consistent theming. */
        :root {
            --bg: #f6f4ef;
            --ink: #1f2937;
            --muted: #6b7280;
            --accent: #2563eb;
            --accent-strong: #1d4ed8;
            --card: #ffffff;
            --line: #e5e7eb;
            --soft: #f1f5f9;
            --success: #16a34a;
            --error: #dc2626;
            --shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
        }

        /* Global reset. */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* Page background and typography defaults. */
        body {
            font-family: 'Manrope', system-ui, -apple-system, Segoe UI, sans-serif;
            background:
                radial-gradient(1200px 600px at 15% 10%, #e2e8f0 0%, transparent 60%),
                radial-gradient(900px 600px at 90% 0%, #dbeafe 0%, transparent 55%),
                var(--bg);
            min-height: 100vh;
            padding: 32px 18px 48px;
            color: var(--ink);
        }
        
        /* Main card container. */
        .container {
            max-width: 1080px;
            margin: 0 auto;
            background: var(--card);
            padding: 36px 36px 40px;
            border-radius: 18px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(15, 23, 42, 0.06);
        }
        
        /* Header/title block. */
        .header {
            text-align: center;
            margin-bottom: 26px;
        }
        
        .header h1 {
            color: var(--ink);
            font-size: 28px;
            margin-bottom: 8px;
            font-weight: 800;
            letter-spacing: -0.4px;
        }
        
        .header p {
            color: var(--muted);
            font-size: 14px;
        }
        
        /* Date/time panel. */
        .datetime-display {
            background: var(--soft);
            padding: 16px 18px;
            border-radius: 12px;
            margin-bottom: 22px;
            text-align: center;
            border: 1px solid var(--line);
        }
        
        .datetime-display .date {
            font-size: 16px;
            color: var(--ink);
            font-weight: 600;
        }
        
        .datetime-display .time {
            font-size: 24px;
            color: var(--accent);
            font-weight: 800;
            margin-top: 5px;
            letter-spacing: 0.3px;
        }
        
        /* Form layout. */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        /* Remove extra spacing from form groups. */
        .form-group {
            margin-bottom: 0;
        }
        
        /* Field labels. */
        label {
            display: block;
            margin-bottom: 8px;
            color: var(--ink);
            font-weight: 600;
            font-size: 14px;
        }
        
        /* Form input styling. */
        input[type="text"],
        select,
        textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--line);
            border-radius: 10px;
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            background: #ffffff;
            color: var(--ink);
        }
        
        /* Focus ring for accessibility. */
        input[type="text"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }
        
        /* Textarea sizing. */
        textarea {
            resize: vertical;
            min-height: 90px;
        }
        
        /* Base button styling. */
        button {
            width: 50%;
            display: block; 
            margin: 0 auto;
            padding: 14px 18px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
        }
        
        /* Primary submit button. */
        .btn-time-in {
            background: var(--accent);
            color: white;
        }
        
        /* Hover effect for submit button. */
        .btn-time-in:hover {
            background: var(--accent-strong);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
        }
        
        /* Feedback message container. */
        .message {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        /* Success message style. */
        .message.success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid rgba(22, 163, 74, 0.2);
        }
        
        /* Error message style. */
        .message.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid rgba(220, 38, 38, 0.25);
        }
        
        /* Admin link container. */
        .admin-link {
            text-align: center;
            margin-top: 20px;
        }
        
        /* Admin link styling. */
        .admin-link a {
            color: var(--accent);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        
        .admin-link a:hover {
            text-decoration: underline;
        }
        
        /* Required asterisk color. */
        .required {
            color: var(--error);
        }

        /* Informational note box (not used currently). */
        .info-note {
            background: #eff6ff;
            border-left: 4px solid var(--accent);
            padding: 12px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #1e3a8a;
        }

        /* Two-column layout on larger screens. */
        @media (min-width: 900px) {
            .form-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .form-group.full {
                grid-column: 1 / -1;
            }
        }

        /* Mobile spacing adjustments. */
        @media (max-width: 640px) {
            body {
                padding: 20px 14px 30px;
            }

            .container {
                padding: 26px 20px 28px;
            }

            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 DepEd Southern Leyte Division Library</h1>
            <p>Library Visitor Log System</p>
        </div>
        
        <!-- Live date/time display -->
        <div class="datetime-display">
            <div class="date" id="currentDate"></div>
            <div class="time" id="currentTime"></div>
        </div>

        <!-- Status message after form submission -->
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
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
    
    <script>
        // Update the date/time display in the Asia/Manila timezone.
        function updateDateTime() {
            const now = new Date();
            
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                timeZone: 'Asia/Manila'
            };
            const dateStr = now.toLocaleDateString('en-PH', options);
            
            const timeStr = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                timeZone: 'Asia/Manila'
            });
            
            document.getElementById('currentDate').textContent = dateStr;
            document.getElementById('currentTime').textContent = timeStr;
        }
        
        // Update time every second.
        updateDateTime();
        setInterval(updateDateTime, 1000);
        
        // Clear form after submission if successful.
        <?php if ($messageType === 'success'): ?>
        setTimeout(function() {
            document.getElementById('logForm').reset();
        }, 2000);
        <?php endif; ?>
    </script>
</body>
</html>
