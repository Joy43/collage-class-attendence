<?php
require_once dirname(__DIR__) . "/src/views/layouts/app_header.php";

$message = ""; $msg_type = "success";

// ─── Notification Helper ───
function saveNotification($conn, $faculty_user, $type, $title, $message_text) {
    $fu  = mysqli_real_escape_string($conn, $faculty_user);
    $tp  = mysqli_real_escape_string($conn, $type);
    $ti  = mysqli_real_escape_string($conn, $title);
    $msg = mysqli_real_escape_string($conn, $message_text);
    mysqli_query($conn, "INSERT INTO notifications (faculty_user, type, title, message) VALUES ('$fu','$tp','$ti','$msg')");
}

// ─── CRUD Actions ───
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_student'])) {
    $roll_no = mysqli_real_escape_string($conn, trim($_POST['roll_no']));
    $name    = mysqli_real_escape_string($conn, trim($_POST['name']));
    $sid     = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;

    if ($sid > 0) {
        $sql = "UPDATE student_details SET roll_no='$roll_no', name='$name' WHERE id='$sid'";
        if (mysqli_query($conn, $sql)) {
            $message = "Student updated successfully!";
            $msg_type = "success";
            saveNotification($conn, $faculty_user,
                'update',
                '✏️ Student Updated',
                "Profile of $name (Roll: $roll_no) was updated."
            );
        } else {
            $message = "Error: ".mysqli_error($conn);
            $msg_type = "error";
        }
    } else {
        $sql = "INSERT INTO student_details (roll_no, name) VALUES ('$roll_no','$name')";
        if (mysqli_query($conn, $sql)) {
            $message = "Student registered successfully!";
            $msg_type = "success";
            saveNotification($conn, $faculty_user,
                'create',
                '➕ New Student Registered',
                "$name (Roll: $roll_no) has been added to the system."
            );
        } else {
            $message = "Error: ".mysqli_error($conn);
            $msg_type = "error";
        }
    }
}

if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    // Fetch name before deletion for notification
    $dq  = mysqli_query($conn, "SELECT name, roll_no FROM student_details WHERE id='$did'");
    $del_student = $dq ? mysqli_fetch_assoc($dq) : ['name'=>'Unknown','roll_no'=>''];
    mysqli_begin_transaction($conn);
    try {
        mysqli_query($conn, "DELETE FROM attendance_details WHERE student_id='$did'");
        mysqli_query($conn, "DELETE FROM student_details WHERE id='$did'");
        mysqli_commit($conn);
        $message = "Student and attendance records deleted.";
        $msg_type = "success";
        saveNotification($conn, $faculty_user,
            'delete',
            '🗑️ Student Deleted',
            "{$del_student['name']} (Roll: {$del_student['roll_no']}) was removed from the system."
        );
    } catch(Exception $e) {
        mysqli_rollback($conn);
        $message = $e->getMessage();
        $msg_type = "error";
    }
}

$edit_student = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $eq  = mysqli_query($conn, "SELECT * FROM student_details WHERE id='$eid'");
    if ($eq && mysqli_num_rows($eq)) $edit_student = mysqli_fetch_assoc($eq);
}

