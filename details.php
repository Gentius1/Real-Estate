<?php
include("config/db.php");

$id = $_GET['id'] ?? null;
if (!$id) {
  die("No property selected.");
}

$id = mysqli_real_escape_string($conn, $id);
$sql = "SELECT * FROM pasurit WHERE property_id = $id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
  die("Property not found.");
}

$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($row['title']) ?> - Property Details</title>
  <link href="./style/output.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-[#F5F7FA] font-[Poppins] text-white">
  <header class="flex justify-between items-center bg-[#1A2E45] px-6 py-4 shadow-md">
    <h1 class="text-[#F0F0F0] text-4xl font-extrabold tracking-wide transition-all duration-300 hover:[text-shadow:0_0_15px_rgba(59,130,246,0.8)]">🏠 Real Estate</h1>
    <ul class="flex">
      <li class="text-white ml-3.5 font-extrabold transition-all duration-300 hover:[text-shadow:0_0_50px_rgba(59,130,246,0.8)]"><a href="index.php">Home</a></li>
      <li class="text-white ml-3.5 font-extrabold transition-all duration-300 hover:[text-shadow:0_0_50px_rgba(59,130,246,0.8)]"><a href="#">Listing</a></li>
      <li class="text-white ml-3.5 font-extrabold transition-all duration-300 hover:[text-shadow:0_0_50px_rgba(59,130,246,0.8)]"><a href="#">Contact</a></li>
      <li class="text-white ml-3.5 font-extrabold transition-all duration-300 hover:[text-shadow:0_0_50px_rgba(59,130,246,0.8)]"><a href="#">About Us</a></li>
    </ul>
  </header>

  <main class="bg-[#16293e] min-h-screen p-10 flex justify-center items-start">
    <div class="bg-[#203853] w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 hover:scale-[1.01] hover:shadow-blue-500/20">
      <?php
        $images = [];
        if (!empty($row['image_url'])) {
          $decodedDetail = json_decode($row['image_url'], true);
          if (json_last_error() === JSON_ERROR_NONE && is_array($decodedDetail) && count($decodedDetail) > 0) {
            $images = array_values($decodedDetail);
          } else {
            $images = [ $row['image_url'] ];
          }
        }
        if (empty($images)) {
          $images = ['imgs/index_imgs/welcome.jfif'];
        }

        $primary = $images[0];
      ?>

      <div class="w-full">
        <img id="mainImage" src="./<?= htmlspecialchars($primary) ?>" alt="Property image" class="w-full h-[450px] object-cover rounded-t-2xl">

        <?php if (count($images) > 1): ?>
          <div class="mt-4 px-6 pb-6">
            <div class="flex gap-3 overflow-x-auto">
              <?php foreach ($images as $idx => $imgPath): ?>
                <button type="button" class="thumbnail shrink-0 rounded-lg overflow-hidden border-2 border-transparent focus:outline-none" data-src="<?= htmlspecialchars($imgPath) ?>" aria-label="Show image <?= $idx + 1 ?>">
                  <img src="./<?= htmlspecialchars($imgPath) ?>" alt="Thumb <?= $idx + 1 ?>" class="w-28 h-20 object-cover block">
                </button>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <script>
          (function(){
            const thumbnails = document.querySelectorAll('.thumbnail');
            const main = document.getElementById('mainImage');
            if (!main) return;
            thumbnails.forEach(btn => {
              btn.addEventListener('click', function(){
                const src = this.getAttribute('data-src');
                if (src) {
                  main.src = './' + src.replace(/^\.\//, '');
                  thumbnails.forEach(b => b.style.borderColor = 'transparent');
                  this.style.borderColor = '#3b82f6'; 
                }
              });
            });
            if (thumbnails.length > 0) thumbnails[0].style.borderColor = '#3b82f6';
          })();
        </script>
      </div>
      
      <div class="p-8 space-y-5">
        <div class="flex justify-between items-center">
          <h1 class="text-3xl md:text-4xl font-bold"><?= htmlspecialchars($row['title']) ?></h1>
          <span class="bg-blue-500 text-white px-4 py-2 rounded-full text-lg font-semibold">€<?= number_format($row['price'], 2) ?></span>
        </div>

        <p class="text-gray-300 leading-relaxed text-lg"><?= nl2br(htmlspecialchars($row['description'])) ?></p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
          <div class="bg-[#1e354f] p-4 rounded-xl">
            <p class="text-gray-400 text-sm mb-1 uppercase tracking-wide">City</p>
            <p class="text-xl font-semibold"><?= htmlspecialchars($row['city']) ?></p>
          </div>

          <div class="bg-[#1e354f] p-4 rounded-xl">
            <p class="text-gray-400 text-sm mb-1 uppercase tracking-wide">Property Type</p>
            <p class="text-xl font-semibold"><?= htmlspecialchars($row['property_type']) ?></p>
          </div>

          <div class="bg-[#1e354f] p-4 rounded-xl">
            <p class="text-gray-400 text-sm mb-1 uppercase tracking-wide">Status</p>
            <p class="text-xl font-semibold"><?= htmlspecialchars($row['status']) ?></p>
          </div>

          <div class="bg-[#1e354f] p-4 rounded-xl">
            <p class="text-gray-400 text-sm mb-1 uppercase tracking-wide">Listing ID</p>
            <p class="text-xl font-semibold">#<?= htmlspecialchars($row['property_id']) ?></p>
          </div>

          <div class="bg-[#1e354f] p-4 rounded-xl">
            <p class="text-gray-400 text-sm mb-1 uppercase tracking-wide">Bedrooms</p>
            <p class="text-xl font-semibold"><?= isset($row['bedrooms']) && $row['bedrooms'] !== null ? (int)$row['bedrooms'] : 'N/A' ?></p>
          </div>

          <div class="bg-[#1e354f] p-4 rounded-xl">
            <p class="text-gray-400 text-sm mb-1 uppercase tracking-wide">Bathrooms</p>
            <p class="text-xl font-semibold"><?= isset($row['bathrooms']) && $row['bathrooms'] !== null ? (int)$row['bathrooms'] : 'N/A' ?></p>
          </div>

          <div class="bg-[#1e354f] p-4 rounded-xl">
            <p class="text-gray-400 text-sm mb-1 uppercase tracking-wide">Area</p>
            <p class="text-xl font-semibold"><?= isset($row['area']) && $row['area'] !== null ? htmlspecialchars(number_format((float)$row['area'], 1)) . ' m²' : 'N/A' ?></p>
          </div>
        </div>

        <div class="flex justify-between mt-8">
          <a href="shop.php" class="bg-gray-600 hover:bg-gray-700 px-6 py-3 rounded-xl font-semibold transition-all duration-300">← Back to Listings</a>
          <a href="contact.php?property=<?= $row['property_id'] ?>" class="bg-blue-500 hover:bg-blue-600 px-6 py-3 rounded-xl font-semibold transition-all duration-300">Contact Agent</a>
        </div>
      </div>
    </div>
  </main>
</body>
</html>