<?php
require_once dirname(__DIR__) . "/src/views/layouts/app_header.php";

$courses  = mysqli_query($conn, "SELECT id, title, code FROM course_details ORDER BY title");
$sessions = mysqli_query($conn, "SELECT id, year, term FROM session_details ORDER BY year DESC");

$message = ""; $msg_type = "success";

// ─── Handle Submission ───
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance'])) {
    $course_id  = (int) $_POST['course_id'];
    $session_id = (int) $_POST['session_id'];
    $on_date    = mysqli_real_escape_string($conn, $_POST['on_date']);

    $fq = mysqli_query($conn, "SELECT id FROM faculty_details WHERE user_name='$faculty_user'");
    $faculty_id = mysqli_fetch_assoc($fq)['id'] ?? 0;

    mysqli_begin_transaction($conn);
    $ok = $dup = 0;
    try {
        foreach ($_POST['attendance'] as $sid => $status) {
            $sid    = (int) $sid;
            $status = mysqli_real_escape_string($conn, $status);
            $sql = "INSERT INTO attendance_details (faculty_id,course_id,session_id,student_id,on_date,status)
                    VALUES ('$faculty_id','$course_id','$session_id','$sid','$on_date','$status')";
            if (!mysqli_query($conn, $sql)) {
                if (strpos(mysqli_error($conn), 'Duplicate') !== false) $dup++;
                else throw new Exception(mysqli_error($conn));
            } else $ok++;
        }
        mysqli_commit($conn);
        if ($dup > 0 && $ok === 0) { $message = "Attendance already recorded for this date, course, and session."; $msg_type = "warning"; }
        else { $message = "Attendance saved for $ok students successfully!" . ($dup > 0 ? " ($dup already existed)" : ""); $msg_type = "success"; }
    } catch (Exception $e) { mysqli_rollback($conn); $message = $e->getMessage(); $msg_type = "error"; }
}

$students = mysqli_query($conn, "SELECT id, roll_no, name FROM student_details ORDER BY roll_no ASC");
$all_students = [];
if ($students) while ($r = mysqli_fetch_assoc($students)) $all_students[] = $r;
?>

<?php
page_header(['Home', 'Attendance'], 'Take Attendance', 'Select a course, session, and date to record attendance.');
if ($message) echo alert_box($message, $msg_type);

$chip_defs = [
    'Present' => ['✅ Present', 'bg-present-soft text-present border-present/25 data-[selected=true]:ring-present'],
    'Absent'  => ['❌ Absent',  'bg-absent-soft text-absent border-absent/25 data-[selected=true]:ring-absent'],
    'Late'    => ['⏰ Late',    'bg-late-soft text-late border-late/25 data-[selected=true]:ring-late'],
    'Leave'   => ['📝 Leave',   'bg-leave-soft text-leave border-leave/25 data-[selected=true]:ring-leave'],
];
?>

