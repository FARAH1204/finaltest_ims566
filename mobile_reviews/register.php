<?php
session_start();
include 'db.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    // Check if email already exists
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        $error = "Email telah digunakan. Sila cuba dengan email lain.";
    } else {
        $conn->query("INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')");
        $_SESSION['user'] = $conn->query("SELECT * FROM users WHERE email='$email'")->fetch_assoc();
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>REGISTER NEW ACCOUNT</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 450px;">
  <h3 class="mb-4">REGISTER NEW ACCOUNT</h3>
  <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
  <form method="post" class="bg-light p-4 rounded">
    <input type="text" name="name" class="form-control mb-2" placeholder="FULL NAME" required>
    <input type="email" name="email" class="form-control mb-2" placeholder="EMAIL" required>
    <input type="password" name="password" class="form-control mb-3" placeholder="PASSWORD" required>
    <button class="btn btn-success w-100">REGISTER NOW</button>
    <div class="text-center mt-3">
      <p>Already have account? <a href="login.php">Log in</a></p>
    </div>
  </form>
</div>
</body>
</html>
