<?php
// ============================================================
// System Settings CRUD Page
// ============================================================
require_once dirname(__DIR__) . "/src/config/database.php";

session_start();
if (!isset($_SESSION['faculty'])) {
    header("Location: /login.php");
    exit();
}

$msg = "";
$msg_type = "";
$tab = $_GET['tab'] ?? 'faculty'; // Default tab

// ============================================================
// 1. Handle Deletions (GET)
// ============================================================
if (isset($_GET['del_faculty'])) {
    $id = (int)$_GET['del_faculty'];
    mysqli_query($conn, "DELETE FROM faculty_details WHERE id=$id");
    $msg = "Faculty deleted successfully.";
    $msg_type = "success";
    $tab = 'faculty';
} elseif (isset($_GET['del_session'])) {
    $id = (int)$_GET['del_session'];
    mysqli_query($conn, "DELETE FROM session_details WHERE id=$id");
    $msg = "Session deleted successfully.";
    $msg_type = "success";
    $tab = 'session';
} elseif (isset($_GET['del_course'])) {
    $id = (int)$_GET['del_course'];
    mysqli_query($conn, "DELETE FROM course_details WHERE id=$id");
    $msg = "Course deleted successfully.";
    $msg_type = "success";
    $tab = 'course';
}

// ============================================================
// 2. Handle Add/Update (POST)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Faculty ---
    if (isset($_POST['save_faculty'])) {
        $id = (int)($_POST['f_id'] ?? 0);
        $user_name = mysqli_real_escape_string($conn, trim($_POST['user_name']));
        $name = mysqli_real_escape_string($conn, trim($_POST['name']));
        $password = mysqli_real_escape_string($conn, trim($_POST['password']));
        $tab = 'faculty';

        if ($id > 0) {
            $sql = "UPDATE faculty_details SET user_name='$user_name', name='$name', password='$password' WHERE id=$id";
            if (mysqli_query($conn, $sql)) {
                $msg = "Faculty updated successfully.";
                $msg_type = "success";
            } else {
                $msg = "Error updating faculty: " . mysqli_error($conn);
                $msg_type = "error";
            }
        } else {
            $sql = "INSERT INTO faculty_details (user_name, name, password) VALUES ('$user_name', '$name', '$password')";
            if (mysqli_query($conn, $sql)) {
                $msg = "Faculty added successfully.";
                $msg_type = "success";
            } else {
                $msg = "Error adding faculty: " . mysqli_error($conn);
                $msg_type = "error";
            }
        }
    }
    
    // --- Session ---
    elseif (isset($_POST['save_session'])) {
        $id = (int)($_POST['s_id'] ?? 0);
        $year = (int)$_POST['year'];
        $term = mysqli_real_escape_string($conn, trim($_POST['term']));
        $tab = 'session';

        if ($id > 0) {
            $sql = "UPDATE session_details SET year=$year, term='$term' WHERE id=$id";
            if (mysqli_query($conn, $sql)) {
                $msg = "Session updated successfully.";
                $msg_type = "success";
            } else {
                $msg = "Error updating session: " . mysqli_error($conn);
                $msg_type = "error";
            }
        } else {
            $sql = "INSERT INTO session_details (year, term) VALUES ($year, '$term')";
            if (mysqli_query($conn, $sql)) {
                $msg = "Session added successfully.";
                $msg_type = "success";
            } else {
                $msg = "Error adding session: " . mysqli_error($conn);
                $msg_type = "error";
            }
        }
    }

    // --- Course ---
    elseif (isset($_POST['save_course'])) {
        $id = (int)($_POST['c_id'] ?? 0);
        $code = mysqli_real_escape_string($conn, trim($_POST['code']));
        $title = mysqli_real_escape_string($conn, trim($_POST['title']));
        $credit = (int)$_POST['credit'];
        $tab = 'course';

        if ($id > 0) {
            $sql = "UPDATE course_details SET code='$code', title='$title', credit=$credit WHERE id=$id";
            if (mysqli_query($conn, $sql)) {
                $msg = "Course updated successfully.";
                $msg_type = "success";
            } else {
                $msg = "Error updating course: " . mysqli_error($conn);
                $msg_type = "error";
            }
        } else {
            $sql = "INSERT INTO course_details (code, title, credit) VALUES ('$code', '$title', $credit)";
            if (mysqli_query($conn, $sql)) {
                $msg = "Course added successfully.";
                $msg_type = "success";
            } else {
                $msg = "Error adding course: " . mysqli_error($conn);
                $msg_type = "error";
            }
        }
    }
}

