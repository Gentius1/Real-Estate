<?php
include("../config/db.php");

$success = false;
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $city = $_POST['city'];
  $price = $_POST['price'];
  $type = $_POST['type'];
  $status = $_POST['status'];
  $description = $_POST['description'];
  $bedrooms = isset($_POST['bedrooms']) && $_POST['bedrooms'] !== '' ? (int)$_POST['bedrooms'] : null;
  $bathrooms = isset($_POST['bathrooms']) && $_POST['bathrooms'] !== '' ? (int)$_POST['bathrooms'] : null;
  $area = isset($_POST['area']) && $_POST['area'] !== '' ? (float)$_POST['area'] : null;

  $imageNames = [];
  $targetDir = "../uploads/";
  $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

  if (!empty($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
    foreach ($_FILES['images']['name'] as $idx => $origName) {
      if (empty($origName)) continue;
      $tmpName = $_FILES['images']['tmp_name'][$idx] ?? null;
      if (!$tmpName) continue;

      $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
      if (!in_array($ext, $allowedTypes)) continue;

      $safeName = time() . '_' . uniqid() . '.' . $ext;
      $targetFilePath = $targetDir . $safeName;

      if (move_uploaded_file($tmpName, $targetFilePath)) {
        $imageNames[] = 'uploads/' . $safeName;
      }
    }
  }

  $imagePaths = json_encode($imageNames);

  $stmt = $conn->prepare("INSERT INTO pasurit (title, city, price, property_type, status, description, image_url, bedrooms, bathrooms, area) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssdssssiid", $title, $city, $price, $type, $status, $description, $imagePaths, $bedrooms, $bathrooms, $area);

  if ($stmt->execute()) {
    $success = true;
  } else {
    $error = "Failed to add property. Please try again.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Property</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-[#16293e] text-white font-[Poppins] min-h-screen flex flex-col">
<?php include("admin_includes/side_bar.php"); ?>

<main class="flex-grow flex items-center justify-center px-4 py-10">
  <div class="bg-[#102541] w-full max-w-2xl p-8 rounded-2xl shadow-2xl border border-[#1f3a5f]">

    <h1 class="text-3xl font-bold mb-6 text-center text-white">🏡 Add New Property</h1>

    <?php if ($success): ?>
      <div class="bg-green-600 text-white p-3 rounded mb-4 text-center font-semibold">
        ✅ Property added successfully!
      </div>
    <?php elseif ($error): ?>
      <div class="bg-red-600 text-white p-3 rounded mb-4 text-center font-semibold">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form action="add_property.php" method="POST" enctype="multipart/form-data" class="space-y-5">
      <div>
        <label class="block font-semibold mb-2">Title</label>
        <input type="text" name="title" class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none" required>
      </div>

      <div>
        <label class="block font-semibold mb-2">City</label>
        <input type="text" name="city" class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none" required>
      </div>

      <div>
        <label class="block font-semibold mb-2">Price (€)</label>
        <input type="number" name="price" class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none" required>
      </div>

      <div class="grid grid-cols-3 gap-4">
        <div>
          <label class="block font-semibold mb-2">Bedrooms</label>
          <input type="number" name="bedrooms" min="0" class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none">
        </div>
        <div>
          <label class="block font-semibold mb-2">Bathrooms</label>
          <input type="number" name="bathrooms" min="0" class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none">
        </div>
        <div>
          <label class="block font-semibold mb-2">Area (m²)</label>
          <input type="number" name="area" min="0" step="0.1" class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none">
        </div>
      </div>

      <div>
        <label class="block font-semibold mb-2">Type</label>
        <select name="type" class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none">
          <option>Residential</option>
          <option>Commercial</option>
          <option>House</option>
          <option>Villa</option>
        </select>
      </div>

      <div>
        <label class="block font-semibold mb-2">Status</label>
        <select name="status" class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none">
          <option>Available</option>
          <option>Sold</option>
          <option>Rented</option>
        </select>
      </div>

      <div>
        <label class="block font-semibold mb-2">Description</label>
        <textarea name="description" rows="4" class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none"></textarea>
      </div>

      <div>
        <label class="block font-semibold mb-2">Property Images</label>
        <input type="file" name="images[]" accept="image/*" multiple id="imageInput"
              class="w-full bg-[#1A2E50] p-2 rounded text-white file:mr-3 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">

        <div id="imagePreview" class="mt-4 grid grid-cols-2 gap-3"></div>
      </div>

      <button type="submit" 
              class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 rounded font-semibold transition-all">
        ➕ Add Property
      </button>
    </form>
  </div>
</main>

<script>
const imageInput = document.getElementById('imageInput');
const imagePreview = document.getElementById('imagePreview');

imageInput.addEventListener('change', function() {
  imagePreview.innerHTML = ''; 
  const files = Array.from(this.files);
  if (files.length > 0) {
    files.forEach(file => {
      const reader = new FileReader();
      reader.onload = function(e) {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.classList.add('rounded-xl', 'w-full', 'h-40', 'object-cover', 'border', 'border-gray-600');
        imagePreview.appendChild(img);
      };
      reader.readAsDataURL(file);
    });
  }
});
</script>
</body>
</html>
