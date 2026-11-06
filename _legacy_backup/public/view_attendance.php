<?php
require_once dirname(__DIR__) . "/src/views/layouts/app_header.php";

// ─── Filters ───
$filter_course  = isset($_GET['course_id'])  ? (int)$_GET['course_id']  : 0;
$filter_session = isset($_GET['session_id']) ? (int)$_GET['session_id'] : 0;
$filter_date    = isset($_GET['on_date'])    ? mysqli_real_escape_string($conn, $_GET['on_date'])  : '';
$filter_student = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$filter_status  = isset($_GET['status'])     ? mysqli_real_escape_string($conn, $_GET['status'])  : '';

$courses  = mysqli_query($conn, "SELECT id, code, title FROM course_details ORDER BY code");
$sessions = mysqli_query($conn, "SELECT id, year, term FROM session_details ORDER BY year DESC");
$students_dd = mysqli_query($conn, "SELECT id, roll_no, name FROM student_details ORDER BY roll_no");

// ─── Query Builder ───
$where = ["1=1"];
if ($filter_course)  $where[] = "a.course_id='$filter_course'";
if ($filter_session) $where[] = "a.session_id='$filter_session'";
if ($filter_date)    $where[] = "a.on_date='$filter_date'";
if ($filter_student) $where[] = "a.student_id='$filter_student'";
if ($filter_status)  $where[] = "a.status='$filter_status'";
$where_str = implode(' AND ', $where);

