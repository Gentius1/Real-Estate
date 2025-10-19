<?php
session_start();

include("../config/db.php");

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM pasurit"))['count'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read_id'])) {
  $mid = (int)$_POST['mark_read_id'];
  mysqli_query($conn, "UPDATE messages SET status = 'read' WHERE id = $mid");
}

$messages = [];
$msgRes = mysqli_query($conn, "SELECT * FROM messages ORDER BY created_at DESC LIMIT 10");
if ($msgRes) {
  while ($m = mysqli_fetch_assoc($msgRes)) $messages[] = $m;
}
$messageCount = 0;
$countRes = @mysqli_query($conn, "SELECT COUNT(*) AS c FROM messages");
if ($countRes) {
  $rowc = mysqli_fetch_assoc($countRes);
  $messageCount = isset($rowc['c']) ? (int)$rowc['c'] : 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-[#16293e] text-white font-[Poppins] h-screen flex">
  <?php include("admin_includes/side_bar.php"); ?>

  <main class="md:ml-64 ml-0 flex-1 p-6 md:p-10 overflow-y-auto">
    <h2 class="text-3xl font-bold mb-6">📊 Dashboard Overview</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-[#203853] rounded-xl p-6 shadow-lg text-center hover:scale-[1.03] transition">
        <h3 class="text-lg font-semibold mb-2 text-blue-400">Total Properties</h3>
        <p class="text-3xl font-bold"><?= $total ?></p>
      </div>

      <div class="bg-[#203853] rounded-xl p-6 shadow-lg text-center hover:scale-[1.03] transition">
        <h3 class="text-lg font-semibold mb-2 text-blue-400">Pending Reviews</h3>
        <p class="text-3xl font-bold">0</p>
      </div>

      <div class="bg-[#203853] rounded-xl p-6 shadow-lg text-center hover:scale-[1.03] transition">
        <h3 class="text-lg font-semibold mb-2 text-blue-400">Messages</h3>
        <p class="text-3xl font-bold"><?= $messageCount ?></p>
      </div>
    </div>

    <div class="mt-10">
      <h3 class="text-2xl font-bold mb-4">Recent Actions</h3>
      <div class="bg-[#1A2E50] rounded-xl p-6 shadow-lg">
        <ul class="space-y-3">
          <li>These are place holders</li>
          <li>✅ Property "Modern Villa" updated</li>
          <li>🗑 Property "Old House" deleted</li>
          <li>➕ New property "City Apartment" added</li>
        </ul>
      </div>
    </div>

    <div class="mt-10" id="messages">
      <h3 class="text-2xl font-bold mb-4">Recent Messages</h3>
      <div class="bg-[#1A2E50] rounded-xl p-6 shadow-lg space-y-4">
        <?php if (count($messages) === 0): ?>
          <p class="text-gray-300">No messages yet.</p>
        <?php else: ?>
          <?php foreach ($messages as $m): ?>
            <div class="bg-[#15283f] p-4 rounded-lg flex justify-between items-start">
              <div>
                <div class="text-sm text-gray-400"><?= htmlspecialchars($m['created_at']) ?> <?php if ($m['status'] === 'unread'): ?><span class="ml-2 inline-block bg-red-600 text-white text-xs px-2 py-0.5 rounded">UNREAD</span><?php endif; ?></div>
                <div class="font-semibold text-white"><?= htmlspecialchars($m['name']) ?> <span class="text-gray-300 text-sm">(<?= htmlspecialchars($m['email']) ?>)</span></div>
                <div class="text-gray-300 mt-2"><?= nl2br(htmlspecialchars($m['message'])) ?></div>
                <?php if (!empty($m['property_title'])): ?>
                  <div class="text-gray-400 text-sm mt-2">Regarding: <?= htmlspecialchars($m['property_title']) ?></div>
                <?php endif; ?>
              </div>
              <div class="ml-4 flex flex-col gap-2">
                <?php if ($m['status'] === 'unread'): ?>
                  <form method="POST">
                    <input type="hidden" name="mark_read_id" value="<?= $m['id'] ?>">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded">Mark read</button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </main>
</body>
</html>
