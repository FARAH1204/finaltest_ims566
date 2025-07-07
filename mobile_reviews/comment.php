<?php
include 'db.php';
$app_id = $_GET['app_id'] ?? 0;

// Add comment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $comment = $_POST['comment'];
    $rating = $_POST['rating'];
    $conn->query("INSERT INTO comments (application_id, name, comment, rating) 
                  VALUES ('$app_id', '$name', '$comment', '$rating')");
}

$comments = $conn->query("SELECT * FROM comments WHERE application_id=$app_id");
$app = $conn->query("SELECT * FROM applications WHERE id=$app_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Comments</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>Comments for: <?= $app['title'] ?></h2>
  <form method="post" class="bg-light p-3 mb-4 rounded">
    <input name="name" class="form-control mb-2" placeholder="Your Name" required>
    <textarea name="comment" class="form-control mb-2" placeholder="Your Comment" required></textarea>
    <select name="rating" class="form-select mb-2">
      <option value="5">⭐⭐⭐⭐⭐</option>
      <option value="4">⭐⭐⭐⭐</option>
      <option value="3">⭐⭐⭐</option>
      <option value="2">⭐⭐</option>
      <option value="1">⭐</option>
    </select>
    <button class="btn btn-primary">Post Comment</button>
  </form>

  <h4>All Comments</h4>
  <?php while ($row = $comments->fetch_assoc()): ?>
    <div class="card mb-2">
      <div class="card-body">
        <strong><?= $row['name'] ?></strong> - <?= str_repeat("⭐", $row['rating']) ?><br>
        <?= $row['comment'] ?><br>
        <small class="text-muted"><?= date('d M Y, h:i A', strtotime($row['created'])) ?></small>
      </div>
    </div>
  <?php endwhile; ?>
  <a href="index.php" class="btn btn-secondary mt-3">Back</a>
</div>
</body>
</html>
