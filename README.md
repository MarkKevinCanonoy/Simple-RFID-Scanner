# RFID Student Registration & Scanning System
### XAMPP + PHP (Procedural) + MySQL

---

## File Structure

```
rfid_system/           ← Place this entire folder in htdocs/
│
├── db.php             ← Database connection (edit credentials here)
├── navbar.php         ← Shared navigation partial
├── style.css          ← Global dark + hot-pink stylesheet
│
├── index.php          ← Register new student
├── dashboard.php      ← View / Edit / Delete all students
├── scan.php           ← RFID scan interface
│
└── setup.sql          ← Run this once to create DB + table
```

---

## Setup Steps

### 1. Start XAMPP
- Open XAMPP Control Panel
- Start **Apache** and **MySQL**

### 2. Create the Database
1. Open your browser → `http://localhost/phpmyadmin`
2. Click **"New"** or the **SQL** tab
3. Paste and run the contents of `setup.sql`

Or run it directly in the phpMyAdmin SQL console:

```sql
CREATE DATABASE IF NOT EXISTS rfid_system;
USE rfid_system;

CREATE TABLE IF NOT EXISTS users (
    id          INT(11)      NOT NULL AUTO_INCREMENT,
    full_name   VARCHAR(100) NOT NULL,
    student_id  VARCHAR(50)  NOT NULL,
    rfid_number VARCHAR(100) NOT NULL UNIQUE,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3. Copy Files to htdocs

Place the entire `rfid_system/` folder here:

```
C:\xampp\htdocs\rfid_system\
```

### 4. Configure db.php (if needed)

Open `db.php` and verify these match your XAMPP setup:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');   // default XAMPP user
define('DB_PASS', '');       // default XAMPP password is empty
define('DB_NAME', 'rfid_system');
```

### 5. Open in Browser

```
http://localhost/rfid_system/
```

---

## Pages

| URL | Description |
|-----|-------------|
| `index.php` | Register a new student with name, ID, and RFID |
| `dashboard.php` | View all students, edit name, delete records |
| `scan.php` | RFID scan station — auto-submits on Enter keypress |

---

## RFID Scanner Notes

Physical USB RFID readers behave like keyboards:
1. They type the card's code into the focused input field
2. They automatically press **Enter** when done

The scan page:
- Keeps the input field **permanently focused** (ready at all times)
- Listens for the **Enter keypress** and auto-submits
- After each scan: **clears and re-focuses** the input immediately
- Shows student name + ID on success, or "User Not Found" on failure

---

## Security Notes (for production)

This system uses procedural PHP with `mysqli_real_escape_string()` for basic protection. For a production deployment, consider:
- Prepared statements (`mysqli_prepare`)
- Session-based authentication
- HTTPS
- Input length limits

For a school lab or internal XAMPP use, the current implementation is sufficient.
