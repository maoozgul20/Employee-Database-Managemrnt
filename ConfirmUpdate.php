<?php
/* =========================================================
   ConfirmUpdate.php — Update step 3: save changes (UPDATE)
   Receives data from: UpdateRecord.php
   ========================================================= */

require_once 'includes/db.php';
require_post('UpdateRecord.html');

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

// ---------- 3. Check the record still exists ----------
$check = $conn->prepare("SELECT empID FROM Employee WHERE empID = ?");
$check->bind_param("i", $empID);
$check->execute();
$check->store_result();
$exists = $check->num_rows > 0;
$check->close();

if (!$exists) {
    $conn->close();
    render_result(
        'info',
        'No employee found',
        'There is no employee with ID ' . $empID . '. It may have been deleted.',
        [],
        [['UpdateRecord.html', 'Try Again', 'primary'], ['SelectAll.php', 'View All', 'ghost']]
    );
    exit;
}

// ---------- 4. Update using a prepared statement ----------
$stmt = $conn->prepare("UPDATE Employee SET empName = ?, Department = ?, empSalary = ? WHERE empID = ?");
$stmt->bind_param("ssdi", $empName, $Department, $empSalary, $empID);
$stmt->execute();
$changed = $stmt->affected_rows > 0;
$stmt->close();
$conn->close();

$details = [
    'Employee ID' => $empID,
    'Name'        => $empName,
    'Department'  => $Department,
    'Salary'      => format_salary($empSalary),
];
$actions = [['SelectAll.php', 'View All', 'primary'], ['UpdateRecord.html', 'Update Another', 'ghost']];

if ($changed) {
    render_result('success', 'Employee updated successfully', 'The record now has these details.', $details, $actions);
} else {
    render_result('info', 'No changes made', 'The new values were the same as the old ones.', $details, $actions);
}
