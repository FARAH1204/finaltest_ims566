<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $result = $conn->query("SELECT * FROM users WHERE email='$email' AND password='$password'");
    if ($result->num_rows > 0) {
        $_SESSION['user'] = $result->fetch_assoc();
        header("Location: index.php");
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 400px;">
  <h3 class="mb-4">🔐 Login</h3>
  <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
  <form method="post" class="bg-light p-4 rounded">
    <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
    <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
    <button class="btn btn-primary w-100">Login</button>
  </form>
<div class="text-center mt-3">
  <p>No account yet?<a href="register.php">Register here</a></p>
</div>

</div>
</body>
</html>
