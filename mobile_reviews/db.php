<?php
$conn = new mysqli("localhost", "root", "", "mobile_reviews");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
