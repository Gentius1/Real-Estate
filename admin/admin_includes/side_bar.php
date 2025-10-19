<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['admin'])) {
  $_SESSION['admin'] = "Admin";
}

if (isset($_GET['logout'])) {
  session_destroy();
  header("Location: admin_login.php");
  exit;
}
?>

<button id="sidebarToggle" aria-controls="adminSidebar" aria-expanded="false" class="md:hidden fixed top-4 left-4 z-60 bg-blue-600 text-white p-2 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
</button>

<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-10 hidden transition-opacity duration-300"></div>

<aside id="adminSidebar" class="fixed top-0 left-0 h-full w-64 bg-[#102541] border-r border-[#1f3a5f] flex flex-col justify-between transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-30" aria-hidden="true">
    <div>
      <div class="p-6 text-center border-b border-[#1f3a5f]">
        <h1 class="text-2xl font-bold text-blue-400">🏠 Admin Panel</h1>
        <p class="text-sm text-gray-400 mt-1">Welcome, <?= htmlspecialchars($_SESSION['admin']) ?></p>
      </div>
      <nav class="flex flex-col mt-4 space-y-2 px-4">
        <a href="admin_dashboard.php" class="px-4 py-2 rounded hover:bg-[#1a3b63] transition <?= basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'text-blue-400 underline' : '' ?>">📊 Dashboard</a>
        <a href="add_property.php" class="px-4 py-2 rounded hover:bg-[#1a3b63] transition <?= basename($_SERVER['PHP_SELF']) == 'add_property.php' ? 'text-blue-400 underline' : '' ?>">➕ Add Property</a>
        <a href="edit_property.php" class="px-4 py-2 rounded hover:bg-[#1a3b63] transition <?= basename($_SERVER['PHP_SELF']) == 'edit_property.php' ? 'text-blue-400 underline' : '' ?>">✏️ Edit Property</a>
        <?php
          $unreadCount = 0;
          if (isset($conn)) {
            $res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM messages WHERE status = 'unread'");
            if ($res) {
              $unreadCount = (int)mysqli_fetch_assoc($res)['c'];
            }
          }
        ?>
        <a href="admin_dashboard.php#messages" class="px-4 py-2 rounded hover:bg-[#1a3b63] transition <?= basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'text-blue-400 underline' : '' ?>">✉️ Messages <?php if ($unreadCount > 0): ?><span class="ml-2 inline-flex items-center justify-center bg-red-600 text-white text-xs font-semibold px-2 py-0.5 rounded"><?= $unreadCount ?></span><?php endif; ?></a>
      </nav>
    </div>

    <div class="p-4 border-t border-[#1f3a5f]">
      <a href="?logout=true" 
         class="w-full inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded font-semibold transition">
        🚪 Logout
      </a>
    </div>
  </aside>

  <script>
    (function(){
      const toggle = document.getElementById('sidebarToggle');
      const sidebar = document.getElementById('adminSidebar');
      const overlay = document.getElementById('sidebarOverlay');

      function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebar.setAttribute('aria-hidden', 'false');
        toggle.setAttribute('aria-expanded', 'true');
        overlay.classList.remove('hidden');
        setTimeout(()=> overlay.classList.add('opacity-100'), 10);
        document.body.style.overflow = 'hidden';
      }

      function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        sidebar.setAttribute('aria-hidden', 'true');
        toggle.setAttribute('aria-expanded', 'false');
        overlay.classList.remove('opacity-100');
        setTimeout(()=> overlay.classList.add('hidden'), 200);
        document.body.style.overflow = '';
      }

      toggle.addEventListener('click', function(){
        const expanded = this.getAttribute('aria-expanded') === 'true';
        if (expanded) closeSidebar(); else openSidebar();
      });

      overlay.addEventListener('click', closeSidebar);

      document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') closeSidebar();
      });

      document.querySelectorAll('#adminSidebar nav a').forEach(a => {
        a.addEventListener('click', function(){
          if (window.innerWidth < 768) {
            closeSidebar();
          }
        });
      });

      window.addEventListener('resize', function(){
        if (window.innerWidth >= 768) {
          sidebar.classList.remove('-translate-x-full');
          sidebar.setAttribute('aria-hidden', 'false');
          overlay.classList.add('hidden');
          overlay.classList.remove('opacity-100');
          document.body.style.overflow = '';
          toggle.setAttribute('aria-expanded', 'true');
        } else {
          sidebar.classList.add('-translate-x-full');
          sidebar.setAttribute('aria-hidden', 'true');
          toggle.setAttribute('aria-expanded', 'false');
        }
      });

      if (window.innerWidth >= 768) {
        sidebar.classList.remove('-translate-x-full');
        sidebar.setAttribute('aria-hidden', 'false');
        toggle.setAttribute('aria-expanded', 'true');
      } else {
        sidebar.classList.add('-translate-x-full');
        sidebar.setAttribute('aria-hidden', 'true');
        toggle.setAttribute('aria-expanded', 'false');
      }
    })();
  </script>