<?php
require_once dirname(__DIR__) . "/src/views/layouts/app_header.php";

// ─── Fetch live statistics ───
function q($conn, $sql) {
    $r = mysqli_query($conn, $sql);
    return $r ? (mysqli_fetch_assoc($r)['cnt'] ?? 0) : 0;
}

$total_students  = q($conn, "SELECT COUNT(*) cnt FROM student_details");
$total_courses   = q($conn, "SELECT COUNT(*) cnt FROM course_details");
$total_faculty   = q($conn, "SELECT COUNT(*) cnt FROM faculty_details");
$today           = date('Y-m-d');
$today_present   = q($conn, "SELECT COUNT(DISTINCT student_id) cnt FROM attendance_details WHERE on_date='$today' AND status='Present'");
$today_absent    = q($conn, "SELECT COUNT(DISTINCT student_id) cnt FROM attendance_details WHERE on_date='$today' AND status='Absent'");
$today_late      = q($conn, "SELECT COUNT(DISTINCT student_id) cnt FROM attendance_details WHERE on_date='$today' AND status='Late'");
$today_leave     = q($conn, "SELECT COUNT(DISTINCT student_id) cnt FROM attendance_details WHERE on_date='$today' AND status='Leave'");
$att_pct         = $total_students > 0 ? round(($today_present / $total_students) * 100) : 0;

// ─── Last 7 days attendance trend ───
$trend_labels = $trend_present = $trend_absent = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $trend_labels[] = date('D', strtotime($d));
    $trend_present[] = q($conn, "SELECT COUNT(DISTINCT student_id) cnt FROM attendance_details WHERE on_date='$d' AND status='Present'");
    $trend_absent[]  = q($conn, "SELECT COUNT(DISTINCT student_id) cnt FROM attendance_details WHERE on_date='$d' AND status='Absent'");
}

