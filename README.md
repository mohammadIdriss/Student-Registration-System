# Campus Registry — Student Registration System

A responsive student records website for BWP501. It uses HTML5, CSS, JavaScript, Bootstrap 5, PHP, PDO, and MySQL.

## Features

- Account registration with password confirmation and secure password hashing
- Login with username or email, protected sessions, and logout
- Add, view, update, and delete student records
- Optional JPG, PNG, GIF, or WebP profile picture upload (maximum 2 MB)
- AJAX validation on the Add Student form
- Search by student name, email, or phone number
- Sortable columns and pagination with 10 students per page
- Delete confirmation and success/error messages
- Responsive Bootstrap layout for desktop and mobile

## 1. Quick start for the team (Windows + XAMPP)

1. Install XAMPP in `C:\xampp` (or set the `XAMPP_HOME` environment variable to its folder).
2. Keep all project files together and double-click **`start.bat`**.
3. The launcher starts MySQL, imports `database.sql`, starts the website, and opens `http://127.0.0.1:7100/`.
4. Register a new account on first use. The website runs in the minimized **Campus Registry Server** window; close that window to stop it.

The launcher uses XAMPP's default MySQL account (`root` with no password). If you changed that account, update the login in `start.bat` and `config/database.php`. The project can run from any folder; copying it into `htdocs` is not required.

## 2. Manual XAMPP setup (Apache)

1. Copy the project folder into XAMPP's `htdocs` directory and name it `campus-registry`.
2. Open the XAMPP Control Panel and start **Apache** and **MySQL**.
3. Visit `http://localhost/phpmyadmin`.
4. Open **Import**, choose `database.sql`, and press **Import**. This creates the `student_registration_system` database and both required tables.
5. The default configuration uses MySQL user `root` with no password, which matches a normal local XAMPP installation. If your credentials differ, edit `config/database.php`.
6. Visit `http://localhost/campus-registry/`.

You can also import the database from a terminal:

```bash
mysql -u root -p < database.sql
```

## 3. Run with PHP's development server

Make sure MySQL is running and `database.sql` has been imported. PHP 8.0 or later is required, with `pdo_mysql`, `fileinfo`, and `mbstring` enabled.

```bash
php -S 127.0.0.1:7100
```

For environments that use npm scripts, the included launcher finds XAMPP's PHP installation automatically:

```bash
npm run dev -- --host 127.0.0.1 --port 7100
```

The database connection can also be configured with `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASS` environment variables.

## 4. Use the website

1. Open **Register** and create an account. Passwords need at least 8 characters, including a letter and a number.
2. Log in using either your username or email address.
3. Choose **Add student**, complete the required fields, and optionally select a profile picture.
4. Open **Students** to browse the directory. Use the search field or click a sortable column heading.
5. Use the pencil button to update a student or the trash button to delete one. Deletion asks for confirmation first.
6. Use the account menu and **Log out** when finished.

## How the main features work

### Authentication and security

`register.php` validates new account details and stores only `password_hash()` output. `login.php` checks credentials with `password_verify()`, starts an authenticated PHP session, and regenerates the session ID. Student pages call `require_login()` so visitors must authenticate first. Logout destroys the session and its cookie.

All SQL values are passed through PDO prepared statements. Dynamic sort columns and directions are selected from fixed allowlists. Output is HTML-escaped, and record-changing forms use CSRF tokens.

### CRUD and image uploads

`add_student.php` inserts records, `view_students.php` reads them, `update_student.php` edits them, and `delete_student.php` removes them. Uploaded files are checked for upload errors, size, real MIME type, and valid image data. PHP generates a random filename and `move_uploaded_file()` stores it under `uploads/students/`; only its relative path is saved in MySQL. Replacing or deleting a record also removes its old image.

### AJAX validation

`assets/js/validation.js` sends Add Student field values to `validate_student.php` using `fetch()` when a field changes or loses focus. The endpoint returns JSON errors, which appear beside the relevant inputs without reloading the page. A valid form is then submitted by AJAX to `add_student.php`. Server-side validation always runs again, so browser validation cannot be bypassed.

### Search, sorting, and pagination

The directory uses prepared SQL `LIKE` conditions across name, email, and phone. Sorting is restricted to known columns. PHP calculates the total number of pages from `COUNT(*)`, then passes `LIMIT 10` and the correct `OFFSET` to fetch only the current page. Previous, next, and numbered page links preserve search and sorting choices.

## Project structure

```text
assets/css/style.css          Shared responsive styling
assets/js/main.js             Alerts, password visibility, delete confirmation
assets/js/validation.js       AJAX student validation and submission
config/database.php           PDO connection settings
includes/                     Sessions, shared helpers, header, and footer
uploads/students/             Uploaded profile pictures
index.html                    Homepage
login.html / login.php        Login form and authentication handler
register.html / register.php  Registration form and account handler
add_student.php               Add form, validation, upload, and insert
view_students.php             Search, sorting, pagination, and directory
update_student.php            Edit form and update handler
delete_student.php            Confirmed POST deletion handler
validate_student.php          JSON validation endpoint
database.sql                  Database and table definitions
```
