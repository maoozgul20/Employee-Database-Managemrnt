<?php
/* =========================================================
   insert.php — Add a new employee (INSERT)
   Receives data from: index.html
   ========================================================= */

require_once 'includes/db.php';
require_post('index.html');

// ---------- 1. Collect input ----------
$empID      = trim($_POST['empID'] ?? '');
$empName    = trim($_POST['empName'] ?? '');
$Department = trim($_POST['Department'] ?? '');
$empSalary  = trim($_POST['empSalary'] ?? '');

// ---------- 2. Server-side validation ----------
$errors = validate_employee($empID, $empName, $Department, $empSalary);

if (!empty($errors)) {
    render_result('error', 'Please fix the following', '', [], [['back', 'Go Back', 'primary']], $errors);
    exit;
}

// ---------- 3. Check the ID is not already used ----------
$check = $conn->prepare("SELECT empID FROM Employee WHERE empID = ?");
$check->bind_param("i", $empID);
$check->execute();
$check->store_result();
$exists = $check->num_rows > 0;
$check->close();

if ($exists) {
    render_result(
        'error',
        'Employee ID already exists',
        'ID ' . $empID . ' is already used by another employee. Please choose a different ID.',
        [],
        [['back', 'Go Back', 'primary'], ['SelectAll.php', 'View All', 'ghost']]
    );
    exit;
}

// ---------- 4. Insert using a prepared statement ----------
$stmt = $conn->prepare("INSERT INTO Employee (empID, empName, Department, empSalary) VALUES (?, ?, ?, ?)");
$stmt->bind_param("issd", $empID, $empName, $Department, $empSalary);
$stmt->execute();
$stmt->close();
$conn->close();

render_result(
    'success',
    'Employee added successfully',
    'The new record has been saved to the database.',
    [
        'Employee ID' => $empID,
        'Name'        => $empName,
        'Department'  => $Department,
        'Salary'      => format_salary($empSalary),
    ],
    [['index.html', 'Add Another', 'primary'], ['SelectAll.php', 'View All', 'ghost']]
);
