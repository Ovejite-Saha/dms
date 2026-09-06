# Document Management System (DMS)

A complete Document Management System built with **PHP**, **MySQL/MariaDB**, and **Bootstrap 5 (offline)**.

Documents are organized by **Ministry → Division → District → Upazila → Project**, with cascading dropdowns, granular user access, and GIS file support.

---

## Features

### Roles
| Role | Capabilities |
|------|----------------|
| **Admin** | Full control — users, subadmins, hierarchy, documents (upload / edit / delete) |
| **Subadmin** | Upload, edit, delete documents; edit own profile |
| **User** | View & download documents based on assigned access level; edit own profile |

### Hierarchy
- **Ministry** → **Division** → **District** → **Upazila** → **Project**
- Pre-seeded Bangladesh administrative data (divisions, districts, upazilas)
- Pre-seeded list of government ministries
- Admin can add / rename / delete any hierarchy item (**Hierarchy** menu)

### Document upload (all fields required)
1. Select **Ministry**
2. Select **Division** (enabled after Ministry)
3. Select **District** (enabled after Division — loaded via AJAX)
4. Select **Upazila** (enabled after District — loaded via AJAX)
5. Select **Project**
6. Enter **Document Name**
7. Choose file and upload

Files are stored as: `ProjectName_DocumentName.ext`  
(duplicate names get a numeric suffix).

### User access levels (set by Admin)
1. **All documents** — every ministry / division / district / upazila / project  
2. **Selected Division(s)** — all documents under those divisions  
3. **Selected District(s)** — all documents under those districts  
4. **Selected Upazila(s)** — all documents under those upazilas  
5. **Selected Project(s)** — all documents under those projects  

### Search & filters
Admin, Subadmin, and User can filter documents by:
- Ministry  
- Division  
- District (cascades from Division)  
- Upazila (cascades from District)  
- Free-text search (document name, project, ministry, etc.)

### Supported file types
**Office / general:** PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, GIF, TXT, ZIP  

**GIS:** SHP, SHX, DBF, PRJ, GeoJSON, JSON, KML, KMZ, GPX, GPKG, TIF/TIFF (GeoTIFF), ASC, DEM, TAB, MIF, MID, GML, DXF  

Each type shows a matching SVG icon (including a dedicated GIS icon).

---

## Default Admin Login

```
Username: admin
Password: admin123
```

## Default Sub-Admin Login

```
Username: subadmin
Password: admin123
```

## Default User Login

```
Username: user-1
Password: user123
```

---

## Installation (XAMPP / phpMyAdmin)

1. **Copy the project** into your web root, e.g.  
   `C:\xampp\htdocs\dms2\`

2. **Create the database** in phpMyAdmin → SQL tab:  
   - Run `config/setup.sql`  
     (creates `dms_db`, tables, default admin, ministries, divisions, sample projects)  
   - Then run `config/seed_hierarchy.sql`  
     (loads full District & Upazila tree)

3. **Configure DB connection** (if needed) in `config/db.php`:
   ```php
   define('DB_HOST', '127.0.0.1');
   define('DB_NAME', 'dms_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

4. **Make uploads writable**  
   Ensure `assets/uploads/` is writable by the web server.

5. **Open the app**  
   `http://localhost/dms2/index.php`  
   Log in with the default admin credentials above.

---

## File Structure

```
dms2/
├── index.php                         # Hero page + AJAX login modal
├── README.md
│
├── actions/
│   ├── login_action.php              # Login (admin / subadmin / user)
│   ├── logout.php
│   ├── admin_user_action.php         # Users + access levels
│   ├── admin_class_action.php        # Hierarchy CRUD
│   ├── ajax_hierarchy.php            # Cascading Division→District→Upazila
│   ├── doc_upload_action.php         # Upload with hierarchy + GIS check
│   ├── doc_manage_action.php         # Edit / delete documents
│   └── profile_action.php            # Profile & password
│
├── admin/
│   ├── dashboard.php
│   ├── manage_subadmins.php
│   ├── manage_users.php              # Access type + multi-select
│   ├── manage_classes.php            # Hierarchy tabs
│   └── manage_documents.php          # List / filter / edit
│
├── subadmin/
│   ├── dashboard.php
│   ├── upload_document.php           # Cascading upload form
│   ├── manage_documents.php
│   └── profile.php
│
├── user/
│   ├── dashboard.php                 # Filtered by access level
│   └── profile.php
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── auth_check.php                # Session & require_role()
│   ├── file_helper.php               # Icons, labels, allowed extensions
│   └── doc_access.php                # SQL filter by user access
│
├── config/
│   ├── db.php                        # PDO + session_start()
│   ├── setup.sql                     # Schema + seed ministries/divisions/projects
│   ├── seed_hierarchy.sql            # Districts & Upazilas
│   └── dms_db.sql                    # Legacy dump (optional)
│
└── assets/
    |-- bmdata                        # Stored bmdata
    ├── css/                          # Bootstrap 5 + custom style
    ├── js/                           # Bootstrap bundle + main.js
    ├── icons/                        # SVG icons (incl. gis.svg)
    ├── img/
    └── uploads/                      # Stored documents
```

---

## Database overview

| Table | Purpose |
|-------|---------|
| `admins` | Admin accounts |
| `subadmins` | Subadmin accounts |
| `users` | Users with `access_type` + `access_ids` (JSON) |
| `ministries` | Ministry list |
| `divisions` | Divisions |
| `districts` | Districts (FK → division) |
| `upazilas` | Upazilas (FK → district) |
| `projects` | Project list |
| `documents` | Files linked to ministry, division, district, upazila, project |

---

## Session

- Session starts in `config/db.php`
- No custom idle timeout is configured (PHP default applies, often ~24 minutes)
- Logout clears the session

---

## Tech Stack

- PHP 7.4+ (PDO / MySQL)
- MySQL 5.7+ / MariaDB
- Bootstrap 5.3 (offline)
- Font Awesome 6.5 (CDN)
- Vanilla JavaScript (AJAX login & cascading filters)
- 

---

## Notes

- Old “Class” system has been replaced by the hierarchy and user access levels.
- After a fresh import of `setup.sql` + `seed_hierarchy.sql`, re-upload any documents that were created under the old schema.
- Default admin password hash corresponds to **admin123**.
