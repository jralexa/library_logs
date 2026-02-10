# DepEd Southern Leyte Division Library Log System

A simple and efficient library visitor logging system built with native PHP for DepEd Southern Leyte Division.

## Features

### Public Side
- **Easy Visit Logging**: Visitors can quickly log their library visit with a single submission
- **Auto Date/Time Display**: Current date and time are automatically displayed and recorded
- **Clean User Interface**: Modern, responsive design that works on all devices
- **Required Fields**: Captures essential information (Name, Client Type, District, Purpose)

### Admin Side
- **Dashboard Overview**: View statistics including total visits, unique visitors, and today's visits
- **Complete Log Management**: View all library visit logs in a detailed table
- **Advanced Filtering**: Filter logs by date, name, or client type
- **Export to CSV**: Download filtered logs for reporting and analysis
- **Delete Entries**: Remove incorrect or test entries
- **Secure Login**: Password-protected admin access

## System Requirements

- XAMPP (Apache + MySQL + PHP 7.4 or higher)
- Web Browser (Chrome, Firefox, Edge, Safari)

## Installation Instructions

### Step 1: Install XAMPP
1. Download XAMPP from https://www.apachefriends.org/
2. Install XAMPP on your computer
3. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Setup Database
1. Open your web browser and go to `http://localhost/phpmyadmin`
2. Click on "Import" tab
3. Click "Choose File" and select the `database.sql` file
4. Click "Go" to import the database
5. You should see two tables created: `users` and `logbook_entries`

### Step 3: Copy System Files
1. Locate your XAMPP installation folder (usually `C:\xampp` on Windows)
2. Navigate to the `htdocs` folder
3. Create a new folder named `library-system`
4. Copy all system files into this folder with the following structure:

```
htdocs/library-system/
├── index.php
├── database.sql
├── config/
│   ├── database.php
│   └── session.php
└── admin/
    ├── login.php
    ├── dashboard.php
    ├── logout.php
    └── export.php
```

### Step 4: Access the System
1. Open your web browser
2. Go to `http://localhost/library-system`
3. You should see the Library Log form

## Default Login Credentials

**Admin Access:**
- Username: `admin`
- Password: `admin123`

**⚠️ IMPORTANT**: Change the default password immediately after first login for security!

## How to Use

### For Library Visitors

1. **Access the System**: Go to `http://localhost/library-system`
2. **Fill in the Form**:
   - Your full name
   - Client type (free text)
   - Position or Grade Level (optional)
   - District
   - Purpose of visit
3. **Submit**: Click the "SUBMIT LOG" button to record your visit
   - The current date and time will be automatically recorded
   - This system records a single visit entry (no time-out tracking)

### For Administrators

1. **Login**: Click "Admin Login" at the bottom of the homepage
2. **Enter Credentials**: Use username `admin` and password `admin123`
3. **View Dashboard**: See statistics and all library logs
4. **Filter Logs**: Use the filter options to find specific entries
5. **Export Data**: Click "Export to CSV" to download logs for reports
6. **Delete Entries**: Click "Delete" button to remove incorrect entries
7. **Logout**: Click "Logout" when finished

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
- Check `config/database.php` file for correct credentials
- Default XAMPP MySQL password is empty
- Make sure database name is `liblogs_db`

### Cannot login as admin
- Make sure you imported the `database.sql` file
- Check if the `users` table exists in `liblogs_db` database
- Use default credentials: admin / admin123

### Form submission not working
- Check if the `logbook_entries` table exists
- Verify all required fields are filled in
- Check browser console for JavaScript errors

## Security Recommendations

1. **Change Default Password**: Update admin password after installation
2. **Restrict Access**: Only allow access from trusted networks
3. **Regular Backups**: Export database regularly using phpMyAdmin
4. **Update Regularly**: Keep XAMPP and PHP updated

## Support

For issues or questions, contact your IT administrator or DepEd Southern Leyte Division ICT Coordinator.

## License

This system is developed for DepEd Southern Leyte Division Office use.

---

**Developed for**: Department of Education - Southern Leyte Division  
**Version**: 2.0  
**Last Updated**: February 2026
