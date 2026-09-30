# EmployeeDB: PHP & MySQL CRUD

A Web Engineering II project. It covers the four operations from the Week 14 and Week 15 notes: **Insert, Select, Search, Delete and Update**. It runs on XAMPP.

## How to run

1. Copy the `EmployeeDB` folder to `C:\xampp\htdocs\`.
2. In the XAMPP Control Panel, start **Apache** and **MySQL**.
3. If the database doesn't exist yet, open phpMyAdmin, go to the **SQL** tab and run `database/employeedb.sql`.
4. Open `http://localhost/EmployeeDB/` in your browser.

## Folder structure

```
EmployeeDB/
├── index.html            Add Employee form           → insert.php
├── insert.php            INSERT a new record
├── SelectAll.php         SELECT all records (table + Edit/Delete buttons)
├── SearchRecord.html     Search form                 → SearchRecord.php
├── SearchRecord.php      SELECT ... WHERE empID = ?
├── DeleteRecord.html     Delete form                 → DeleteRecord.php
├── DeleteRecord.php      DELETE ... WHERE empID = ?
├── UpdateRecord.html     Update step 1: enter ID     → UpdateRecord.php
├── UpdateRecord.php      Update step 2: edit form    → ConfirmUpdate.php
├── ConfirmUpdate.php     Update step 3: UPDATE ... WHERE empID = ?
│
├── assets/
│   ├── css/style.css     All design (one file for every page)
│   └── js/validation.js  Form validation, delete popup, table filter
│
├── includes/
│   ├── db.php            Database connection (edit here only)
│   └── functions.php     Shared layout, validation and helpers
│
└── database/
    └── employeedb.sql    Creates the database and table
```

## Teacher's file → this project

| Teacher's demo (student DB) | This project (EmployeeDB) |
|---|---|
| Entry.html + Insert.php | index.html + insert.php |
| SelectAll.php | SelectAll.php |
| SearchRecord.html + SearchRecord.php | same names |
| DeleteRecord.html + DeleteRecord.php | same names |
| UpdateRecord.html + UpdateRecord.php + ConfirmUpdate.php | same names |
| Table `result` (stid, stname, stmarks) | Table `Employee` (empID, empName, Department, empSalary) |

## Improvements over the demo code

- **Security:** all queries use prepared statements (no SQL injection), and all output is escaped with `htmlspecialchars` (no XSS).
- **Validation on both sides:** JavaScript validates while you type, and PHP validates again on the server.
- **Clear messages:** "ID already exists", "No employee found", "No changes made" and friendly database error pages.
- **Reusable code:** the connection is written once in `db.php` instead of in every file, and the design is in one CSS file.
- **UI/UX:** consistent navigation, a confirmation popup before deleting, loading buttons, a live table filter, summary cards, smooth transitions and a mobile-friendly layout.
- **Accessibility:** labels for every input, error messages that screen readers announce, keyboard support, and reduced motion for users who ask for it.
