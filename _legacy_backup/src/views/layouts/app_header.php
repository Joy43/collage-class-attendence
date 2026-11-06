<?php
/**
 * Authenticated app layout — opening half (sidebar + top bar).
 * Closed by app_footer.php. Set $page_title before including to override the title.
 */
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['faculty'])) {
    header("Location: /login.php");
    exit();
}

require_once dirname(__DIR__, 2) . "/config/database.php";
require_once dirname(__DIR__, 2) . "/helpers/icons.php";
require_once dirname(__DIR__, 2) . "/helpers/ui.php";

$current_page = basename($_SERVER['PHP_SELF']);
$faculty_user = $_SESSION['faculty'];
$initials     = strtoupper(substr($faculty_user, 0, 2));

/** Sidebar link classes, active-aware. */
function nav_classes(string $page, string $current): string {
    $base = 'mb-0.5 flex items-center gap-2.5 whitespace-nowrap rounded-[10px] border px-3 py-[9px] text-sm font-medium transition-all duration-150 group';
    return $page === $current
        ? "$base border-primary/25 bg-primary/15 text-white"
        : "$base border-transparent text-slate-400 hover:bg-slate-800 hover:text-slate-200";
}
function nav_icon_classes(string $page, string $current): string {
    return $page === $current ? 'text-blue-400 opacity-100' : 'opacity-75 group-hover:opacity-100';
}

