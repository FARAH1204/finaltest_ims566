<?php
// admin.php - Admin Panel for Mobile App Review System
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

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action == 'delete_app') {
        $id = $_POST['id'];
        $conn->query("DELETE FROM applications WHERE id = $id");
        echo "<div class='alert alert-success'>Application deleted successfully!</div>";
    }
    
    if ($action == 'update_status') {
        $id = $_POST['id'];
        $status = $_POST['status'];
        $conn->query("UPDATE applications SET status = '$status' WHERE id = $id");
        echo "<div class='alert alert-success'>Status updated successfully!</div>";
    }
    
    if ($action == 'add_category') {
        $title = $_POST['title'];
        $status = $_POST['status'];
        $conn->query("INSERT INTO categories (title, status) VALUES ('$title', '$status')");
        echo "<div class='alert alert-success'>Category added successfully!</div>";
    }
    
    if ($action == 'delete_category') {
        $id = $_POST['id'];
        $conn->query("DELETE FROM categories WHERE id = $id");
        echo "<div class='alert alert-success'>Category deleted successfully!</div>";
    }
}

// Get all applications
$apps = $conn->query("SELECT a.*, c.title as category_title FROM applications a LEFT JOIN categories c ON a.category_id = c.id ORDER BY a.created DESC");

// Get all categories
$categories = $conn->query("SELECT * FROM categories ORDER BY title");

// Get statistics
$total_apps = $conn->query("SELECT COUNT(*) as count FROM applications")->fetch_assoc()['count'];
$active_apps = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status = 'active'")->fetch_assoc()['count'];
$inactive_apps = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status = 'inactive'")->fetch_assoc()['count'];
$total_categories = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Mobile App Review System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .app-card img {
            height: 200px;
            object-fit: cover;
        }
        .status-active { color: #28a745; font-weight: bold; }
        .status-inactive { color: #dc3545; font-weight: bold; }
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
        }
        .stats-card {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <h1 class="mb-0">Admin Panel</h1>
            <p class="mb-0">Mobile App Review System Management</p>
        </div>
    </div>

    <div class="container mt-4">
        <!-- Navigation -->
        <div class="row mb-4">
            <div class="col-12">
                <nav class="navbar navbar-expand-lg navbar-light bg-light rounded">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="admin.php">Admin</a>
                        <div class="navbar-nav">
                            <a class="nav-link" href="index.php">← Back to Main Site</a>
                        </div>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3 class="text-primary"><?= $total_apps ?></h3>
                        <p class="mb-0">Total Applications</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3 class="text-success"><?= $active_apps ?></h3>
                        <p class="mb-0">Active Applications</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3 class="text-danger"><?= $inactive_apps ?></h3>
                        <p class="mb-0">Inactive Applications</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3 class="text-info"><?= $total_categories ?></h3>
                        <p class="mb-0">Total Categories</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="apps-tab" data-bs-toggle="tab" data-bs-target="#apps" type="button">Applications</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories" type="button">Categories</button>
            </li>
        </ul>

        <div class="tab-content mt-4" id="adminTabsContent">
            <!-- Applications Tab -->
            <div class="tab-pane fade show active" id="apps" role="tabpanel">
                <h2>All Applications</h2>
                
                <!-- Applications Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($app = $apps->fetch_assoc()): ?>
                            <tr>
                                <td><?= $app['id'] ?></td>
                                <td>
                                    <?php if($app['image'] && file_exists($app['image_dir'])): ?>
                                        <img src="<?= htmlspecialchars($app['image_dir']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                    <?php else: ?>
                                        <span class="text-muted">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($app['title']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars(substr($app['review'], 0, 50)) ?>...</small>
                                </td>
                                <td><?= htmlspecialchars($app['author']) ?></td>
                                <td><?= htmlspecialchars($app['category_title']) ?></td>
                                <td>
                                    <span class="status-<?= $app['status'] ?>"><?= ucfirst($app['status']) ?></span>
                                </td>
                                <td><?= date('M j, Y', strtotime($app['created'])) ?></td>
                                <td>
                                    <!-- Change Status -->
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="id" value="<?= $app['id'] ?>">
                                        <select name="status" class="form-select form-select-sm" style="width: auto; display: inline;" onchange="this.form.submit()">
                                            <option value="active" <?= $app['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                            <option value="inactive" <?= $app['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                    </form>
                                    
                                    <!-- Delete Button -->
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this application?')">
                                        <input type="hidden" name="action" value="delete_app">
                                        <input type="hidden" name="id" value="<?= $app['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Categories Tab -->
            <div class="tab-pane fade" id="categories" role="tabpanel">
                <div class="row">
                    <div class="col-md-8">
                        <h2>Categories</h2>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $categories->data_seek(0); // Reset pointer
                                    while($category = $categories->fetch_assoc()): 
                                    ?>
                                    <tr>
                                        <td><?= $category['id'] ?></td>
                                        <td><?= htmlspecialchars($category['title']) ?></td>
                                        <td><span class="status-<?= $category['status'] ?>"><?= ucfirst($category['status']) ?></span></td>
                                        <td><?= date('M j, Y', strtotime($category['created'])) ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                                <input type="hidden" name="action" value="delete_category">
                                                <input type="hidden" name="id" value="<?= $category['id'] ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h4>Add New Category</h4>
                        <form method="POST" class="bg-light p-3 rounded">
                            <input type="hidden" name="action" value="add_category">
                            <div class="mb-3">
                                <label class="form-label">Category Title:</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status:</label>
                                <select name="status" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Category</button>
                        </form>
                    </div>
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