# DepEd Southern Leyte Division Library Log System

A compact, modern library visitor logging system built with native PHP + MySQL.

## Features

### Public Side
- Fast visitor log form (single submission)
- Dynamic `District -> School` dependent dropdown
- Dynamic personnel list by selected client category
- Built-in purpose quick-select + custom purpose
- Auto-recorded date and time-in

### Admin Side
- Logbook dashboard with filters + pagination
- CSV export for filtered records
- Master Data management for:
  - Districts (add, edit, activate/deactivate)
  - Schools (add, edit, reassign district, activate/deactivate)
  - Client Types (add, edit, activate/deactivate)
  - Personnel (add, edit, activate/deactivate)
- Compact SaaS-style UI for high-density data editing

## Requirements

- XAMPP (Apache + MySQL + PHP 7.4+)
- Browser (Chrome, Edge, Firefox, Safari)

## Installation

### 1. Prepare XAMPP
1. Install XAMPP from https://www.apachefriends.org/
2. Start `Apache` and `MySQL`

### 2. Put Project in `htdocs`
1. Copy project folder to `C:\xampp\htdocs\`
2. Example folder name: `library_logs`

### 3. Import Database
1. Open `http://localhost/phpmyadmin`
2. Click `Import`
3. Select `database.sql`
4. Click `Go`

This creates database `library_logs` with these tables:
- `users`
- `districts`
- `schools`
- `client_types`
- `personnel`
- `logbook_entries`

### 4. Access the App
If your folder is `library_logs`:
- Public: `http://localhost/library_logs`
- Admin Login: `http://localhost/library_logs/admin/login.php`
- Master Data: `http://localhost/library_logs/admin/master_data.php`

## Default Admin Account

- Username: `admin`
- Password: `admin123`

Change this password after first login.

## How to Use

### Visitor Flow
1. Open public page
2. Select client category and name (or `Other`)
3. Fill position, organization category, purpose
4. Submit log

### Admin Flow
1. Login via `/admin/login.php`
2. Review entries in dashboard (`/admin/dashboard.php`)
3. Manage master data in `/admin/master_data.php`
4. Export CSV as needed

## Project Structure

```text
library_logs/
|-- index.php
|-- database.sql
|-- README.md
|-- INSTALLATION.txt
|-- admin/
|   |-- login.php
|   |-- dashboard.php
|   |-- export.php
|   |-- master_data.php
|   `-- logout.php
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
|-- js/
|   `-- main.js
`-- scripts/
    `-- generate_school_seed.php
```

## Notes

- `logbook_entries` stores `time_in` only (no timeout field).
- Form dropdown values come from active master data records.
- District/School validation is enforced server-side on submit.

## Troubleshooting

### Cannot open app
- Confirm Apache is running
- Confirm folder path inside `htdocs`
- Confirm URL matches your actual folder name

### DB connection error
- Confirm MySQL is running
- Check `config/database.php` credentials
- Confirm DB name is `library_logs`

### Admin login fails
- Ensure `database.sql` was imported
- Use `admin / admin123` first

## Version

- Version: 2.3
- Updated: February 2026