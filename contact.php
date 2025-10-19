<?php
include("config/db.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/PHPMailer-master/src/Exception.php';
require 'phpmailer/PHPMailer-master/src/PHPMailer.php';
require 'phpmailer/PHPMailer-master/src/SMTP.php';

$property_id = $_GET['property'] ?? null;

  $sql = "SELECT title FROM pasurit WHERE property_id = " . mysqli_real_escape_string($conn, $property_id);
  $result = mysqli_query($conn, $sql);
  $property = mysqli_fetch_assoc($result);

if (!$property) {
    die("Property not found.");
  }
  
$success = false;
$error = "";

$id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $message = trim($_POST['message']);

  if ($name && $email && $message) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'genchalili06@gmail.com';
        $mail->Password = 'oklp lyls lkrd uzph';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('genchalili06@gmail.com', 'Website Contact');
        $mail->addAddress('genchalili06@gmail.com', 'Agent');
        $mail->addReplyTo($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'New Inquiry about ' . htmlspecialchars($property['title']);
        $mail->Body = "
          <h2>New Property Inquiry</h2>
          <p><strong>Property:</strong> " . htmlspecialchars($property['title']) . "</p>
          <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
          <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
          <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
        ";

        $mail->send();
            $success = true;
            if (isset($conn)) {
              $stmt = mysqli_prepare($conn, "INSERT INTO messages (name, email, message, property_id, property_title, status) VALUES (?, ?, ?, ?, ?, 'unread')");
              if ($stmt) {
                $propId = $property_id ? (int)$property_id : null;
                $propTitle = isset($property['title']) ? $property['title'] : null;
                mysqli_stmt_bind_param($stmt, 'sssis', $name, $email, $message, $propId, $propTitle);
                if (!mysqli_stmt_execute($stmt)) {
                  error_log('Failed to insert message: ' . mysqli_stmt_error($stmt));
                }
                mysqli_stmt_close($stmt);
              } else {
                error_log('Messages insert prepare failed: ' . mysqli_error($conn));
              }
            }
      } catch (Exception $e) {
        $error = "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
      }
    } else {
      $error = "Please fill in all fields.";
    } 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Agent</title>
  <link href="./style/output.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-[#16293e] text-white font-[Poppins] min-h-screen flex flex-col">
  <header class="flex justify-between items-center bg-[#1A2E45] px-6 py-4 shadow-md">
    <h1 class="text-[#F0F0F0] text-4xl font-extrabold tracking-wide">🏠 Real Estate</h1>
    <ul class="flex">
      <li class="text-white ml-3.5 font-extrabold"><a href="index.php">Home</a></li>
      <li class="text-white ml-3.5 font-extrabold"><a href="#">Listing</a></li>
      <li class="text-white ml-3.5 font-extrabold"><a href="#">Contact</a></li>
      <li class="text-white ml-3.5 font-extrabold"><a href="#">About Us</a></li>
    </ul>
  </header>

  <main class="flex-grow flex justify-center items-center p-8">
    <div class="bg-[#203853] p-8 rounded-2xl shadow-2xl w-full max-w-lg">
      <h2 class="text-2xl font-bold mb-4">Contact Agent</h2>
      <p class="text-gray-300 mb-6">Inquiring about: 
        <span class="font-semibold text-blue-400"><?= htmlspecialchars($property['title']) ?></span>
      </p>

      <?php if ($success): ?>
        <div class="bg-green-600 text-white p-3 rounded mb-4">
          ✅ Your message has been sent successfully!
        </div>
      <?php elseif ($error): ?>
        <div class="bg-red-600 text-white p-3 rounded mb-4">
          ⚠️ <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="space-y-4">
        <input type="text" name="name" placeholder="Your Name" class="w-full px-4 py-2 rounded bg-[#1e354f] placeholder-gray-300 outline-none" required>
        <input type="email" name="email" placeholder="Your Email" class="w-full px-4 py-2 rounded bg-[#1e354f] placeholder-gray-300 outline-none" required>
        <textarea name="message" rows="4" placeholder="Your Message..." class="w-full px-4 py-2 rounded bg-[#1e354f] placeholder-gray-300 outline-none" required></textarea>
        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded font-semibold transition-all duration-300">Send Message</button>
      </form>
      <div class="mt-6 text-center"><a href="details.php?id=<?= $property_id ?>" class="text-gray-400 hover:text-blue-400 transition">← Back to Property</a></div>
    </div>
  </main>
</body>
</html>
