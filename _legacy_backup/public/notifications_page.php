<?php
require_once dirname(__DIR__) . "/src/views/layouts/app_header.php";

$faculty = mysqli_real_escape_string($conn, $faculty_user);

// ─── Actions ───
$toast_msg = $toast_type = '';

if (isset($_GET['mark_all'])) {
    mysqli_query($conn, "UPDATE notifications SET is_read=1 WHERE faculty_user='$faculty'");
    $toast_msg = 'All notifications marked as read.'; $toast_type = 'success';
}
if (isset($_GET['delete_all'])) {
    mysqli_query($conn, "DELETE FROM notifications WHERE faculty_user='$faculty'");
    $toast_msg = 'All notifications cleared.'; $toast_type = 'success';
}
if (isset($_GET['delete_id'])) {
    $del_id = (int)$_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM notifications WHERE id='$del_id' AND faculty_user='$faculty'");
    header("Location: /notifications_page.php"); exit();
}
if (isset($_GET['mark_id'])) {
    $mid = (int)$_GET['mark_id'];
    mysqli_query($conn, "UPDATE notifications SET is_read=1 WHERE id='$mid' AND faculty_user='$faculty'");
    header("Location: /notifications_page.php"); exit();
}

// ─── Filter ───
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$allowed = ['all','create','update','delete','unread'];
if (!in_array($filter, $allowed)) $filter = 'all';

$where = "faculty_user='$faculty'";
if ($filter === 'unread') $where .= " AND is_read=0";
elseif (in_array($filter, ['create','update','delete'])) $where .= " AND type='$filter'";

// ─── Load notifications ───
$notifs_q = mysqli_query($conn, "
    SELECT * FROM notifications WHERE $where ORDER BY created_at DESC
");
$notifs = [];
if ($notifs_q) while ($r = mysqli_fetch_assoc($notifs_q)) $notifs[] = $r;

// ─── Counts ───
function countWhere($conn, $where) {
    $r = mysqli_query($conn, "SELECT COUNT(*) cnt FROM notifications WHERE $where");
    return $r ? (mysqli_fetch_assoc($r)['cnt'] ?? 0) : 0;
}
$fac_where = "faculty_user='$faculty'";
$count_all    = countWhere($conn, $fac_where);
$count_unread = countWhere($conn, $fac_where . " AND is_read=0");
$count_create = countWhere($conn, $fac_where . " AND type='create'");
$count_update = countWhere($conn, $fac_where . " AND type='update'");
$count_delete = countWhere($conn, $fac_where . " AND type='delete'");

$type_config = [
    'create' => ['icon'=>'➕','text'=>'text-present','soft'=>'bg-present-soft','ring'=>'border-present/20','label'=>'Created'],
    'update' => ['icon'=>'✏️','text'=>'text-primary','soft'=>'bg-primary-soft','ring'=>'border-primary/20','label'=>'Updated'],
    'delete' => ['icon'=>'🗑️','text'=>'text-absent', 'soft'=>'bg-absent-soft', 'ring'=>'border-absent/20', 'label'=>'Deleted'],
];

// Filter tabs: key => [label, count, emoji, text, soft bg, active border, active ring]
$tabs = [
    'all'    => ['All',     $count_all,    '📋', 'text-primary', 'bg-primary-soft', 'border-primary', 'ring-primary/20'],
    'unread' => ['Unread',  $count_unread, '🔵', 'text-leave',   'bg-leave-soft',   'border-leave',   'ring-leave/20'],
    'create' => ['Created', $count_create, '➕', 'text-present', 'bg-present-soft', 'border-present', 'ring-present/20'],
    'update' => ['Updated', $count_update, '✏️', 'text-primary', 'bg-primary-soft', 'border-primary', 'ring-primary/20'],
    'delete' => ['Deleted', $count_delete, '🗑️', 'text-absent',  'bg-absent-soft',  'border-absent',  'ring-absent/20'],
];
$labels = ['all'=>'All','unread'=>'Unread','create'=>'Student Created','update'=>'Student Updated','delete'=>'Student Deleted'];
?>

<?php if ($toast_msg): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    showToast('<?= $toast_type === 'success' ? '✅ Done' : 'ℹ️ Info' ?>', '<?= htmlspecialchars($toast_msg, ENT_QUOTES) ?>', '<?= $toast_type ?>', 3000);
});
</script>
<?php endif; ?>