$records_q = mysqli_query($conn, "
    SELECT a.on_date, a.status,
           s.name, s.roll_no,
           c.code AS course_code, c.title AS course_title,
           se.year, se.term,
           f.name AS faculty_name
    FROM attendance_details a
    JOIN student_details s  ON a.student_id  = s.id
    JOIN course_details c   ON a.course_id   = c.id
    JOIN session_details se ON a.session_id  = se.id
    LEFT JOIN faculty_details f ON a.faculty_id = f.id
    WHERE $where_str
    ORDER BY a.on_date DESC, s.roll_no ASC
");
$records = []; if ($records_q) while ($r = mysqli_fetch_assoc($records_q)) $records[] = $r;

// ─── Summary Counts ───
$count = count($records);
$counts = ['Present'=>0,'Absent'=>0,'Late'=>0,'Leave'=>0];
foreach ($records as $r) { if (isset($counts[$r['status']])) $counts[$r['status']]++; }
?>

<?php
page_header(['Home', 'Reports'], 'Attendance Reports', 'Filter, browse, and export attendance records.');

$mini = [
    ['Present', '✅', 'border-l-present', 'text-present', $counts['Present']],
    ['Absent',  '❌', 'border-l-absent',  'text-absent',  $counts['Absent']],
    ['Late',    '⏰', 'border-l-late',    'text-late',    $counts['Late']],
    ['Leave',   '📝', 'border-l-leave',   'text-leave',   $counts['Leave']],
    ['Total',   '📋', 'border-l-primary', 'text-primary', $count],
];
?>

<!-- ─── Mini Stats ─── -->
<div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-[repeat(auto-fit,minmax(130px,1fr))]">
  <?php foreach ($mini as [$label, $em, $border, $text, $n]): ?>
  <div class="flex items-center gap-3 rounded-xl border border-l-[3px] border-line bg-surface p-4 shadow-sm <?= $border ?>">
    <span class="text-[1.2rem]"><?= $em ?></span>
    <div>
      <div class="text-[1.4rem] font-extrabold tracking-tighter <?= $text ?>"><?= $n ?></div>
      <div class="text-[.7rem] font-semibold uppercase tracking-wider text-muted"><?= $label ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- ─── Filters ─── -->
<div class="<?= ui('card') ?> mb-5">
  <div class="<?= ui('card_header') ?>">
    <span class="<?= ui('card_title') ?>">🔎 Filter Records</span>
    <a href="view_attendance.php" class="<?= ui('btn', 'ghost', 'sm') ?>">Clear Filters</a>
  </div>
  <div class="<?= ui('card_body') ?>">
    <form method="GET" action="view_attendance.php">
      <div class="<?= ui('form_grid') ?>">
        <div class="<?= ui('field') ?>">
          <label class="<?= ui('label') ?>">Course</label>
          <select name="course_id" class="<?= ui('select') ?>">
            <option value="">All Courses</option>
            <?php while ($c = mysqli_fetch_assoc($courses)): ?>
            <option value="<?= $c['id'] ?>" <?= $filter_course == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['code'].' — '.$c['title']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="<?= ui('field') ?>">
          <label class="<?= ui('label') ?>">Session</label>
          <select name="session_id" class="<?= ui('select') ?>">
            <option value="">All Sessions</option>
            <?php while ($s = mysqli_fetch_assoc($sessions)): ?>
            <option value="<?= $s['id'] ?>" <?= $filter_session == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['year'].' — '.$s['term']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="<?= ui('field') ?>">
          <label class="<?= ui('label') ?>">Student</label>
          <select name="student_id" class="<?= ui('select') ?>">
            <option value="">All Students</option>
            <?php while ($s = mysqli_fetch_assoc($students_dd)): ?>
            <option value="<?= $s['id'] ?>" <?= $filter_student == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['roll_no'].' — '.$s['name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="<?= ui('field') ?>">
          <label class="<?= ui('label') ?>">Date</label>
          <input type="date" name="on_date" class="<?= ui('input') ?>" value="<?= htmlspecialchars($filter_date) ?>">
        </div>
        <div class="<?= ui('field') ?>">
          <label class="<?= ui('label') ?>">Status</label>
          <select name="status" class="<?= ui('select') ?>">
            <option value="">All Statuses</option>
            <option value="Present" <?= $filter_status === 'Present' ? 'selected' : '' ?>>✅ Present</option>
            <option value="Absent"  <?= $filter_status === 'Absent'  ? 'selected' : '' ?>>❌ Absent</option>
            <option value="Late"    <?= $filter_status === 'Late'    ? 'selected' : '' ?>>⏰ Late</option>
            <option value="Leave"   <?= $filter_status === 'Leave'   ? 'selected' : '' ?>>📝 Leave</option>
          </select>
        </div>
        <div class="flex items-end">
          <button type="submit" class="<?= ui('btn', 'grad', 'full') ?>">Apply Filters</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- ─── Results Table ─── -->
<div class="<?= ui('card') ?>">
  <div class="<?= ui('toolbar') ?>">
    <?= search_box('report-search', 'Search name, roll, course…', 'filterReport(this.value)') ?>
    <button type="button" class="<?= ui('btn', 'secondary', 'sm') ?>" onclick="exportCSV()">Export CSV</button>
    <span class="ml-auto text-[.8rem] text-muted"><?= $count ?> records</span>
  </div>
  <div class="overflow-x-auto">
  <table id="report-table" class="<?= ui('table') ?>">
    <thead>
      <tr>
        <?php foreach (['#', 'Student', 'Roll No', 'Course', 'Session', 'Date', 'Status', 'Faculty'] as $h): ?>
        <th class="<?= ui('th') ?>"><?= $h ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($records)): ?>
      <tr><td colspan="8" class="<?= ui('empty') ?>">
        <div class="mb-3 text-[2.5rem]">📊</div>
        <div class="mb-1.5 text-base font-bold text-fg">No records found</div>
        <p>Try adjusting your filters or <a href="take_attendance.php" class="text-primary">record attendance</a>.</p>
      </td></tr>
      <?php else: foreach ($records as $i => $r): ?>
      <tr class="report-row group" data-search="<?= htmlspecialchars(strtolower($r['name'].' '.$r['roll_no'].' '.$r['course_code'].' '.$r['status'])) ?>">
        <td class="<?= ui('td') ?> text-[.8rem] text-faint"><?= $i + 1 ?></td>
        <td class="<?= ui('td') ?>">
          <div class="flex items-center gap-2.5">
            <?= avatar(strtoupper(substr($r['name'], 0, 1)), 'h-[30px] w-[30px] text-[.72rem]') ?>
            <span class="text-[.85rem] font-semibold"><?= htmlspecialchars($r['name']) ?></span>
          </div>
        </td>
        <td class="<?= ui('td') ?> text-[.8rem] font-semibold"><?= htmlspecialchars($r['roll_no']) ?></td>
        <td class="<?= ui('td') ?>">
          <div class="text-[.8rem] font-semibold"><?= htmlspecialchars($r['course_code']) ?></div>
          <div class="text-[.72rem] text-muted"><?= htmlspecialchars($r['course_title']) ?></div>
        </td>
        <td class="<?= ui('td') ?> text-[.8rem]"><?= htmlspecialchars($r['year'].' – '.$r['term']) ?></td>
        <td class="<?= ui('td') ?> whitespace-nowrap text-[.8rem]"><?= date('M j, Y', strtotime($r['on_date'])) ?></td>
        <td class="<?= ui('td') ?>"><span class="<?= CHIP_BASE ?> <?= status_chip_classes($r['status']) ?>"><?= htmlspecialchars($r['status']) ?></span></td>
        <td class="<?= ui('td') ?> text-[.8rem] text-muted"><?= htmlspecialchars($r['faculty_name'] ?? '—') ?></td>
      </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
  </div>
</div>

<script>
function filterReport(q) {
  q = q.toLowerCase();
  document.querySelectorAll('.report-row').forEach(tr => {
    tr.style.display = tr.dataset.search.includes(q) ? '' : 'none';
  });
}

function exportCSV() {
  const rows = [['#','Student','Roll No','Course','Session','Date','Status','Faculty']];
  document.querySelectorAll('#report-table tbody .report-row').forEach((tr, i) => {
    const cells = tr.querySelectorAll('td');
    rows.push([i+1, ...[1,2,3,4,5,6,7].map(n => cells[n]?.innerText?.trim())]);
  });
  const csv = rows.map(r => r.map(c => `"${String(c||'').replace(/"/g,'""')}"`).join(',')).join('\n');
  const a = document.createElement('a');
  a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
  a.download = 'attendance_report_<?= date('Y-m-d') ?>.csv';
  a.click();
  showToast('Export Complete', 'CSV report downloaded successfully.', 'success');
}
</script>

<?php require_once dirname(__DIR__) . "/src/views/layouts/app_footer.php"; ?>
