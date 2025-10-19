<header class="fixed top-0 left-0 w-full z-30 col-span-2 flex justify-between items-center bg-[#1A2E45] px-6 py-4 shadow-md text-sm sm:text-base md:text-lg">
    <h1 class="text-[#F0F0F0] text-4xl font-extrabold tracking-wide transition-all duration-300 hover:[text-shadow:0_0_15px_rgba(59,130,246,0.8)]">🏠 Real Estate</h1>
    <ul class="flex">
        <li class="text-white ml-3.5 font-extrabold transition-all duration-300 hover:[text-shadow:0_0_50px_rgba(59,130,246,0.8)]"><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-blue-400 underline' : '' ?>">Home</a></li>
        <li class="text-white ml-3.5 font-extrabold transition-all duration-300 hover:[text-shadow:0_0_50px_rgba(59,130,246,0.8)]"><a href="shop.php" class="<?= basename($_SERVER['PHP_SELF']) == 'shop.php' ? 'text-blue-400 underline' : '' ?>">Shop</a></li>
        <li class="text-white ml-3.5 font-extrabold transition-all duration-300 hover:[text-shadow:0_0_50px_rgba(59,130,246,0.8)]"><a href="contact_pg.php" class="<?= basename($_SERVER['PHP_SELF']) == 'contact_pg.php' ? 'text-blue-400 underline' : '' ?>">Contact</a></li>
        <li class="text-white ml-3.5 font-extrabold transition-all duration-300 hover:[text-shadow:0_0_50px_rgba(59,130,246,0.8)]"><a href="about_us.php" class="<?= basename($_SERVER['PHP_SELF']) == 'about_us.php' ? 'text-blue-400 underline' : '' ?>">About Us</a></li>
        <li class="text-white ml-3.5 font-extrabold transition-all duration-300 hover:[text-shadow:0_0_50px_rgba(59,130,246,0.8)]"><a href="./admin/admin_login.php" class="<?= basename($_SERVER['PHP_SELF']) == './admin/admin_login.php' ? 'text-blue-400 underline' : '' ?>">Admin</a></li>
    </ul>
</header>