// ─── Recent activity (last 5 attendance actions) ───
$recent_q = mysqli_query($conn, "
    SELECT s.name, s.roll_no, a.status, a.on_date, c.code
    FROM attendance_details a
    JOIN student_details s ON a.student_id = s.id
    JOIN course_details c  ON a.course_id  = c.id
    ORDER BY a.on_date DESC
    LIMIT 5
");
$recent = [];
if ($recent_q) while ($r = mysqli_fetch_assoc($recent_q)) $recent[] = $r;
?>

<?php
page_header(['Home', 'Dashboard'], 'Dashboard',
    'Welcome back, <strong>' . htmlspecialchars($faculty_user) . '</strong> — ' . date('l, F j, Y'));

// Stat card definitions: [tone, emoji, label, value, footer, progress%|null]
$tones = [
    'primary' => ['bar' => 'bg-primary', 'icon' => 'bg-primary-soft text-primary', 'fill' => 'bg-primary'],
    'present' => ['bar' => 'bg-present', 'icon' => 'bg-present-soft text-present', 'fill' => 'bg-present'],
    'absent'  => ['bar' => 'bg-absent',  'icon' => 'bg-absent-soft text-absent',   'fill' => 'bg-absent'],
    'late'    => ['bar' => 'bg-late',    'icon' => 'bg-late-soft text-late',       'fill' => 'bg-late'],
    'leave'   => ['bar' => 'bg-leave',   'icon' => 'bg-leave-soft text-leave',     'fill' => 'bg-leave'],
    'holiday' => ['bar' => 'bg-holiday', 'icon' => 'bg-holiday-soft text-holiday', 'fill' => 'bg-primary'],
];
$stats = [
    ['primary', '📚', 'Total Students',  $total_students,  "$total_courses courses active", null],
    ['present', '✅', 'Present Today',   $today_present,   "out of $total_students students", $att_pct],
    ['absent',  '❌', 'Absent Today',    $today_absent,    'students not present', null],
    ['late',    '⏰', 'Late Today',      $today_late,      'arrived late', null],
    ['leave',   '📝', 'Leave Requests',  $today_leave,     'on leave today', null],
    ['holiday', '📊', 'Attendance %',    $att_pct . '%',   "today's overall rate", $att_pct],
];
$dot_colors = ['Present' => 'bg-present', 'Absent' => 'bg-absent', 'Late' => 'bg-late', 'Leave' => 'bg-leave'];
$txt_colors = ['Present' => 'text-present', 'Absent' => 'text-absent', 'Late' => 'text-late', 'Leave' => 'text-leave'];
?>

<!-- ─── Stats Row ─── -->
<div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-[repeat(auto-fit,minmax(180px,1fr))]">
  <?php foreach ($stats as [$tone, $emoji, $label, $value, $footer, $progress]): $t = $tones[$tone]; ?>
  <div class="relative flex flex-col gap-3 overflow-hidden rounded-[14px] border border-line bg-surface p-5 shadow-sm transition-all duration-150 hover:-translate-y-px hover:shadow-md">
    <div class="absolute inset-x-0 top-0 h-[3px] <?= $t['bar'] ?>"></div>
    <div class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-[10px] text-[1.1rem] <?= $t['icon'] ?>"><?= $emoji ?></div>
    <div class="text-xs font-semibold uppercase tracking-wider text-muted"><?= $label ?></div>
    <div class="text-[2rem] font-extrabold tracking-tighter"><?= $value ?></div>
    <div class="text-xs text-muted"><?= $footer ?></div>
    <?php if ($progress !== null): ?>
    <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-subtle">
      <div class="h-full rounded-full transition-[width] duration-500 <?= $t['fill'] ?>" style="width:<?= (int)$progress ?>%"></div>
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>

<!-- ─── Charts Row ─── -->
<div class="mb-6 grid grid-cols-1 gap-4 xl:grid-cols-[2fr_1fr]">
  <div class="<?= ui('card') ?>">
    <div class="<?= ui('card_header') ?>">
      <span class="<?= ui('card_title') ?>">📈 7-Day Attendance Trend</span>
      <span class="text-xs text-muted">Last 7 days</span>
    </div>
    <div class="<?= ui('card_body') ?> relative h-60"><canvas id="trendChart"></canvas></div>
  </div>
  <div class="<?= ui('card') ?>">
    <div class="<?= ui('card_header') ?>"><span class="<?= ui('card_title') ?>">🎯 Today's Overview</span></div>
    <div class="<?= ui('card_body') ?> relative flex h-60 items-center justify-center"><canvas id="pieChart"></canvas></div>
  </div>
</div>

<!-- ─── Bottom Grid ─── -->
<div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
  <!-- Recent Activity -->
  <div class="<?= ui('card') ?> md:col-span-2">
    <div class="<?= ui('card_header') ?>">
      <span class="<?= ui('card_title') ?>">🔔 Recent Attendance Activity</span>
      <a href="/view_attendance.php" class="<?= ui('btn', 'ghost', 'sm') ?> !text-primary">View All →</a>
    </div>
    <div class="px-5">
      <?php if (empty($recent)): ?>
        <div class="<?= ui('empty') ?>">No attendance records yet.</div>
      <?php else: foreach ($recent as $r): ?>
        <div class="flex items-start gap-3 border-b border-line py-2.5 last:border-b-0">
          <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full <?= $dot_colors[$r['status']] ?? 'bg-faint' ?>"></span>
          <div>
            <div class="text-[.825rem]">
              <strong><?= htmlspecialchars($r['name']) ?></strong> (<?= htmlspecialchars($r['roll_no']) ?>) marked
              <strong class="<?= $txt_colors[$r['status']] ?? 'text-muted' ?>"><?= htmlspecialchars($r['status']) ?></strong>
              in <em><?= htmlspecialchars($r['code']) ?></em>
            </div>
            <div class="text-xs text-muted"><?= date('M j, Y', strtotime($r['on_date'])) ?></div>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="<?= ui('card') ?>">
    <div class="<?= ui('card_header') ?>"><span class="<?= ui('card_title') ?>">⚡ Quick Actions</span></div>
    <div class="<?= ui('card_body') ?> flex flex-col gap-2.5">
      <a href="/take_attendance.php"  class="<?= ui('btn', 'primary', 'full') ?> !justify-start">Take Today's Attendance</a>
      <a href="/manage_students.php"  class="<?= ui('btn', 'secondary', 'full') ?> !justify-start">Manage Student Catalog</a>
      <a href="/view_attendance.php"  class="<?= ui('btn', 'secondary', 'full') ?> !justify-start">Generate Reports</a>
    </div>
  </div>
</div>

<!-- ─── Charts JS ─── -->
<script>
const trendLabels  = <?= json_encode($trend_labels) ?>;
const trendPresent = <?= json_encode($trend_present) ?>;
const trendAbsent  = <?= json_encode($trend_absent) ?>;

const isDark = document.documentElement.classList.contains('dark');
Chart.defaults.color = isDark ? '#94A3B8' : '#64748B';
Chart.defaults.borderColor = isDark ? '#334155' : '#E2E8F0';
Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";

new Chart(document.getElementById('trendChart'), {
  type: 'line',
  data: {
    labels: trendLabels,
    datasets: [
      { label: 'Present', data: trendPresent, borderColor:'#16A34A', backgroundColor:'rgba(22,163,74,.08)', tension:.4, fill:true, pointRadius:4, pointHoverRadius:6 },
      { label: 'Absent',  data: trendAbsent,  borderColor:'#DC2626', backgroundColor:'rgba(220,38,38,.06)', tension:.4, fill:true, pointRadius:4, pointHoverRadius:6 },
    ]
  },
  options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom',labels:{padding:16,usePointStyle:true}}}, scales:{y:{beginAtZero:true,ticks:{stepSize:1}}} }
});

const todayP = <?= $today_present ?>, todayA = <?= $today_absent ?>, todayL = <?= $today_late ?>, todayLv = <?= $today_leave ?>, noData = (todayP+todayA+todayL+todayLv === 0);
new Chart(document.getElementById('pieChart'), {
  type: 'doughnut',
  data: {
    labels: noData ? ['No Data'] : ['Present', 'Absent', 'Late', 'Leave'],
    datasets: [{
      data: noData ? [1] : [todayP, todayA, todayL, todayLv],
      backgroundColor: noData ? ['#E2E8F0'] : ['#16A34A','#DC2626','#EA580C','#7C3AED'],
      borderWidth: 2, borderColor: isDark ? '#1E293B' : '#FFFFFF'
    }]
  },
  options: { responsive:true, maintainAspectRatio:false, cutout:'65%',
    plugins:{ legend:{ position:'bottom', labels:{ padding:14, usePointStyle:true, font:{ size:11 } } } } }
});
</script>

<?php require_once dirname(__DIR__) . "/src/views/layouts/app_footer.php"; ?>
