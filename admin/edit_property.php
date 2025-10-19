<?php
include("../config/db.php");


$success = false;
$error = "";
$property = null;

$properties = mysqli_query($conn, "SELECT property_id, title FROM pasurit ORDER BY title ASC");

$id = $_POST['selected_id'] ?? null;

if ($id) {
  $id = (int)$id;
  $result = mysqli_query($conn, "SELECT * FROM pasurit WHERE property_id = $id");
  $property = mysqli_fetch_assoc($result);

  if (!$property) {
    $error = "Property not found!";
  }
}

if (isset($_POST['update'])) {
  $id = (int)$_POST['property_id'];
  $title = $_POST['title'];
  $city = $_POST['city'];
  $price = $_POST['price'];
  $type = $_POST['type'];
  $status = $_POST['status'];
  $description = $_POST['description'];
  $bedrooms = isset($_POST['bedrooms']) && $_POST['bedrooms'] !== '' ? (int)$_POST['bedrooms'] : null;
  $bathrooms = isset($_POST['bathrooms']) && $_POST['bathrooms'] !== '' ? (int)$_POST['bathrooms'] : null;
  $area = isset($_POST['area']) && $_POST['area'] !== '' ? (float)$_POST['area'] : null;

  $update = $conn->prepare("UPDATE pasurit SET title=?, city=?, price=?, property_type=?, status=?, description=?, bedrooms=?, bathrooms=?, area=? WHERE property_id=?");
  $update->bind_param("ssdsssiidi", $title, $city, $price, $type, $status, $description, $bedrooms, $bathrooms, $area, $id);

  if ($update->execute()) {
    $success = true;
    $result = mysqli_query($conn, "SELECT * FROM pasurit WHERE property_id = $id");
    $property = mysqli_fetch_assoc($result);
  } else {
    $error = "Failed to update property.";
  }
}

