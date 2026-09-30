<?php
/* =========================================================
   DeleteRecord.php — Delete one employee (DELETE)
   Receives the ID from: DeleteRecord.html,
   or from the Delete buttons in SelectAll.php / SearchRecord.php
   ========================================================= */

require_once 'includes/db.php';
require_post('DeleteRecord.html');

// ---------- 1. Collect & validate input ----------
$empID   = trim($_POST['empID'] ?? '');
$idError = validate_id($empID);

if ($idError !== '') {
    render_result('error', 'Invalid Employee ID', $idError, [], [['DeleteRecord.html', 'Try Again', 'primary']]);
    exit;
}

// ---------- 2. Get the record first, so we can show what was deleted ----------
$find = $conn->prepare("SELECT empID, empName, Department, empSalary FROM Employee WHERE empID = ?");
$find->bind_param("i", $empID);
$find->execute();
$row = $find->get_result()->fetch_assoc();
$find->close();

if (!$row) {
    $conn->close();
    render_result(
        'info',
        'No employee found',
        'There is no employee with ID ' . $empID . ', so nothing was deleted.',
        [],
        [['DeleteRecord.html', 'Try Again', 'primary'], ['SelectAll.php', 'View All', 'ghost']]
    );
    exit;
}

// ---------- 3. Delete using a prepared statement ----------
$stmt = $conn->prepare("DELETE FROM Employee WHERE empID = ?");
$stmt->bind_param("i", $empID);
$stmt->execute();
$stmt->close();
$conn->close();

render_result(
    'success',
    'Employee deleted',
    'The following record has been removed from the database.',
    [
        'Employee ID' => $row['empID'],
        'Name'        => $row['empName'],
        'Department'  => $row['Department'],
        'Salary'      => format_salary($row['empSalary']),
    ],
    [['SelectAll.php', 'View All', 'primary'], ['DeleteRecord.html', 'Delete Another', 'ghost']]
);
