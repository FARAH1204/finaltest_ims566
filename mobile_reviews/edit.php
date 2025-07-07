<?php
include 'db.php';
$id = $_GET['id'];
$app = $conn->query("SELECT * FROM applications WHERE id=$id")->fetch_assoc();
$cats = $conn->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['category'];
    $author = $_POST['author'];
    $title = $_POST['title'];
    $review = $_POST['review'];
    $status = $_POST['status'];
    
    $image = $app['image'];
    if ($_FILES['image']['name']) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/$image");
    }

    $conn->query("UPDATE applications SET 
        category_id='$category', author='$author', title='$title', 
        review='$review', image='$image', image_dir='uploads/$image', status='$status' 
        WHERE id=$id");

    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Review</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>Edit Review</h2>
  <form method="post" enctype="multipart/form-data" class="bg-light p-4 rounded shadow-sm">
    <div class="mb-3">
      <label>Title</label>
      <input type="text" name="title" value="<?= $app['title'] ?>" class="form-control">
    </div>
    <div class="mb-3">
      <label>Author</label>
      <input type="text" name="author" value="<?= $app['author'] ?>" class="form-control">
    </div>
    <div class="mb-3">
      <label>Review</label>
      <textarea name="review" class="form-control"><?= $app['review'] ?></textarea>
    </div>
    <div class="mb-3">
      <label>Category</label>
      <select name="category" class="form-select">
        <?php while($cat = $cats->fetch_assoc()): ?>
        <option value="<?= $cat['id'] ?>" <?= $app['category_id'] == $cat['id'] ? 'selected' : '' ?>>
            <?= $cat['title'] ?>
        </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="mb-3">
      <label>Image</label><br>
      <img src="uploads/<?= $app['image'] ?>" width="60" class="mb-2"><br>
      <input type="file" name="image" class="form-control">
    </div>
    <div class="mb-3">
      <label>Status</label>
      <select name="status" class="form-select">
        <option value="active" <?= $app['status']=='active' ? 'selected' : '' ?>>Active</option>
        <option value="inactive" <?= $app['status']=='inactive' ? 'selected' : '' ?>>Inactive</option>
      </select>
    </div>
    <button type="submit" class="btn btn-warning">Update</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</body>
</html>
