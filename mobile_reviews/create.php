<?php
// db.php - Database connection file
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mobile_reviews";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database and tables if they don't exist
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);



// Insert sample categories if table is empty
$check_cats = $conn->query("SELECT COUNT(*) as count FROM categories");
$cat_count = $check_cats->fetch_assoc()['count'];

if ($cat_count == 0) {
    $conn->query("INSERT INTO categories (title, status) VALUES 
        ('Gaming', 'active'),
        ('Social Media', 'active'),
        ('Productivity', 'active'),
        ('Entertainment', 'active'),
        ('Education', 'active')");
}

// Create uploads directory if it doesn't exist
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['category'];
    $author = $_POST['author'];
    $title = $_POST['title'];
    $review = $_POST['review'];
    $status = $_POST['status'];
    
    // Handle image upload
    $image = '';
    $image_dir = '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = basename($_FILES['image']['name']);
        $image_dir = "uploads/" . $image;
        
        // Check if file was uploaded successfully
        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_dir)) {
            // File uploaded successfully
        } else {
            echo "<div class='alert alert-danger'>Error uploading file.</div>";
        }
    }
    
    // Insert into database using prepared statement
    $stmt = $conn->prepare("INSERT INTO applications (category_id, author, title, review, image, image_dir, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $category, $author, $title, $review, $image, $image_dir, $status);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Application review added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Get all categories for dropdown
$cats = $conn->query("SELECT * FROM categories WHERE status = 'active'");

// Get all applications to display
$apps = $conn->query("SELECT a.*, c.title as category_title FROM applications a LEFT JOIN categories c ON a.category_id = c.id ORDER BY a.created DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mobile Application Review System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .app-card img {
            height: 200px;
            object-fit: cover;
        }
        .status-active { color: #28a745; }
        .status-inactive { color: #dc3545; }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Mobile App Review System</h1>
    
    <!-- Add Review Form -->
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h2>Add Application Review</h2>
            <form method="post" enctype="multipart/form-data" class="bg-light p-4 rounded shadow-sm">
                <div class="mb-3">
                    <label class="form-label">Title:</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Author:</label>
                    <input type="text" name="author" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Review:</label>
                    <textarea name="review" class="form-control" rows="5" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Category:</label>
                    <select name="category" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php while($cat = $cats->fetch_assoc()): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['title']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Image:</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="form-text text-muted">Optional: Choose an image for the app</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status:</label>
                    <select name="status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Add Review</button>
                <button type="reset" class="btn btn-secondary">Clear Form</button>
            </form>
        </div>
    </div>
    
    <!-- Success Message -->
    <div class="row mt-4">
        <div class="col-md-8 mx-auto">
            <div class="alert alert-info text-center">
                <h4>Thank you for your review!</h4>
                <p>Your application review has been submitted successfully. An admin will review it before it goes live.</p>
                <a href="admin.php" class="btn btn-sm btn-outline-primary">Admin Panel</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>