<!-- ─── Page Header ─── -->
<div class="mb-6 flex flex-wrap items-start justify-between gap-3">
  <div>
    <?php page_header(['Home', 'Notifications'], '🔔 Notifications',
        $count_unread > 0 ? "<strong class='text-absent'>$count_unread unread</strong> of $count_all total" : "$count_all total notifications"); ?>
  </div>
  <div class="flex flex-wrap items-center gap-2">
    <?php if ($count_unread > 0): ?>
    <a href="?mark_all=1" class="<?= ui('btn', 'secondary', 'sm') ?>" onclick="return confirm('Mark all as read?')">Mark All Read</a>
    <?php endif; ?>
    <?php if ($count_all > 0): ?>
    <a href="?delete_all=1" class="<?= ui('btn', 'danger', 'sm') ?>" onclick="return confirm('Delete ALL notifications? This cannot be undone.')">Clear All</a>
    <?php endif; ?>
  </div>
</div>

<!-- ─── Filter Cards ─── -->
<div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-[repeat(auto-fit,minmax(140px,1fr))]">
  <?php foreach ($tabs as $key => [$label, $n, $em, $text, $soft, $bdr, $ring]): $active = $filter === $key; ?>
  <a href="?filter=<?= $key ?>" class="flex flex-col gap-1.5 rounded-[14px] border-2 p-4 transition-all duration-150
      <?= $active ? "$soft $bdr ring-[3px] $ring" : 'border-line bg-surface shadow-sm hover:border-line-hover' ?>">
    <span class="text-[1.2rem]"><?= $em ?></span>
    <span class="text-2xl font-extrabold tracking-tighter <?= $text ?>"><?= $n ?></span>
    <span class="text-[.72rem] font-bold uppercase tracking-wider text-muted"><?= $label ?></span>
  </a>
  <?php endforeach; ?>
</div>