<form method="POST" action="">
  <!-- Session details -->
  <div class="<?= ui('card') ?> mb-5">
    <div class="<?= ui('card_header') ?>"><span class="<?= ui('card_title') ?>">📋 Session Details</span></div>
    <div class="<?= ui('card_body') ?>">
      <div class="<?= ui('form_grid') ?>">
        <div class="<?= ui('field') ?>">
          <label class="<?= ui('label') ?>">Course</label>
          <select name="course_id" class="<?= ui('select') ?>" required>
            <option value="">— Select Course —</option>
            <?php while ($c = mysqli_fetch_assoc($courses)): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['code'].' — '.$c['title']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="<?= ui('field') ?>">
          <label class="<?= ui('label') ?>">Session / Term</label>
          <select name="session_id" class="<?= ui('select') ?>" required>
            <option value="">— Select Session —</option>
            <?php while ($s = mysqli_fetch_assoc($sessions)): ?>
            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['year'].' — '.$s['term']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="<?= ui('field') ?>">
          <label class="<?= ui('label') ?>">Date</label>
          <input type="date" name="on_date" class="<?= ui('input') ?>" value="<?= date('Y-m-d') ?>" required>
        </div>
      </div>
    </div>
  </div>

  <!-- Student table -->
  <div class="<?= ui('card') ?>">
    <div class="<?= ui('toolbar') ?>">
      <?= search_box('student-search', 'Filter students…', 'filterStudents(this.value)') ?>
      <button type="button" class="<?= ui('btn', 'grad_green', 'sm') ?>" onclick="markAll('Present')">All Present</button>
      <button type="button" class="<?= ui('btn', 'grad_red', 'sm') ?>" onclick="markAll('Absent')">All Absent</button>
      <span class="ml-auto text-[.8rem] text-muted"><?= count($all_students) ?> students</span>
    </div>
    <div class="overflow-x-auto">
    <table class="<?= ui('table') ?>">
      <thead>
        <tr>
          <th class="<?= ui('th') ?> w-[42px]">#</th>
          <th class="<?= ui('th') ?>">Student</th>
          <th class="<?= ui('th') ?>">Roll No</th>
          <th class="<?= ui('th') ?> min-w-[300px]">Attendance Status</th>
        </tr>
      </thead>
      <tbody id="student-tbody">
        <?php if (empty($all_students)): ?>
        <tr><td colspan="4" class="<?= ui('empty') ?>">No students found. <a href="manage_students.php" class="text-primary">Add students →</a></td></tr>
        <?php else: foreach ($all_students as $i => $row): ?>
        <tr class="student-row group" data-name="<?= htmlspecialchars(strtolower($row['name'])) ?>" data-roll="<?= htmlspecialchars(strtolower($row['roll_no'])) ?>">
          <td class="<?= ui('td') ?> text-[.8rem] text-muted"><?= $i + 1 ?></td>
          <td class="<?= ui('td') ?>">
            <div class="flex items-center gap-2.5">
              <?= avatar(strtoupper(substr($row['name'], 0, 1)), 'h-[34px] w-[34px] text-[.8rem]', avatar_gradient($i)) ?>
              <div class="text-sm font-semibold"><?= htmlspecialchars($row['name']) ?></div>
            </div>
          </td>
          <td class="<?= ui('td') ?> text-[.8rem] font-semibold text-muted"><?= htmlspecialchars($row['roll_no']) ?></td>
          <td class="<?= ui('td') ?>">
            <div class="status-chips flex flex-wrap gap-1.5" data-id="<?= $row['id'] ?>">
              <?php foreach ($chip_defs as $status => [$label, $tone]): ?>
              <span data-status="<?= $status ?>" onclick="selectStatus(this,'<?= $status ?>',<?= $row['id'] ?>)"
                    class="<?= CHIP_BASE ?> cursor-pointer data-[selected=true]:scale-105 data-[selected=true]:ring-2 <?= $tone ?>"><?= $label ?></span>
              <?php endforeach; ?>
            </div>
            <input type="hidden" name="attendance[<?= $row['id'] ?>]" id="att-<?= $row['id'] ?>" value="">
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
    </div>
    <?php if (!empty($all_students)): ?>
    <div class="flex justify-end border-t border-line px-4 py-3.5">
      <button type="submit" class="<?= ui('btn', 'grad') ?>">Save Attendance Records</button>
    </div>
    <?php endif; ?>
  </div>
</form>

<script>
function setChip(row, status) {
  row.querySelectorAll('[data-status]').forEach(c => { c.dataset.selected = (c.dataset.status === status) ? 'true' : 'false'; });
}

function selectStatus(chip, status, id) {
  setChip(chip.closest('.status-chips'), status);
  document.getElementById('att-' + id).value = status;
}

function markAll(status) {
  document.querySelectorAll('.status-chips').forEach(row => {
    setChip(row, status);
    document.getElementById('att-' + row.dataset.id).value = status;
  });
  showToast('Bulk Action', 'All students marked as ' + status, status === 'Present' ? 'success' : 'warning');
}

function filterStudents(query) {
  const q = query.toLowerCase();
  document.querySelectorAll('.student-row').forEach(tr => {
    tr.style.display = (tr.dataset.name.includes(q) || tr.dataset.roll.includes(q)) ? '' : 'none';
  });
}
</script>

<?php require_once dirname(__DIR__) . "/src/views/layouts/app_footer.php"; ?>
