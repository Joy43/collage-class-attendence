<?php
/**
 * Shared <head> contents: Tailwind (Play CDN), theme tokens, font, no-flash theme.
 * Expects $page_title (string) to be set by the including layout/page.
 */
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title ?? 'AttApp — Student Attendance Management') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
<script>
  // Apply saved theme before first paint (default: dark) to avoid a flash.
  try { if (localStorage.getItem('theme') !== 'light') document.documentElement.classList.add('dark'); } catch (e) {}
</script>
<script src="https://cdn.tailwindcss.com/3.4.17"></script>
<script src="/assets/js/tailwind.config.js"></script>
<link rel="stylesheet" href="/assets/css/app.css">
