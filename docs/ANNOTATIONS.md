# Code Annotations

This file explains what each project file does and highlights key blocks. It is meant to complement the in-file comments.

**File: index.php**
Purpose: Public visitor log form and form submission handler.
Key blocks:
- Bootstrap inclusion for DB/session/helpers.
- POST handler that validates organization category (`district_school`, `division_office`, `other`) and inserts into normalized `logbook_entries`.
- District/school lookup loading from reference tables.
- Flash message display and redirect to prevent resubmission.
- HTML form UI for public logging.

**File: admin/dashboard.php**
Purpose: Admin logbook list with filters, pagination, and delete actions.
Key blocks:
- Admin access guard.
- Delete action handling via query parameter.
- Filter parsing and query building (date, name, client type, district, school, other organization).
- Joined reads from `districts`/`schools` to render organization labels.
- Pagination calculation and data query.
- Table rendering and pagination links.

**File: admin/login.php**
Purpose: Admin authentication form and login handler.
Key blocks:
- Redirect if already logged in.
- POST handler that verifies user credentials.
- Session assignment on success.
- Simple login UI.

**File: admin/export.php**
Purpose: Export filtered logbook entries to CSV.
Key blocks:
- Admin access guard.
- Filter parsing and query building (same filters as dashboard).
- Joined CSV export with district, school, and other organization columns.
- CSV headers, BOM for Excel, and row output.

**File: admin/logout.php**
Purpose: Clear session and log out.
Key blocks:
- `session_destroy()` and redirect to login.

**File: config/database.php**
Purpose: Database connection settings and initialization.
Key blocks:
- Constants for DB credentials.
- `mysqli` connection and error handling.
- UTF-8 charset setup.

**File: config/session.php**
Purpose: Session initialization and access-control helpers.
Key blocks:
- Default timezone setup.
- `isLoggedIn`, `isAdmin`, `requireLogin`, `requireAdmin` helpers.

**File: includes/bootstrap.php**
Purpose: Single include to load shared configuration and helpers.
Key blocks:
- Requires database connection, session helpers, and general helpers.

**File: includes/helpers.php**
Purpose: Shared helper functions for sanitizing, flash messages, and redirects.
Key blocks:
- `h()` for HTML escaping.
- `post_value()` and `get_value()` to read inputs safely.
- Flash message set/consume helpers.
- Redirect helpers.
- Badge class normalizer for client types.
- District and school lookup loaders.
- Organization label formatter for admin display.

**File: includes/partials/document_start.php**
Purpose: Shared HTML document start for consistent layout.
Key blocks:
- Defaults for title, styles, and body class.
- `<head>` + stylesheet links + opening `<body>`.

**File: includes/partials/document_end.php**
Purpose: Shared HTML document end for consistent layout.
Key blocks:
- Script loading and closing tags.

**File: includes/partials/flash.php**
Purpose: Shared flash message renderer.
Key blocks:
- Consumes and renders a flash message if present.

**File: css/style.css**
Purpose: Public visitor form styling.
Key blocks:
- Design tokens (`:root`) and layout rules.
- Form styles, buttons, and responsive rules.

**File: css/admin.css**
Purpose: Admin dashboard styling.
Key blocks:
- Layout for navbar, filters, table, pagination.
- Badge styles and responsive adjustments.

**File: js/main.js**
Purpose: Small client-side enhancements for the public page.
Key blocks:
- Date display in Asia/Manila timezone.
- Flash message auto-hide.
- Dynamic visibility rules for organization category.
- District -> school dependent dropdown population.

**File: database.sql**
Purpose: Database schema and default admin user.
Key blocks:
- `library_logs` creation.
- `users` table with hashed password.
- `districts` and `schools` reference tables with foreign keys.
- Normalized `logbook_entries` with district/school foreign keys and free-text organization fallback.

**File: README.md**
Purpose: Project overview, installation, and usage guide.
Key blocks:
- Features and requirements.
- Installation steps.
- Usage and troubleshooting.

**File: INSTALLATION.txt**
Purpose: Quick installation checklist.
Key blocks:
- Short steps for XAMPP, database import, and access URLs.
