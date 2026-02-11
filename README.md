# DepEd Southern Leyte Division Library Log System

A simple and efficient library visitor logging system built with native PHP for DepEd Southern Leyte Division.

## Features

### Public Side
- Easy visit logging with a single submission
- Auto date display and recorded time-in
- Clean, responsive UI
- Required fields for consistent data

### Admin Side
- Complete log management table
- Filtering by date, name, or client type
- Export to CSV
- Delete entries
- Secure login

## System Requirements

- XAMPP (Apache + MySQL + PHP 7.4 or higher)
- Web browser (Chrome, Firefox, Edge, Safari)

## Installation

### Step 1: Install XAMPP
1. Download XAMPP from https://www.apachefriends.org/
2. Install XAMPP on your computer
3. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Setup Database
1. Open your browser and go to `http://localhost/phpmyadmin`
2. Click the "Import" tab
3. Click "Choose File" and select `database.sql`
4. Click "Go"
5. You should see two tables created: `users` and `logbook_entries`

### Step 3: Copy System Files
1. Locate your XAMPP installation folder (usually `C:\xampp` on Windows)
2. Navigate to the `htdocs` folder
3. Create a new folder named `library-system`
4. Copy all project files into this folder

### Step 4: Access the System
1. Open your web browser
2. Go to `http://localhost/library-system`
3. You should see the Library Log form

## Default Login Credentials

Admin Access:
- Username: `admin`
- Password: `admin123`

Important: Change the default password immediately after first login for security.

## How to Use

### For Library Visitors

1. Access the system: `http://localhost/library-system`
2. Fill in the form
3. Click "SUBMIT LOG" to record your visit
4. The current date and time are recorded automatically

### For Administrators

1. Login: `http://localhost/library-system/admin/login.php`
2. Enter credentials: `admin` / `admin123`
3. View the logbook table
4. Filter logs by date, name, or client type
5. Export data to CSV
6. Delete incorrect entries
7. Logout when finished

## Folder Structure
```
htdocs/library-system/
|-- index.php
|-- database.sql
|-- README.md
|-- INSTALLATION.txt
|-- admin/
|   |-- login.php
|   |-- dashboard.php
|   |-- logout.php
|   `-- export.php
|-- config/
|   |-- database.php
|   `-- session.php
|-- css/
|   |-- style.css
|   `-- admin.css
|-- includes/
|   |-- bootstrap.php
|   |-- helpers.php
|   `-- partials/
|       |-- document_start.php
|       |-- document_end.php
|       `-- flash.php
`-- js/
    `-- main.js
```

## Database Structure

### users table
- `id`: Unique identifier
- `username`: Admin username
- `password`: Encrypted password
- `role`: User role (admin)
- `created_at`: Account creation timestamp

### logbook_entries table
- `id`: Unique identifier
- `date`: Date of visit
- `time_in`: Time of visit
- `name`: Visitor's full name
- `client_type`: Type of visitor (free text)
- `position`: Position or grade level
- `district`: District name
- `purpose`: Purpose of visit
- `created_at`: Record creation timestamp

Note: The system records a single visit entry; there is no time-out field.

## Troubleshooting

### Cannot access the system
- Make sure Apache and MySQL are running in XAMPP Control Panel
- Check if you're using the correct URL: `http://localhost/library-system`

### Database connection error
- Verify MySQL is running
- Check `config/database.php` for correct credentials
- Default XAMPP MySQL password is empty
- Make sure database name is `library_logs`

### Cannot login as admin
- Make sure you imported `database.sql`
- Check if the `users` table exists in `library_logs`
- Use default credentials: admin / admin123

### Form submission not working
- Check if the `logbook_entries` table exists
- Verify all required fields are filled in
- Check browser console for JavaScript errors

## Security Recommendations

1. Change the default password after installation
2. Restrict access to trusted networks
3. Regularly back up the database
4. Keep XAMPP and PHP updated

## Support

For issues or questions, contact your IT administrator or DepEd Southern Leyte Division ICT Coordinator.

## License

This system is developed for DepEd Southern Leyte Division Office use.

---

Developed for: Department of Education - Southern Leyte Division
Version: 2.1
Last Updated: February 2026
