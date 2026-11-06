/* Light/dark toggle. The initial class is set by an inline script in partials/head.blade.php. */
document.getElementById('theme-btn')?.addEventListener('click', () => {
    const dark = document.documentElement.classList.toggle('dark');
    try { localStorage.setItem('theme', dark ? 'dark' : 'light'); } catch (e) { /* storage unavailable */ }
});