// ============================================================
// 3. Fetch Data
// ============================================================
$faculty_res = mysqli_query($conn, "SELECT * FROM faculty_details ORDER BY name ASC");
$session_res = mysqli_query($conn, "SELECT * FROM session_details ORDER BY year DESC, term ASC");
$course_res  = mysqli_query($conn, "SELECT * FROM course_details ORDER BY code ASC");

// Include UI Header
require_once dirname(__DIR__) . "/src/views/layouts/app_header.php";

$tabs = [
    'faculty' => '👤 Faculty Users',
    'session' => '📅 Academic Sessions',
    'course'  => '📚 Courses',
];

/** Edit + delete icon buttons for a table row. */
function row_actions(string $editJs, string $deleteHref, string $confirm): string {
    return '<div class="flex justify-center gap-1.5">'
        . '<button type="button" class="' . ui('icon_sm') . '" title="Edit" onclick="' . $editJs . '">' . icon('edit') . '</button>'
        . '<a href="' . $deleteHref . '" class="' . ui('icon_sm') . ' !text-absent" title="Delete" onclick="return confirm(\'' . $confirm . '\')">' . icon('trash') . '</a>'
        . '</div>';
}

/** Modal shell: header + body + footer with cancel/save. */
function settings_modal(string $id, string $action, string $titleId, string $title, string $saveName, string $btnId, string $btnLabel, string $fields): void { ?>
<div id="<?= $id ?>" data-modal class="<?= ui('modal') ?>">
  <div class="<?= ui('modal_box') ?> max-w-[400px]">
    <form method="POST" action="<?= $action ?>">
      <div class="<?= ui('modal_head') ?>">
        <span id="<?= $titleId ?>" class="text-base font-bold"><?= $title ?></span>
        <button type="button" class="<?= ui('modal_close') ?>" onclick="closeModal('<?= $id ?>')">✕</button>
      </div>
      <div class="flex flex-col gap-3 p-[22px]"><?= $fields ?></div>
      <div class="<?= ui('card_footer') ?> flex justify-end gap-2">
        <button type="button" class="<?= ui('btn', 'cancel', 'md') ?>" onclick="closeModal('<?= $id ?>')">Cancel</button>
        <button type="submit" name="<?= $saveName ?>" id="<?= $btnId ?>" class="<?= ui('btn', 'grad_green') ?>"><?= $btnLabel ?></button>
      </div>
    </form>
  </div>
</div>
<?php }

/** Labelled input for modal forms. */
function modal_field(string $label, string $name, string $id, string $type = 'text', string $extra = ''): string {
    return '<div class="' . ui('field') . '"><label class="' . ui('label') . '">' . $label . '</label>'
        . '<input type="' . $type . '" name="' . $name . '" id="' . $id . '" class="' . ui('input') . '" required ' . $extra . '></div>';
}
?>

<?php
page_header(['System', 'Settings'], 'Settings Configuration');
if ($msg) echo alert_box($msg, $msg_type === 'success' ? 'success' : 'error');
?>

<!-- Tabs -->
<div class="mb-5 flex gap-1 overflow-x-auto border-b border-line">
  <?php foreach ($tabs as $key => $label): $on = $tab === $key; ?>
  <a href="?tab=<?= $key ?>" class="-mb-px whitespace-nowrap rounded-t-[10px] border border-b-0 px-4 py-2 text-sm font-semibold transition-colors
      <?= $on ? 'border-line bg-surface text-fg' : 'border-transparent text-muted hover:bg-subtle hover:text-fg' ?>"><?= $label ?></a>
  <?php endforeach; ?>
</div>

