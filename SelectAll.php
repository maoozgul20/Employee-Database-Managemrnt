<?php
/* =========================================================
   SelectAll.php — Display all employees (SELECT *)
   Each row also has Edit and Delete buttons, which post the
   ID to UpdateRecord.php and DeleteRecord.php.
   ========================================================= */

require_once 'includes/db.php';

// ---------- 1. Fetch all records ----------
$result = $conn->query("SELECT empID, empName, Department, empSalary FROM Employee ORDER BY empID");
$employees = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();

// ---------- 2. Summary numbers for the stat cards ----------
$total       = count($employees);
$departments = count(array_unique(array_map('strtolower', array_column($employees, 'Department'))));
$payroll     = array_sum(array_column($employees, 'empSalary'));

// ---------- 3. Page output ----------
page_header('All Employees', 'all');
?>

<div class="page-header animate-in">
  <h1>Employee Records</h1>
  <p>All employees stored in the database.</p>
</div>

<div class="stats">
  <div class="card stat animate-in delay-1">
    <div class="card-icon"><?= icon('users', 22) ?></div>
    <div><div class="stat-label">Total Employees</div><div class="stat-value"><?= $total ?></div></div>
  </div>
  <div class="card stat animate-in delay-2">
    <div class="card-icon warning"><?= icon('layers', 22) ?></div>
    <div><div class="stat-label">Departments</div><div class="stat-value"><?= $departments ?></div></div>
  </div>
  <div class="card stat animate-in delay-3">
    <div class="card-icon"><?= icon('wallet', 22) ?></div>
    <div><div class="stat-label">Monthly Payroll</div><div class="stat-value"><?= e(format_salary($payroll)) ?></div></div>
  </div>
</div>

<section class="card animate-in delay-3">
  <div class="toolbar">
    <h2>Employees</h2>
    <?php if ($total > 0): ?>
      <div class="search-box">
        <?= icon('search', 16) ?>
        <input type="search" class="form-control" placeholder="Filter by ID, name or department…"
               data-table-filter="empTable" aria-label="Filter employees">
      </div>
    <?php endif; ?>
    <a href="index.html" class="btn btn-primary btn-sm"><?= icon('plus', 16) ?> Add Employee</a>
  </div>

  <?php if ($total === 0): ?>
    <div class="empty-state">
      <?= icon('users', 44) ?>
      <h3>No employees yet</h3>
      <p>Add your first employee to see it here.</p>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="table" id="empTable">
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
          <?php foreach ($employees as $row): ?>
            <tr data-row>
              <td><span class="id-badge"><?= e($row['empID']) ?></span></td>
              <td><strong><?= e($row['empName']) ?></strong></td>
              <td><span class="dept-badge"><?= e($row['Department']) ?></span></td>
              <td class="num"><?= e(format_salary($row['empSalary'])) ?></td>
              <td class="actions">
                <form action="UpdateRecord.php" method="POST">
                  <input type="hidden" name="empID" value="<?= e($row['empID']) ?>">
                  <button type="submit" class="btn btn-sm btn-icon-edit" aria-label="Edit employee <?= e($row['empID']) ?>"><?= icon('edit', 15) ?> Edit</button>
                </form>
                <form action="DeleteRecord.php" method="POST" data-confirm>
                  <input type="hidden" name="empID" value="<?= e($row['empID']) ?>">
                  <button type="submit" class="btn btn-sm btn-icon-delete" aria-label="Delete employee <?= e($row['empID']) ?>"><?= icon('trash', 15) ?> Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="empty-state" id="noMatch" hidden>
        <h3>No matching employees</h3>
        <p>Try a different search term.</p>
      </div>
    </div>
    <div class="table-footer">Showing <span id="rowCount"><?= $total ?></span> of <?= $total ?> employees</div>
  <?php endif; ?>
</section>

<?php page_footer(); ?>
