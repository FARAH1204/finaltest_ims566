<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Mobile Reviews</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">Mobile Application Reviews</h2>
  <a href="create.php" class="btn btn-success mb-3">+ Add Review</a>
  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>Image</th>
        <th>Title</th>
        <th>Author</th>
        <th>Category</th>
        <th>Status</th>
        <th>Posted</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
<?php
$sql = "SELECT applications.*, categories.title AS category_title
        FROM applications
        JOIN categories ON applications.category_id = categories.id";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()):
?>
<tr>
  <td><img src="uploads/<?= $row['image'] ?>" width="60" class="img-thumbnail"></td>
  <td><?= htmlspecialchars($row['title']) ?></td>
  <td><?= htmlspecialchars($row['author']) ?></td>
  <td><?= htmlspecialchars($row['category_title']) ?></td>
  <td>
    <?php if ($row['status'] == 'active'): ?>
      <span class="badge bg-success">Active</span>
    <?php else: ?>
      <span class="badge bg-danger">Inactive</span>
    <?php endif; ?>
  </td>
  <td><?= date("d M Y, h:i A", strtotime($row['posted_date'])) ?></td>
  <td>
    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
  </td>
</tr>
<?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>

<form method="get" class="mb-3">
  <input type="text" name="search" placeholder="Search title..." class="form-control" value="<?= $_GET['search'] ?? '' ?>">
</form>

<a href="logout.php" class="btn btn-outline-secondary float-end">Logout</a>