<!-- ==================== FACULTY TAB ==================== -->
<?php if ($tab === 'faculty'): ?>
<div class="<?= ui('card') ?>">
  <div class="<?= ui('toolbar') ?>">
    <div class="text-base font-bold">Manage Faculty</div>
    <button type="button" class="<?= ui('btn', 'primary', 'sm') ?> ml-auto" onclick="openFacultyModal()">Add Faculty</button>
  </div>
  <div class="overflow-x-auto">
  <table class="<?= ui('table') ?>">
    <thead><tr>
      <th class="<?= ui('th') ?>">ID</th><th class="<?= ui('th') ?>">Name</th><th class="<?= ui('th') ?>">Username</th>
      <th class="<?= ui('th') ?> w-[100px] !text-center">Actions</th>
    </tr></thead>
    <tbody>
      <?php if (mysqli_num_rows($faculty_res) > 0): while ($row = mysqli_fetch_assoc($faculty_res)): ?>
      <tr class="group">
        <td class="<?= ui('td') ?>">#<?= $row['id'] ?></td>
        <td class="<?= ui('td') ?> font-semibold"><?= htmlspecialchars($row['name']) ?></td>
        <td class="<?= ui('td') ?> text-muted"><?= htmlspecialchars($row['user_name']) ?></td>
        <td class="<?= ui('td') ?>"><?= row_actions(
            "editFaculty({$row['id']}, '" . htmlspecialchars(addslashes($row['name'])) . "', '" . htmlspecialchars(addslashes($row['user_name'])) . "', '" . htmlspecialchars(addslashes($row['password'])) . "')",
            "?tab=faculty&del_faculty={$row['id']}", 'Delete this faculty member?') ?></td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="4" class="<?= ui('empty') ?>">No faculty members found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<?php endif; ?>

<!-- ==================== SESSION TAB ==================== -->
<?php if ($tab === 'session'): ?>
<div class="<?= ui('card') ?>">
  <div class="<?= ui('toolbar') ?>">
    <div class="text-base font-bold">Manage Sessions</div>
    <button type="button" class="<?= ui('btn', 'primary', 'sm') ?> ml-auto" onclick="openSessionModal()">Add Session</button>
  </div>
  <div class="overflow-x-auto">
  <table class="<?= ui('table') ?>">
    <thead><tr>
      <th class="<?= ui('th') ?>">ID</th><th class="<?= ui('th') ?>">Year</th><th class="<?= ui('th') ?>">Term</th>
      <th class="<?= ui('th') ?> w-[100px] !text-center">Actions</th>
    </tr></thead>
    <tbody>
      <?php if (mysqli_num_rows($session_res) > 0): while ($row = mysqli_fetch_assoc($session_res)): ?>
      <tr class="group">
        <td class="<?= ui('td') ?>">#<?= $row['id'] ?></td>
        <td class="<?= ui('td') ?> font-semibold"><?= $row['year'] ?></td>
        <td class="<?= ui('td') ?>"><span class="rounded-md bg-subtle px-2 py-1 text-xs font-semibold"><?= htmlspecialchars($row['term']) ?></span></td>
        <td class="<?= ui('td') ?>"><?= row_actions(
            "editSession({$row['id']}, {$row['year']}, '" . htmlspecialchars(addslashes($row['term'])) . "')",
            "?tab=session&del_session={$row['id']}", 'Delete this session?') ?></td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="4" class="<?= ui('empty') ?>">No sessions found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<?php endif; ?>