// ─── Load Students with attendance summary ───
$students_q = mysqli_query($conn, "
    SELECT s.*,
      (SELECT COUNT(*) FROM attendance_details a WHERE a.student_id=s.id) AS total_att,
      (SELECT COUNT(*) FROM attendance_details a WHERE a.student_id=s.id AND a.status='Present') AS present_att
    FROM student_details s ORDER BY s.roll_no ASC
");
$students = []; if ($students_q) while ($r = mysqli_fetch_assoc($students_q)) $students[] = $r;
$total_students = count($students);
?>

<?php
page_header(['Home', 'Students'], 'Student Management', "$total_students students registered");
if ($message) echo alert_box($message, $msg_type);
?>

<div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-[1fr_340px]">

  <!-- ── Student Directory ── -->
  <div class="<?= ui('card') ?>">
    <div class="<?= ui('toolbar') ?>">
      <?= search_box('student-filter', 'Search students…', 'filterTable(this.value)') ?>
      <span class="ml-auto text-[.8rem] text-muted"><?= $total_students ?> total</span>
    </div>
    <div class="overflow-x-auto">
    <table class="<?= ui('table') ?>">
      <thead>
        <tr>
          <th class="<?= ui('th') ?> w-11"></th>
          <th class="<?= ui('th') ?>">Student</th>
          <th class="<?= ui('th') ?>">Roll No</th>
          <th class="<?= ui('th') ?>">Attendance</th>
          <th class="<?= ui('th') ?> w-[100px] !text-center">Actions</th>
        </tr>
      </thead>
      <tbody id="student-tbody">
        <?php if (empty($students)): ?>
        <tr><td colspan="5" class="<?= ui('empty') ?>">
          <div class="mb-3 text-[2.5rem]">👥</div>
          <div class="mb-1.5 text-base font-bold text-fg">No students yet</div>
          <p>Register your first student using the form.</p>
        </td></tr>
        <?php else: foreach ($students as $i => $s):
          $pct       = $s['total_att'] > 0 ? round(($s['present_att'] / $s['total_att']) * 100) : 0;
          $pct_text  = $pct >= 75 ? 'text-present' : ($pct >= 50 ? 'text-late' : 'text-absent');
          $pct_fill  = $pct >= 75 ? 'bg-present'   : ($pct >= 50 ? 'bg-late'   : 'bg-absent');
          $grad      = avatar_gradient($i);
          $profile_js = htmlspecialchars(json_encode($s)) . ", '" . $grad . "'";
        ?>
        <tr class="student-row group" data-name="<?= htmlspecialchars(strtolower($s['name'])) ?>" data-roll="<?= htmlspecialchars(strtolower($s['roll_no'])) ?>">
          <td class="<?= ui('td') ?>">
            <button type="button" onclick="viewProfile(<?= $profile_js ?>)" class="cursor-pointer border-0 bg-transparent p-0">
              <?= avatar(strtoupper(substr($s['name'], 0, 1)), 'h-9 w-9 text-[.8rem]', $grad) ?>
            </button>
          </td>
          <td class="<?= ui('td') ?>">
            <div class="text-sm font-semibold"><?= htmlspecialchars($s['name']) ?></div>
            <div class="text-xs text-muted">ID: <?= $s['id'] ?></div>
          </td>
          <td class="<?= ui('td') ?> text-[.85rem] font-semibold"><?= htmlspecialchars($s['roll_no']) ?></td>
          <td class="<?= ui('td') ?>">
            <div class="flex items-center gap-2">
              <div class="h-1 flex-grow overflow-hidden rounded-full bg-line">
                <div class="h-full rounded-full transition-[width] duration-500 <?= $pct_fill ?>" style="width:<?= $pct ?>%"></div>
              </div>
              <span class="min-w-9 text-xs font-bold <?= $pct_text ?>"><?= $pct ?>%</span>
            </div>
            <div class="mt-0.5 text-[.7rem] text-muted"><?= $s['total_att'] ?> sessions</div>
          </td>
          <td class="<?= ui('td') ?>">
            <div class="flex justify-center gap-1.5">
              <button type="button" class="<?= ui('icon_sm') ?>" title="View Profile" onclick="viewProfile(<?= $profile_js ?>)"><?= icon('eye') ?></button>
              <button type="button" class="<?= ui('icon_sm') ?>" title="Edit" onclick="editStudent(<?= $s['id'] ?>,'<?= htmlspecialchars(addslashes($s['roll_no'])) ?>','<?= htmlspecialchars(addslashes($s['name'])) ?>')"><?= icon('edit') ?></button>
              <a href="?delete=<?= $s['id'] ?>" class="<?= ui('icon_sm') ?> !text-absent" title="Delete" onclick="return confirm('Delete <?= htmlspecialchars($s['name']) ?>? This removes all their attendance records.')"><?= icon('trash') ?></a>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
    </div>
  </div>

  <!-- ── Add / Edit Form ── -->
  <div id="form-card" class="<?= ui('card') ?>">
    <div class="border-b border-line bg-gradient-to-br from-primary/5 to-leave/5 px-5 py-4">
      <div id="form-title" class="text-[.95rem] font-bold"><?= $edit_student ? '✏️ Edit Student' : 'Register Student' ?></div>
      <div id="form-subtitle" class="mt-px text-xs text-muted">
        <?= $edit_student ? 'Update the student record below' : 'Fill in the details to add a new student' ?>
      </div>
    </div>

    <form method="POST" action="manage_students.php" id="student-form" class="p-5">
      <input type="hidden" name="student_id" id="form-sid" value="<?= $edit_student ? $edit_student['id'] : '' ?>">

      <div class="mb-4">
        <label class="<?= ui('label') ?> mb-[7px] flex items-center gap-[5px]"><span class="opacity-70">🪪</span> Roll Number</label>
        <input type="text" name="roll_no" id="form-roll" class="<?= ui('input') ?>" placeholder="e.g. CSB21011" required
               value="<?= $edit_student ? htmlspecialchars($edit_student['roll_no']) : '' ?>">
      </div>

      <div class="mb-6">
        <label class="<?= ui('label') ?> mb-[7px] flex items-center gap-[5px]"><span class="opacity-70">👤</span> Full Name</label>
        <input type="text" name="name" id="form-name" class="<?= ui('input') ?>" placeholder="e.g. John Doe" required
               value="<?= $edit_student ? htmlspecialchars($edit_student['name']) : '' ?>">
      </div>

      <div class="flex items-center gap-2">
        <button type="submit" name="save_student" id="form-submit-btn" data-mode="<?= $edit_student ? 'edit' : 'new' ?>"
                class="<?= ui('btn', 'grad', 'full', 'md') ?> data-[mode=edit]:!from-violet-500 data-[mode=edit]:!to-violet-600">
          <span id="form-btn-label"><?= $edit_student ? 'Update Profile' : 'Register Student' ?></span>
        </button>
        <button type="button" onclick="resetForm()" class="<?= ui('btn', 'cancel', 'md') ?>">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- ══════════ Profile Modal ══════════ -->
<div id="profile-modal" data-modal class="<?= ui('modal') ?>">
  <div class="<?= ui('modal_box') ?> max-w-[480px]">
    <div class="<?= ui('modal_head') ?>">
      <span class="text-base font-bold">Student Profile</span>
      <button type="button" class="<?= ui('modal_close') ?>" onclick="closeModal('profile-modal')">✕</button>
    </div>
    <div class="p-[22px]">
      <div id="modal-profile-content" class="flex flex-col items-center gap-2 px-4 py-6 text-center"></div>
      <div id="modal-info-rows"></div>
    </div>
    <div class="<?= ui('card_footer') ?> flex justify-end gap-2">
      <button type="button" class="<?= ui('btn', 'secondary', 'sm') ?>" onclick="closeModal('profile-modal')">Close</button>
      <button type="button" class="<?= ui('btn', 'primary', 'sm') ?>" id="modal-edit-btn">Edit</button>
    </div>
  </div>
</div>

<script>
function filterTable(q) {
  q = q.toLowerCase();
  document.querySelectorAll('.student-row').forEach(tr => {
    tr.style.display = (tr.dataset.name.includes(q) || tr.dataset.roll.includes(q)) ? '' : 'none';
  });
}

function setFormMode(mode) {
  const edit = mode === 'edit';
  document.getElementById('form-title').textContent    = edit ? '✏️ Edit Student' : 'Register Student';
  document.getElementById('form-subtitle').textContent = edit ? 'Update the student record below' : 'Fill in the details to add a new student';
  document.getElementById('form-submit-btn').dataset.mode = mode;   // drives the purple/blue button via data-[mode=edit]:
  document.getElementById('form-btn-label').textContent = edit ? 'Update Profile' : 'Register Student';
}

function editStudent(id, roll, name) {
  document.getElementById('form-sid').value  = id;
  document.getElementById('form-roll').value = roll;
  document.getElementById('form-name').value = name;
  setFormMode('edit');
  document.getElementById('form-card').scrollIntoView({behavior: 'smooth'});
  document.getElementById('form-roll').focus();
}

function resetForm() {
  document.getElementById('form-sid').value  = '';
  document.getElementById('form-roll').value = '';
  document.getElementById('form-name').value = '';
  setFormMode('new');
}

function viewProfile(s, grad) {
  const pct = s.total_att > 0 ? Math.round((s.present_att / s.total_att) * 100) : 0;
  const txt  = pct >= 75 ? 'text-present' : (pct >= 50 ? 'text-late' : 'text-absent');
  const fill = pct >= 75 ? 'bg-present'   : (pct >= 50 ? 'bg-late'   : 'bg-absent');
  const row = (label, value, extra = '') =>
    `<div class="flex justify-between border-b border-line py-2.5 text-[.85rem] last:border-b-0"><span class="font-semibold text-muted">${label}</span><span class="${extra}">${value}</span></div>`;

  document.getElementById('modal-profile-content').innerHTML = `
    <div class="mb-1.5 flex h-[72px] w-[72px] items-center justify-center rounded-full bg-gradient-to-br ${grad} text-[1.6rem] font-extrabold text-white shadow-[0_4px_12px_rgba(37,99,235,.3)]">${s.name.charAt(0).toUpperCase()}</div>
    <div class="text-[1.1rem] font-bold">${s.name}</div>
    <div class="text-[.8rem] text-muted">Roll No: ${s.roll_no}</div>
    <div class="mt-3 w-full text-center">
      <div class="mb-1.5 text-xs font-bold ${txt}">Attendance: ${pct}%</div>
      <div class="h-2 overflow-hidden rounded-full bg-line"><div class="h-full rounded-full ${fill}" style="width:${pct}%"></div></div>
      <div class="mt-1.5 text-xs text-muted">${s.present_att} present of ${s.total_att} sessions</div>
    </div>`;
  document.getElementById('modal-info-rows').innerHTML =
      row('Student ID', '#' + s.id)
    + row('Roll Number', s.roll_no)
    + row('Full Name', s.name)
    + row('Total Sessions', s.total_att)
    + row('Present', s.present_att, 'font-bold text-present')
    + row('Absent/Late/Leave', parseInt(s.total_att) - parseInt(s.present_att), 'font-bold text-absent');
  document.getElementById('modal-edit-btn').onclick = () => { closeModal('profile-modal'); editStudent(s.id, s.roll_no, s.name); };
  openModal('profile-modal');
}
</script>

<?php require_once dirname(__DIR__) . "/src/views/layouts/app_footer.php"; ?>
