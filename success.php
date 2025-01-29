<?php
// Check if there's a message in the URL query parameter
if (isset($_POST['message'])) {
    $message = htmlspecialchars($_POST['message']);  // Sanitize the message for security
} else {
    $message = "Your Data is successfully Submitted";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Success</title>
</head>
<body>
    <h1>Success</h1>
    <p><?php echo $message; ?></p>
</body>
</html>