<!-- ==================== COURSE TAB ==================== -->
<?php if ($tab === 'course'): ?>
<div class="<?= ui('card') ?>">
  <div class="<?= ui('toolbar') ?>">
    <div class="text-base font-bold">Manage Courses</div>
    <button type="button" class="<?= ui('btn', 'primary', 'sm') ?> ml-auto" onclick="openCourseModal()">Add Course</button>
  </div>
  <div class="overflow-x-auto">
  <table class="<?= ui('table') ?>">
    <thead><tr>
      <th class="<?= ui('th') ?>">ID</th><th class="<?= ui('th') ?>">Course Code</th><th class="<?= ui('th') ?>">Title</th><th class="<?= ui('th') ?>">Credits</th>
      <th class="<?= ui('th') ?> w-[100px] !text-center">Actions</th>
    </tr></thead>
    <tbody>
      <?php if (mysqli_num_rows($course_res) > 0): while ($row = mysqli_fetch_assoc($course_res)): ?>
      <tr class="group">
        <td class="<?= ui('td') ?>">#<?= $row['id'] ?></td>
        <td class="<?= ui('td') ?> font-semibold text-primary"><?= htmlspecialchars($row['code']) ?></td>
        <td class="<?= ui('td') ?> font-medium"><?= htmlspecialchars($row['title']) ?></td>
        <td class="<?= ui('td') ?>"><?= $row['credit'] ?></td>
        <td class="<?= ui('td') ?>"><?= row_actions(
            "editCourse({$row['id']}, '" . htmlspecialchars(addslashes($row['code'])) . "', '" . htmlspecialchars(addslashes($row['title'])) . "', {$row['credit']})",
            "?tab=course&del_course={$row['id']}", 'Delete this course?') ?></td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="5" class="<?= ui('empty') ?>">No courses found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<?php endif; ?>

<!-- ==================== MODALS ==================== -->
<?php
settings_modal('faculty-modal', '?tab=faculty', 'faculty-modal-title', 'Add Faculty', 'save_faculty', 'faculty-btn-label', 'Save Faculty',
    '<input type="hidden" name="f_id" id="f_id" value="0">'
    . modal_field('Full Name', 'name', 'f_name')
    . modal_field('Username (Login ID)', 'user_name', 'f_user')
    . modal_field('Password', 'password', 'f_pass'));

settings_modal('session-modal', '?tab=session', 'session-modal-title', 'Add Session', 'save_session', 'session-btn-label', 'Save Session',
    '<input type="hidden" name="s_id" id="s_id" value="0">'
    . modal_field('Year', 'year', 's_year', 'number', 'min="2000" max="2100" value="' . date('Y') . '"')
    . modal_field('Term (e.g. Spring Semester)', 'term', 's_term'));

settings_modal('course-modal', '?tab=course', 'course-modal-title', 'Add Course', 'save_course', 'course-btn-label', 'Save Course',
    '<input type="hidden" name="c_id" id="c_id" value="0">'
    . modal_field('Course Code (e.g. CS101)', 'code', 'c_code')
    . modal_field('Course Title', 'title', 'c_title')
    . modal_field('Credits', 'credit', 'c_credit', 'number', 'min="1" max="10" value="3"'));
?>

<script>
const $v = (id, val) => { document.getElementById(id).value = val; };
const $t = (id, txt) => { document.getElementById(id).innerText = txt; };

function openFacultyModal() {
  $v('f_id', '0'); $v('f_name', ''); $v('f_user', ''); $v('f_pass', '');
  $t('faculty-modal-title', 'Add Faculty'); $t('faculty-btn-label', 'Save Faculty');
  openModal('faculty-modal');
}
function editFaculty(id, name, user, pass) {
  $v('f_id', id); $v('f_name', name); $v('f_user', user); $v('f_pass', pass);
  $t('faculty-modal-title', 'Edit Faculty'); $t('faculty-btn-label', 'Update Faculty');
  openModal('faculty-modal');
}

function openSessionModal() {
  $v('s_id', '0'); $v('s_year', new Date().getFullYear()); $v('s_term', '');
  $t('session-modal-title', 'Add Session'); $t('session-btn-label', 'Save Session');
  openModal('session-modal');
}
function editSession(id, year, term) {
  $v('s_id', id); $v('s_year', year); $v('s_term', term);
  $t('session-modal-title', 'Edit Session'); $t('session-btn-label', 'Update Session');
  openModal('session-modal');
}

function openCourseModal() {
  $v('c_id', '0'); $v('c_code', ''); $v('c_title', ''); $v('c_credit', '3');
  $t('course-modal-title', 'Add Course'); $t('course-btn-label', 'Save Course');
  openModal('course-modal');
}
function editCourse(id, code, title, credit) {
  $v('c_id', id); $v('c_code', code); $v('c_title', title); $v('c_credit', credit);
  $t('course-modal-title', 'Edit Course'); $t('course-btn-label', 'Update Course');
  openModal('course-modal');
}
</script>

<?php require_once dirname(__DIR__) . "/src/views/layouts/app_footer.php"; ?>
