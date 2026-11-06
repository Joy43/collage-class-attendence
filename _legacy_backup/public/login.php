<?php
session_start();

// Include database connection
require_once dirname(__DIR__) . "/src/config/database.php";

$error = "";

// Check if form is submitted
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to check credentials
    $sql = "SELECT * FROM faculty_details WHERE user_name='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        // Login successful
        $_SESSION['faculty'] = $username;
        header("Location: /dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php $page_title = 'Faculty Login — AttApp'; require dirname(__DIR__) . "/src/views/partials/head.php"; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-900 to-indigo-950 p-5 font-sans text-slate-50">

<div class="flex w-full max-w-[420px] flex-col rounded-2xl border border-white/10 bg-white/[.03] p-10 shadow-[0_20px_25px_-5px_rgb(0_0_0/.3),0_8px_10px_-6px_rgb(0_0_0/.3)] backdrop-blur-xl">
    <div class="mb-8 text-center">
        <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-2xl font-bold text-white shadow-[0_4px_10px_rgba(79,70,229,.4)]">A</div>
        <h2 class="mb-1.5 text-2xl font-bold tracking-tight">Faculty Portal</h2>
        <p class="text-[.9rem] text-slate-400">Sign in to manage student attendance</p>
    </div>

    <?php if ($error): ?>
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-red-500/20 bg-red-500/10 px-3.5 py-2.5 text-[.85rem] font-medium text-red-300">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-5 flex flex-col gap-2">
            <label for="username" class="text-[.8rem] font-semibold uppercase tracking-wide text-slate-400">Username</label>
            <div class="relative flex items-center">
                <i class="fa-solid fa-user absolute left-3.5 text-slate-500"></i>
                <input type="text" id="username" name="username" placeholder="Enter username" required autocomplete="off"
                    class="w-full rounded-lg border border-white/10 bg-white/[.02] py-3 pl-[42px] pr-3.5 text-[.95rem] text-white outline-none transition focus:border-indigo-600 focus:bg-white/5 focus:ring-[3px] focus:ring-indigo-600/25">
            </div>
        </div>

        <div class="mb-7 flex flex-col gap-2">
            <label for="password" class="text-[.8rem] font-semibold uppercase tracking-wide text-slate-400">Password</label>
            <div class="relative flex items-center">
                <i class="fa-solid fa-lock absolute left-3.5 text-slate-500"></i>
                <input type="password" id="password" name="password" placeholder="Enter password" required
                    class="w-full rounded-lg border border-white/10 bg-white/[.02] py-3 pl-[42px] pr-3.5 text-[.95rem] text-white outline-none transition focus:border-indigo-600 focus:bg-white/5 focus:ring-[3px] focus:ring-indigo-600/25">
            </div>
        </div>

        <button type="submit" name="login"
            class="w-full cursor-pointer rounded-lg bg-indigo-600 p-3 text-[.95rem] font-semibold text-white shadow-[0_4px_6px_-1px_rgba(79,70,229,.2)] transition hover:bg-indigo-700 active:scale-[.98]">
            <i class="fa-solid fa-arrow-right-to-bracket mr-1.5"></i> Sign In
        </button>
    </form>
</div>

</body>
</html>
