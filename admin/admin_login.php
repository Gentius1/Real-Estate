<?php
session_start();

$admin_username = "admin";
$admin_password = "1234"; 

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION["admin_logged_in"] = true;
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-[#16293e] text-white font-[Poppins] flex justify-center items-center min-h-screen">     

  <div class="bg-[#102541] p-8 rounded-2xl shadow-2xl w-full max-w-md">
    <a href="../index.php" class="inline-flex items-center text-blue-400 hover:text-blue-500 font-semibold mb-6 transition">← Back to Home</a>

    <h1 class="text-3xl font-bold mb-6 text-center text-white">🔐 Admin Login</h1>

    <?php if ($error): ?>
      <div class="bg-red-600 text-white p-3 rounded mb-4 text-center">
        ⚠️ <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="flex flex-col space-y-4">
      <div>
        <label for="username" class="block mb-2 text-sm font-semibold">Username</label>
        <input type="text" id="username" name="username" required
               class="w-full p-3 bg-[#1A2E50] text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
      </div>

      <div>
        <label for="password" class="block mb-2 text-sm font-semibold">Password</label>
        <input type="password" id="password" name="password" required
               class="w-full p-3 bg-[#1A2E50] text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
      </div>

      <button type="submit"
              class="w-full bg-blue-500 hover:bg-blue-600 py-3 rounded-lg font-bold text-white transition-all shadow-md">
        Login
      </button>
    </form>

    <p class="text-center text-sm mt-6 text-gray-400">
      © <?= date('Y') ?> Real Estate Admin Panel
    </p>
  </div>

</body>
</html>
