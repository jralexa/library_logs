<?php
require_once 'config/database.php';
require_once 'config/session.php';

$message = '';
$messageType = '';

function post_value($key) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = post_value('name');
    $client_type = post_value('client_type');
    $position = post_value('position');
    $district = post_value('district');
    $purpose = post_value('purpose');

    // Get current date and time
    $log_date = date('Y-m-d');
    $current_time = date('H:i:s');

    if ($name && $client_type && $position && $district && $purpose) {
        $stmt = $conn->prepare(
            "INSERT INTO logbook_entries (date, time_in, name, client_type, position, district, purpose)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssssss", $log_date, $current_time, $name, $client_type, $position, $district, $purpose);

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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 980px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
            font-size: 14px;
        }
        
        .datetime-display {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .datetime-display .date {
            font-size: 16px;
            color: #333;
            font-weight: 600;
        }
        
        .datetime-display .time {
            font-size: 24px;
            color: #667eea;
            font-weight: 700;
            margin-top: 5px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 0;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        input[type="text"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-time-in {
            background: #10b981;
            color: white;
        }
        
        .btn-time-in:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
        }
        
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .message.success {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #10b981;
        }
        
        .message.error {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #ef4444;
        }
        
        .admin-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .admin-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        
        .admin-link a:hover {
            text-decoration: underline;
        }
        
        .required {
            color: #ef4444;
        }

        .info-note {
            background: #e0e7ff;
            border-left: 4px solid #667eea;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #3730a3;
        }

        @media (min-width: 900px) {
            .form-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .form-group.full {
                grid-column: 1 / -1;
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
        
        <div class="datetime-display">
            <div class="date" id="currentDate"></div>
            <div class="time" id="currentTime"></div>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
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
                ✅ SUBMIT LOG
            </button>
        </form>
        
        <div class="admin-link">
            <a href="admin/login.php">🔐 Admin Login</a>
        </div>
    </div>
    
    <script>
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
        
        // Update time every second
        updateDateTime();
        setInterval(updateDateTime, 1000);
        
        // Clear form after submission if successful
        <?php if ($messageType === 'success'): ?>
        setTimeout(function() {
            document.getElementById('logForm').reset();
        }, 2000);
        <?php endif; ?>
    </script>
</body>
</html>