$main_nav = [
    ['dashboard.php',        'grid',      'Dashboard'],
    ['take_attendance.php',  'clipboard', 'Attendance'],
    ['manage_students.php',  'users',     'Students'],
    ['view_attendance.php',  'bar-chart', 'Reports'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require __DIR__ . "/../partials/head.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="h-screen w-screen overflow-hidden bg-canvas font-sans leading-normal text-fg transition-colors duration-150">

<div class="flex h-screen w-screen overflow-hidden">

<!-- ══════════════════ SIDEBAR ══════════════════ -->
<aside id="sidebar" class="scroll-none fixed inset-y-0 left-0 z-[200] flex h-full w-[260px] shrink-0 -translate-x-full flex-col overflow-y-auto overflow-x-hidden bg-slate-900 shadow-xl transition-transform duration-200 lg:static lg:translate-x-0 lg:shadow-none">
  <div class="flex items-center gap-2.5 border-b border-white/5 px-[18px] py-5">
    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-primary text-[.9rem] font-extrabold text-white shadow-[0_4px_12px_rgba(37,99,235,.4)]">A</div>
    <div>
      <div class="text-base font-bold text-slate-50">AttApp</div>
      <div class="mt-px text-[.65rem] font-medium text-slate-500">Management System</div>
    </div>
  </div>

  <div class="px-[18px] pb-1.5 pt-[18px] text-[.67rem] font-bold uppercase tracking-wider text-slate-600">Main Menu</div>
  <nav class="px-2.5 py-2">
    <?php foreach ($main_nav as [$page, $ic, $label]): ?>
    <a href="/<?= $page ?>" class="<?= nav_classes($page, $current_page) ?>">
      <?= icon_span($ic, 'h-[18px] w-[18px]', nav_icon_classes($page, $current_page)) ?> <?= $label ?>
    </a>
    <?php endforeach; ?>
  </nav>

  <div class="px-[18px] pb-1.5 pt-[18px] text-[.67rem] font-bold uppercase tracking-wider text-slate-600">System</div>
  <nav class="px-2.5 py-2">
    <a href="/notifications_page.php" id="sidebar-notif-btn" class="<?= nav_classes('notifications_page.php', $current_page) ?>">
      <?= icon_span('bell', 'h-[18px] w-[18px]', nav_icon_classes('notifications_page.php', $current_page)) ?>
      <span>Notifications</span>
      <span id="sidebar-notif-badge" class="ml-auto hidden min-w-[20px] justify-center rounded-full bg-primary px-[7px] py-px text-[.68rem] font-bold text-white">0</span>
    </a>
    <a href="/settings.php" class="<?= nav_classes('settings.php', $current_page) ?>">
      <?= icon_span('settings', 'h-[18px] w-[18px]', nav_icon_classes('settings.php', $current_page)) ?> Settings
    </a>
  </nav>

  <div class="mt-auto border-t border-white/5 px-2.5 py-3.5">
    <div class="flex items-center gap-2.5 rounded-[10px] px-3 py-2.5 transition-colors hover:bg-slate-800">
      <?= avatar($initials) ?>
      <div>
        <div class="text-[.825rem] font-semibold text-slate-200"><?= htmlspecialchars($faculty_user) ?></div>
        <div class="text-[.7rem] text-slate-500">Faculty Member</div>
      </div>
    </div>
    <a href="/logout.php" class="mt-1 flex items-center gap-2 rounded-lg px-3 py-2 text-[.8rem] font-semibold text-slate-400 transition-all duration-150 hover:bg-red-600/10 hover:text-red-300">
      <?= icon_span('log-out', 'h-3.5 w-3.5') ?> Sign Out
    </a>
  </div>
</aside>

<!-- ══════════════════ PAGE CONTAINER ══════════════════ -->
<div class="flex h-full min-w-0 flex-grow flex-col overflow-hidden">

  <!-- TOP NAV -->
  <header class="flex h-16 shrink-0 items-center gap-3 border-b border-line bg-surface px-4 transition-colors duration-150 sm:px-6">
    <button id="menu-btn" type="button" class="<?= ui('icon_btn') ?> lg:hidden" onclick="toggleSidebar()" aria-label="Menu">
      <?= icon('menu') ?>
    </button>

    <!-- Search -->
    <div class="flex max-w-[380px] flex-grow items-center gap-2 rounded-full border border-line bg-subtle px-3.5 py-[7px] transition-all duration-150 focus-within:border-primary focus-within:bg-surface focus-within:ring-[3px] focus-within:ring-primary/20">
      <?= icon_span('search', 'h-4 w-4', 'text-faint') ?>
      <input type="text" id="global-search" placeholder="Search students, courses…" class="w-full border-0 bg-transparent text-sm text-fg outline-none placeholder:text-faint">
    </div>

    <div class="ml-auto flex items-center gap-1">
      <!-- Notifications -->
      <div class="relative" id="notif-wrap">
        <button type="button" id="notif-btn" title="Notifications" class="<?= ui('icon_btn') ?>" onclick="toggleNotifPanel()">
          <?= icon('bell') ?>
          <span id="notif-dot" class="absolute right-1.5 top-1.5 hidden h-2 w-2 rounded-full border-2 border-surface bg-absent"></span>
        </button>
        <span id="notif-badge" class="pointer-events-none absolute -right-1 -top-1 hidden h-[17px] min-w-[17px] items-center justify-center rounded-full border-2 border-surface bg-absent px-1 text-[.62rem] font-extrabold leading-none text-white">0</span>

        <!-- Dropdown panel -->
        <div id="notif-panel" class="absolute right-0 top-[calc(100%+10px)] z-[9999] hidden w-[min(360px,calc(100vw-2rem))] overflow-hidden rounded-[14px] border border-line bg-surface shadow-xl">
          <div class="flex items-center justify-between border-b border-line px-4 py-3.5">
            <span class="text-[.9rem] font-bold">🔔 Notifications</span>
            <button type="button" onclick="markAllRead()" class="cursor-pointer border-0 bg-transparent text-xs font-semibold text-primary">Mark all read</button>
          </div>
          <div id="notif-list" class="scroll-thin max-h-[380px] overflow-y-auto">
            <div class="p-8 text-center text-[.85rem] text-muted">No notifications yet</div>
          </div>
          <div class="border-t border-line px-4 py-2.5 text-center">
            <a href="#" onclick="clearAllRead();return false;" class="text-[.78rem] text-muted hover:text-fg">Clear read notifications</a>
          </div>
        </div>
      </div>

      <!-- Theme toggle -->
      <button type="button" id="theme-btn" title="Toggle theme" class="<?= ui('icon_btn') ?>" onclick="toggleTheme()">
        <span id="theme-icon" class="inline-flex"><?= icon('moon') ?></span>
      </button>

      <div class="mx-1 h-6 w-px bg-line"></div>

      <div title="<?= htmlspecialchars($faculty_user) ?>" class="flex h-9 w-9 shrink-0 cursor-pointer items-center justify-center rounded-full border-2 border-transparent bg-gradient-to-br from-indigo-500 to-blue-600 text-[.8rem] font-bold text-white transition-colors hover:border-primary">
        <?= $initials ?>
      </div>
    </div>
  </header>

  <!-- MAIN SCROLL AREA (pages render here) -->
  <main id="main-scroll" class="flex-grow overflow-y-auto bg-canvas px-4 py-5 transition-colors duration-150 sm:px-8 sm:py-7">
<!-- ══════════ PAGE CONTENT STARTS ══════════ -->
