<!-- ══════════ PAGE CONTENT ENDS ══════════ -->
  </main>
</div><!-- /page container -->
</div><!-- /app shell -->

<!-- Toasts -->
<div id="toast-container" class="fixed bottom-6 right-6 z-[99999] flex flex-col gap-2.5"></div>

<script>
  // Values the static JS needs from PHP.
  window.APP = {
    notifApi: '/api/notifications.php',
    icons: { sun: `<?= icon('sun') ?>`, moon: `<?= icon('moon') ?>` },
  };
</script>
<script src="/assets/js/app.js"></script>
</body>
</html>
