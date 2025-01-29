<?php
include 'db.php';

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $feedback = $_POST['feedback'];
    
    // You should validate and sanitize user input here
    $stmt = $conn->prepare("INSERT INTO feedback (name, feedback) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $feedback);
    $stmt->execute();
    $stmt->close();
    
    // Redirect to home or show a success message
    header("Location: index.php");
    exit();
}
?>
