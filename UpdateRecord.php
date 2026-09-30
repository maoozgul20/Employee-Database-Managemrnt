<?php
/* =========================================================
   UpdateRecord.php — Update step 2: show the edit form
   Receives the ID from: UpdateRecord.html,
   or from the Edit buttons in SelectAll.php / SearchRecord.php
   Sends the edited data to: ConfirmUpdate.php
   ========================================================= */

require_once 'includes/db.php';
require_post('UpdateRecord.html');

// ---------- 1. Collect & validate input ----------
$empID   = trim($_POST['empID'] ?? '');
$idError = validate_id($empID);

if ($idError !== '') {
    render_result('error', 'Invalid Employee ID', $idError, [], [['UpdateRecord.html', 'Try Again', 'primary']]);
    exit;
}

// ---------- 2. Fetch the current record ----------
$stmt = $conn->prepare("SELECT empID, empName, Department, empSalary FROM Employee WHERE empID = ?");
$stmt->bind_param("i", $empID);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$row) {
    render_result(
        'info',
        'No employee found',
        'There is no employee with ID ' . $empID . '.',
        [],
        [['UpdateRecord.html', 'Try Again', 'primary'], ['SelectAll.php', 'View All', 'ghost']]
    );
    exit;
}

// ---------- 3. Show the pre-filled edit form ----------
page_header('Edit Employee', 'update', true);
?>

<section class="card animate-in">

  <div class="card-head">
    <div class="card-icon warning"><?= icon('edit', 22) ?></div>
    <div>
      <h1>Edit Employee</h1>
      <p>Step 2 of 2: change the details and save.</p>
    </div>
  </div>

  <form class="card-body" action="ConfirmUpdate.php" method="POST" novalidate>

    <div class="form-group">
      <label for="empID">Employee ID</label>
      <input type="text" id="empID" name="empID" class="form-control" value="<?= e($row['empID']) ?>"
             readonly aria-describedby="hint-empID">
      <small class="hint" id="hint-empID">The ID cannot be changed.</small>
    </div>

    <div class="form-group">
      <label for="empName">Full Name <span class="req">*</span></label>
      <input type="text" id="empName" name="empName" class="form-control" value="<?= e($row['empName']) ?>"
             maxlength="50" autocomplete="off" aria-describedby="hint-empName err-empName" autofocus>
      <small class="hint" id="hint-empName">Letters, spaces, dots or apostrophes (2 to 50 characters).</small>
      <small class="error" id="err-empName" aria-live="polite"></small>
    </div>

    <div class="form-group">
      <label for="Department">Department <span class="req">*</span></label>
      <input type="text" id="Department" name="Department" class="form-control" value="<?= e($row['Department']) ?>"
             maxlength="50" autocomplete="off" aria-describedby="hint-Department err-Department">
      <small class="hint" id="hint-Department">For example: IT, HR, Finance, R&amp;D.</small>
      <small class="error" id="err-Department" aria-live="polite"></small>
    </div>

    <div class="form-group">
      <label for="empSalary">Monthly Salary <span class="req">*</span></label>
      <div class="input-wrap has-prefix">
        <span class="input-prefix">Rs</span>
        <input type="text" id="empSalary" name="empSalary" class="form-control" value="<?= e($row['empSalary']) ?>"
               inputmode="decimal" autocomplete="off" aria-describedby="hint-empSalary err-empSalary">
      </div>
      <small class="hint" id="hint-empSalary">Up to 2 decimal places.</small>
      <small class="error" id="err-empSalary" aria-live="polite"></small>
    </div>

    <div class="form-actions">
      <button type="reset" class="btn btn-ghost">Undo Changes</button>
      <button type="submit" class="btn btn-primary btn-block">Update Employee</button>
    </div>

  </form>
</section>

<p class="card-links"><a href="UpdateRecord.html">&larr; Choose a different employee</a></p>

<?php page_footer(); ?>
