<?php
include("config/db.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/PHPMailer-master/src/Exception.php';
require 'phpmailer/PHPMailer-master/src/PHPMailer.php';
require 'phpmailer/PHPMailer-master/src/SMTP.php';
  
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
        $mail->Body = "
          <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
          <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
          <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
        ";

        $mail->send();
        $success = true;
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
  <title>Contact Us</title>
  <link href="./style/output.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#16293e] text-white font-[Poppins] min-h-screen flex flex-col">
<?php include("includes/header.php"); ?>

<section class=" pt-36">
  <div class="container mx-auto px-6">
      <?php if ($success): ?>
        <div class="bg-green-600 text-white p-3 mt-24 rounded mb-4">
          ✅ Your message has been sent successfully!
        </div>
      <?php elseif ($error): ?>
        <div class="bg-red-600 text-white p-3 rounded mb-4">
          ⚠️ <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>
    <form action="contact_pg.php" method="POST" class="max-w-lg mx-auto bg-[#102541] p-6 rounded-lg shadow-lg">
      <h2 class="text-2xl font-bold mb-6 text-center">Contact Agent</h2>
      <input type="text" name="name" placeholder="Your Name" class="w-full p-3 mb-4 rounded bg-[#1A2E50] text-white" required>
      <input type="email" name="email" placeholder="Your Email" class="w-full p-3 mb-4 rounded bg-[#1A2E50] text-white" required>
      <textarea name="message" placeholder="Your Message" rows="5" class="w-full p-3 mb-4 rounded bg-[#1A2E50] text-white" required></textarea>
      <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 py-3 rounded font-bold">Send Message</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $message = htmlspecialchars($_POST['message']);

        echo "<p class='text-center mt-5 text-green-400'>Thank you, $name! Your message has been received.</p>";
    }
    ?>
  </div>
</section>

<?php include("includes/footer.php"); ?>
</body>
</html>
