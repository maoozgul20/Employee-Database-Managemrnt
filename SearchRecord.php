<?php
/* =========================================================
   SearchRecord.php — Display one employee (SELECT ... WHERE)
   Receives the ID from: SearchRecord.html
   ========================================================= */

require_once 'includes/db.php';
require_post('SearchRecord.html');

// ---------- 1. Collect & validate input ----------
$empID   = trim($_POST['empID'] ?? '');
$idError = validate_id($empID);

if ($idError !== '') {
    render_result('error', 'Invalid Employee ID', $idError, [], [['SearchRecord.html', 'Try Again', 'primary']]);
    exit;
}

// ---------- 2. Search using a prepared statement ----------
$stmt = $conn->prepare("SELECT empID, empName, Department, empSalary FROM Employee WHERE empID = ?");
$stmt->bind_param("i", $empID);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

// ---------- 3. Not found ----------
if (!$row) {
    render_result(
        'info',
        'No employee found',
        'There is no employee with ID ' . $empID . '.',
        [],
        [['SearchRecord.html', 'Search Again', 'primary'], ['SelectAll.php', 'View All', 'ghost']]
    );
    exit;
}

// ---------- 4. Found: show the record ----------
page_header('Search Result', 'search');
?>

<div class="page-header animate-in">
  <h1>Search Result</h1>
  <p>Showing the employee with ID <?= e($empID) ?>.</p>
</div>

<section class="card animate-in delay-1">
  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th>Employee ID</th>
          <th>Name</th>
          <th>Department</th>
          <th class="num">Salary</th>
          <th class="actions">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><span class="id-badge"><?= e($row['empID']) ?></span></td>
          <td><strong><?= e($row['empName']) ?></strong></td>
          <td><span class="dept-badge"><?= e($row['Department']) ?></span></td>
          <td class="num"><?= e(format_salary($row['empSalary'])) ?></td>
          <td class="actions">
            <form action="UpdateRecord.php" method="POST">
              <input type="hidden" name="empID" value="<?= e($row['empID']) ?>">
              <button type="submit" class="btn btn-sm btn-icon-edit"><?= icon('edit', 15) ?> Edit</button>
            </form>
            <form action="DeleteRecord.php" method="POST" data-confirm>
              <input type="hidden" name="empID" value="<?= e($row['empID']) ?>">
              <button type="submit" class="btn btn-sm btn-icon-delete"><?= icon('trash', 15) ?> Delete</button>
            </form>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</section>

<div class="result-actions animate-in delay-2">
  <a href="SearchRecord.html" class="btn btn-primary"><?= icon('search', 16) ?> Search Again</a>
  <a href="SelectAll.php" class="btn btn-ghost">View All</a>
</div>

<?php page_footer(); ?>
