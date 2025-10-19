<?php
include("config/db.php"); 

$city = $_GET['city'] ?? 'all';
$type = $_GET['property_type'] ?? 'all'; 
$price = $_GET['price'] ?? 'all';
$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM pasurit WHERE 1=1";

if ($city !== 'all') $sql .= " AND city = '" . mysqli_real_escape_string($conn, $city) . "'";
if ($type !== 'all') $sql .= " AND property_type = '" . mysqli_real_escape_string($conn, $type) . "'";
if (!empty($search)) {
  $safeSearch = mysqli_real_escape_string($conn, $search);
  $sql .= " AND (title LIKE '%$safeSearch%' OR description LIKE '%$safeSearch%')";
}
if ($price === 'under100k') $sql .= " AND price < 100000";
elseif ($price === '100k-200k') $sql .= " AND price BETWEEN 100000 AND 200000";
elseif ($price === 'over200k') $sql .= " AND price > 200000";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Real Estate</title>
  <link href="./style/output.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-[#F5F7FA] font-[Poppins] ">
  <div class="min-h-screen grid grid-rows-[auto_1fr]">
    <?php include("includes/header.php"); ?>
    <button id="filterBtn" class="md:hidden fixed top-32 left-4 z-40 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 transition-all duration-300 hover:bg-blue-700 active:scale-95"><span>☰</span> Filters</button>
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-60 hidden z-40 transition-opacity duration-300"></div>

    <div class="flex h-full">
      <form id="filterPanel" method="GET" class="bg-[#213b58] text-[#F0F0F0] text-sm sm:text-base md:text-lg p-6 border-r border-[#2f4c70] space-y-5 w-72 md:w-72 fixed md:sticky top-0 left-0 h-full md:h-[calc(107vh-4rem)] transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-50 md:z-0 rounded-r-2xl md:rounded-none shadow-2xl md:shadow-none">
        <button type="button" id="closeFilter" class="md:hidden absolute top-4 right-4 text-white text-2xl hover:text-gray-300">&times;</button>
        <h2 class="text-2xl font-bold mb-4 border-b border-blue-400 pb-2">Filters</h2>
        <div>
          <label class="block mb-2 pt-8 font-semibold">Search:</label>
          <input type="text" name="search" placeholder="Search properties..." 
            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
            class="bg-[#1e354f] placeholder-gray-300 px-3 py-2 outline-none w-full rounded-lg focus:ring-2 focus:ring-blue-500 shadow-inner">
        </div>

        <div>
          <label class="block mb-2 font-semibold">City:</label>
          <select name="city" class="text-black w-full p-2 rounded focus:ring-2 focus:ring-blue-500">
            <option value="all"<?= $city === 'all' ? 'selected':''?>>All</option>
            <option value="Gjilan"<?= $city === 'Gjilan' ? 'selected':''?>>Gjilan</option>
            <option value="Prishtina"<?= $city === 'Prishtina' ? 'selected':''?>>Prishtina</option>
            <option value="Prizeren"<?= $city === 'Prizeren' ? 'selected':''?>>Prizeren</option>
            <option value="Mitrovic"<?= $city === 'Mitrovic' ? 'selected':''?>>Mitrovic</option>
            <option value="Peja"<?= $city === 'Peja' ? 'selected':''?>>Peja</option>
          </select>
        </div>

        <div>
          <label class="block mb-2 font-semibold">Type:</label>
          <select name="property_type" class="text-black w-full p-2 rounded focus:ring-2 focus:ring-blue-500">
            <option value="all"<?= $type === 'all' ? 'selected':''?>>All</option>
            <option value="Residential"<?= $type === 'Residential' ? 'selected':''?>>Residential</option>
            <option value="Commercial"<?= $type === 'Commercial' ? 'selected':''?>>Commercial</option>
            <option value="House"<?= $type === 'House' ? 'selected':''?>>House</option>
            <option value="Villa"<?= $type === 'Villa' ? 'selected':''?>>Villa</option>
          </select>
        </div>

        <div>
          <label class="block mb-2 font-semibold">Price Range:</label>
          <select name="price" class="text-black w-full p-2 rounded focus:ring-2 focus:ring-blue-500">
            <option value="all"<?= $price === 'all' ? 'selected':''?>>All prices</option>
            <option value="under100k"<?= $price === 'under100k' ? 'selected':''?>>Under €100,000</option>
            <option value="100k-200k"<?= $price === '100k-200k' ? 'selected':''?>>€100,000 - €200,000</option>
            <option value="over200k"<?= $price === 'over200k' ? 'selected':''?>>Over €200,000</option>
          </select>
        </div>

        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg w-full shadow-lg transition-all duration-300 active:scale-95">Apply Filters</button>
      </form>

      <main class="flex-1 bg-[#16293e] text-white p-8 overflow-y-auto md:ml-0">
        <div class="md:pt-20 pt-40 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 items-stretch">
          <?php $resultCount = mysqli_num_rows($result); ?>
          <?php if ($resultCount > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
              <?php
                $imageSrc = '';
                if (!empty($row['image_url'])) {
                  $decoded = json_decode($row['image_url'], true);
                  if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && count($decoded) > 0) {
                    $imageSrc = $decoded[0];
                  } else {
                    $imageSrc = $row['image_url'];
                  }
                }
                if (empty($imageSrc)) {
                  $imageSrc = 'imgs/index_imgs/welcome.jfif';
                }
              ?>
              <div class="bg-[#203853] hover:shadow-2xl hover:scale-[1.03] transition-all duration-300 rounded-2xl h-full flex flex-col">
                <img src="./<?= htmlspecialchars($imageSrc) ?>" alt="Property image" class="rounded-t-xl w-full h-48 object-cover">
                <div class="p-4 flex justify-between flex-col flex-1">
                  <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($row['title']) ?></h3>
                  <p class="text-gray-200"><?= htmlspecialchars($row['description']) ?></p>
                  <div class="mt-3">
                    <p class="text-gray-300 mb-1">🏙 <?= htmlspecialchars($row['city']) ?> | <?= htmlspecialchars($row['property_type']) ?></p>
                    <p class="text-gray-300 mb-1">💰 €<?= number_format($row['price'], 2) ?></p>
                    <p class="text-gray-300 mb-1">🛏 <?= isset($row['bedrooms']) && $row['bedrooms'] !== null ? (int)$row['bedrooms'] : 'N/A' ?> &nbsp; 🛁 <?= isset($row['bathrooms']) && $row['bathrooms'] !== null ? (int)$row['bathrooms'] : 'N/A' ?> &nbsp; 📐 <?= isset($row['area']) && $row['area'] !== null ? htmlspecialchars(number_format((float)$row['area'], 1)) . ' m²' : 'N/A' ?></p>
                    <p class="text-gray-400 italic"><?= htmlspecialchars($row['status']) ?></p>
                  </div>
                  <a href="details.php?id=<?= $row['property_id'] ?>" class="mt-auto bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded inline-block text-center">View Details</a>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="col-span-1 md:col-span-2 xl:col-span-3">
              <div class="bg-[#203853] rounded-2xl p-8 text-center">
                <h2 class="text-2xl font-bold mb-3">Sorry — no matches found</h2>
                <?php if (!empty($search)): ?>
                  <p class="text-gray-300 mb-4">We couldn't find any properties matching "<span class="font-semibold"><?= htmlspecialchars($search) ?></span>".</p>
                <?php else: ?>
                  <p class="text-gray-300 mb-4">No properties match the selected filters.</p>
                <?php endif; ?>
                <div class="flex flex-col md:flex-row items-center justify-center gap-3">
                  <a href="shop.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">View all listings</a>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </div>
  <script>
    const filterBtn = document.getElementById('filterBtn');
    const filterPanel = document.getElementById('filterPanel');
    const closeFilter = document.getElementById('closeFilter');
    const overlay = document.getElementById('overlay');

    function openFilter() {
      filterPanel.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
      document.body.style.overflow = 'hidden'; 
    }

    function closeFilterPanel() {
      filterPanel.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
      document.body.style.overflow = ''; 
    }

    filterBtn.addEventListener('click', openFilter);
    closeFilter.addEventListener('click', closeFilterPanel);
    overlay.addEventListener('click', closeFilterPanel);
  </script>
</body>
</html>