if (isset($_POST['delete'])) {
  $id = (int)$_POST['property_id'];

  $delete = $conn->prepare("DELETE FROM pasurit WHERE property_id=?");
  $delete->bind_param("i", $id);

  if ($delete->execute()) {
    $success = "Property deleted successfully!";
    $property = null;
  } else {
    $error = "Failed to delete property.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Property</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-[#16293e] text-white font-[Poppins] min-h-screen flex flex-col">

<main class="flex-grow flex items-center justify-center px-4 py-10">
  <?php include("admin_includes/side_bar.php"); ?>
  <div class="bg-[#102541] w-full max-w-2xl p-8 rounded-2xl shadow-2xl border border-[#1f3a5f]">
    <h1 class="text-3xl font-bold mb-6 text-center text-white">✏️ Edit or Delete Property</h1>

    <?php if ($success): ?>
      <div class="bg-green-600 text-white p-3 rounded mb-4"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
      <div class="bg-red-600 text-white p-3 rounded mb-4">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!$property): ?>
      <form method="POST" class="space-y-5" id="propertyForm">
        <label class="block font-semibold mb-2 text-lg text-center">Select a Property</label>

        <input type="text" id="searchBar" placeholder="Search properties..." 
               class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none mb-3">

        <select name="selected_id" id="propertySelect" 
                class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none" required>
          <option value="">-- Choose a property --</option>
          <?php while ($row = mysqli_fetch_assoc($properties)): ?>
            <option value="<?= $row['property_id'] ?>">
              <?= htmlspecialchars($row['title']) ?> (ID: <?= $row['property_id'] ?>)
            </option>
          <?php endwhile; ?>
        </select>

        <button type="submit" 
                class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 rounded font-semibold transition-all">
          ➡️ Continue
        </button>
      </form>

      <script>
        const searchBar = document.getElementById("searchBar");
        const propertySelect = document.getElementById("propertySelect");

        searchBar.addEventListener("input", () => {
          const filter = searchBar.value.toLowerCase();
          const options = propertySelect.options;
          
          for (let i = 0; i < options.length; i++) {
            const text = options[i].text.toLowerCase();
            options[i].style.display = text.includes(filter) ? "" : "none";
          }
        });
      </script>

    <?php else: ?>

    <form method="POST" class="space-y-5">
      <input type="hidden" name="property_id" value="<?= $property['property_id'] ?>">

      <div>
        <label class="block font-semibold mb-2">Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($property['title']) ?>" 
               class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none" required>
      </div>

      <div>
        <label class="block font-semibold mb-2">City</label>
        <select name="type" class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none">
          <option <?= $property['city'] === 'Gjilan' ? 'selected' : '' ?>>Gjilan</option>
          <option <?= $property['city'] === 'Prizeren' ? 'selected' : '' ?>>Prizeren</option>
          <option <?= $property['city'] === 'Mitrovic' ? 'selected' : '' ?>>Mitrovic</option>
          <option <?= $property['city'] === 'Peja' ? 'selected' : '' ?>>Peja</option>
          <option <?= $property['city'] === 'Prishtina' ? 'selected' : '' ?>>Prishtina</option>
        </select>
      </div>

      <div>
        <label class="block font-semibold mb-2">Price (€)</label>
        <input type="number" name="price" value="<?= htmlspecialchars($property['price']) ?>" 
               class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none" required>
      </div>

      <div>
        <label class="block font-semibold mb-2">Type</label>
        <select name="type" class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none">
          <option <?= $property['property_type'] === 'Residential' ? 'selected' : '' ?>>Residential</option>
          <option <?= $property['property_type'] === 'Commercial' ? 'selected' : '' ?>>Commercial</option>
          <option <?= $property['property_type'] === 'House' ? 'selected' : '' ?>>House</option>
          <option <?= $property['property_type'] === 'Villa' ? 'selected' : '' ?>>Villa</option>
        </select>
      </div>

      <div>
        <label class="block font-semibold mb-2">Status</label>
        <select name="status" class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none">
          <option <?= $property['status'] === 'Available' ? 'selected' : '' ?>>Available</option>
          <option <?= $property['status'] === 'Sold' ? 'selected' : '' ?>>Sold</option>
          <option <?= $property['status'] === 'Rented' ? 'selected' : '' ?>>Rented</option>
        </select>
      </div>

      <div>
        <label class="block font-semibold mb-2">Description</label>
        <textarea name="description" rows="4" 
                  class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none"><?= htmlspecialchars($property['description']) ?></textarea>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
          <label class="block font-semibold mb-2">Bedrooms</label>
          <input type="number" name="bedrooms" min="0" step="1" value="<?= isset($property['bedrooms']) ? (int)$property['bedrooms'] : '' ?>" class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none" placeholder="e.g. 3">
        </div>
        <div>
          <label class="block font-semibold mb-2">Bathrooms</label>
          <input type="number" name="bathrooms" min="0" step="1" value="<?= isset($property['bathrooms']) ? (int)$property['bathrooms'] : '' ?>" class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none" placeholder="e.g. 2">
        </div>
        <div>
          <label class="block font-semibold mb-2">Area (m²)</label>
          <input type="number" name="area" min="0" step="0.1" value="<?= isset($property['area']) ? htmlspecialchars(number_format((float)$property['area'],1)) : '' ?>" class="w-full bg-[#1A2E50] p-3 rounded text-white outline-none" placeholder="e.g. 120.5">
        </div>
      </div>

      <div class="flex flex-col md:flex-row gap-3">
        <button type="submit" name="update"
                class="flex-1 bg-green-500 hover:bg-green-600 text-white py-3 rounded font-semibold transition-all">
          💾 Save Changes
        </button>

        <button type="submit" name="delete" id="deleteBtn"
          class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded font-semibold transition-all">
          🗑 Delete Property
        </button>

        <a href="edit_property.php" 
           class="flex-1 text-center bg-gray-500 hover:bg-gray-600 text-white py-3 rounded font-semibold transition-all">
          🔙 Back to Selection
        </a>
      </div>
    </form>
    <?php endif; ?>
  </div>
</main>
</body>
</html>

<script>
document.getElementById('deleteBtn')?.addEventListener('click', function(e) {
  e.preventDefault();
  if (!confirm('Are you sure you want to delete this property?')) return;
  if (!confirm('This action is permanent. Do you really want to delete it?')) return;
  this.closest('form').submit();
});
</script>