<!-- ─── Notifications List ─── -->
<div class="<?= ui('card') ?>">
  <div class="<?= ui('card_header') ?> flex-wrap gap-2.5">
    <span class="<?= ui('card_title') ?>">
      <?= $labels[$filter] ?? 'All' ?> Notifications
      <span class="ml-1.5 text-[.8rem] font-medium text-muted">(<?= count($notifs) ?>)</span>
    </span>
    <?= search_box('notif-search', 'Search notifications…', 'filterNotifs(this.value)') ?>
  </div>

  <div id="notif-list-body">
    <?php if (empty($notifs)): ?>
    <div class="px-6 py-16 text-center text-muted">
      <div class="mb-3 text-[2.5rem]">🔕</div>
      <div class="mb-1.5 text-base font-bold text-fg">No notifications here</div>
      <p class="mt-1 text-[.85rem]">
        <?= $filter !== 'all' ? '<a href="?filter=all" class="text-primary">View all notifications</a>' : 'Notifications will appear here when students are added, updated, or deleted.' ?>
      </p>
    </div>
    <?php else: foreach ($notifs as $n):
      $cfg = $type_config[$n['type']] ?? $type_config['create'];
      $is_unread = $n['is_read'] == 0;
      $time_diff = time() - strtotime($n['created_at']);
      if ($time_diff < 60)        $time_label = 'Just now';
      elseif ($time_diff < 3600)  $time_label = floor($time_diff/60) . 'm ago';
      elseif ($time_diff < 86400) $time_label = floor($time_diff/3600) . 'h ago';
      else                        $time_label = date('M j, Y g:i A', strtotime($n['created_at']));
    ?>
    <div class="notif-row flex flex-col gap-3 border-b border-line px-5 py-4 transition-colors sm:flex-row sm:items-start sm:gap-4 <?= $is_unread ? $cfg['soft'] : '' ?>"
         data-search="<?= htmlspecialchars(strtolower($n['title'].' '.$n['message'])) ?>">
      <div class="mt-0.5 flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border text-[1.2rem] <?= $cfg['soft'] ?> <?= $cfg['ring'] ?>"><?= $cfg['icon'] ?></div>

      <div class="min-w-0 flex-grow">
        <div class="mb-1 flex flex-wrap items-center gap-2">
          <span class="text-sm font-bold <?= $cfg['text'] ?>"><?= htmlspecialchars($n['title']) ?></span>
          <span class="rounded-full border px-2 py-0.5 text-[.68rem] font-bold uppercase tracking-wider <?= $cfg['soft'] ?> <?= $cfg['text'] ?> <?= $cfg['ring'] ?>"><?= $cfg['label'] ?></span>
          <?php if ($is_unread): ?><span class="unread-dot h-2 w-2 shrink-0 rounded-full bg-absent" title="Unread"></span><?php endif; ?>
        </div>
        <div class="mb-1.5 text-[.85rem] leading-normal text-muted"><?= htmlspecialchars($n['message']) ?></div>
        <div class="flex items-center gap-1.5 text-xs text-faint">
          <?= icon_span('calendar', 'h-3 w-3', 'opacity-60') ?>
          <span><?= date('D, M j, Y · g:i A', strtotime($n['created_at'])) ?></span>
          <span class="text-line">·</span>
          <span><?= $time_label ?></span>
        </div>
      </div>

      <div class="flex shrink-0 flex-row items-center gap-1.5 sm:flex-col sm:items-end">
        <?php if ($is_unread): ?>
        <a href="?mark_id=<?= $n['id'] ?>&filter=<?= $filter ?>" data-mark-id="<?= $n['id'] ?>" class="<?= ui('btn', 'secondary', 'sm') ?>" title="Mark as read">Mark Read</a>
        <?php else: ?>
        <span class="whitespace-nowrap rounded-md bg-present-soft px-2 py-1 text-[.72rem] font-semibold text-present">✓ Read</span>
        <?php endif; ?>
        <a href="?delete_id=<?= $n['id'] ?>" class="<?= ui('btn', 'danger_soft', 'sm') ?>" onclick="return confirm('Delete this notification?')">Delete</a>
      </div>
    </div>
    <?php endforeach; endif; ?>
  </div>

  <?php if (!empty($notifs)): ?>
  <div class="<?= ui('card_footer') ?> flex items-center justify-between">
    <span>Showing <?= count($notifs) ?> notification<?= count($notifs) !== 1 ? 's' : '' ?></span>
    <div class="flex gap-2">
      <?php if ($count_unread > 0): ?>
      <a href="?mark_all=1" class="<?= ui('btn', 'secondary', 'sm') ?>" onclick="return confirm('Mark all as read?')">Mark All Read</a>
      <?php endif; ?>
      <?php if ($count_all > 0): ?>
      <a href="?delete_all=1" class="<?= ui('btn', 'danger', 'sm') ?>" onclick="return confirm('Clear all notifications?')">Clear All</a>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<script>
function filterNotifs(q) {
  q = q.toLowerCase();
  document.querySelectorAll('.notif-row').forEach(row => {
    row.style.display = row.dataset.search.includes(q) ? '' : 'none';
  });
}

// Passive read-on-view: mark unread items as read after 5s
document.querySelectorAll('.notif-row [data-mark-id]').forEach(btn => {
  const row = btn.closest('.notif-row');
  setTimeout(() => {
    fetch(window.APP.notifApi + '?action=mark_read&id=' + btn.dataset.markId);
    ['bg-primary-soft','bg-present-soft','bg-absent-soft'].forEach(c => row.classList.remove(c));
    const dot = row.querySelector('.unread-dot'); if (dot) dot.remove();
  }, 5000);
});
</script>

<?php require_once dirname(__DIR__) . "/src/views/layouts/app_footer.php"; ?>
