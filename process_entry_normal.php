<?php
// Include database connection
include 'db.php'; 

// Include CSRF protection function
include 'csrf.php'; 

// Check if the CSRF token is valid
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF token.");
    }

    // Sanitize inputs
    $category = 'Normal'; // Static value for Normal category
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $contact = filter_input(INPUT_POST, 'contact', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $upi_id = filter_input(INPUT_POST, 'upi_id', FILTER_SANITIZE_STRING);
    $donation = filter_input(INPUT_POST, 'donation', FILTER_SANITIZE_STRING);

    // Validate required fields
    if (empty($name) || empty($contact) || empty($email) || empty($upi_id) || empty($donation)) {
        // Redirect with error message (using URL query parameters or session)
        header("Location: error.php?message=All fields are required.");
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Redirect with error message
        header("Location: error.php?message=Invalid email format.");
        exit();
    }

    // Check if the phone number (contact) is already taken
    $stmt = $conn->prepare("SELECT COUNT(*) FROM donations WHERE contact = ?");
    $stmt->bind_param("s", $contact);
    $stmt->execute();
    $stmt->bind_result($contact_count);
    $stmt->fetch();
    $stmt->close();

    if ($contact_count > 0) {
        // Redirect with error message
        header("Location: error.php?message=The phone number is already in use.");
        exit();
    }

    // Check if the email is already taken
    $stmt = $conn->prepare("SELECT COUNT(*) FROM donations WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($email_count);
    $stmt->fetch();
    $stmt->close();

    if ($email_count > 0) {
        // Redirect with error message
        header("Location: error.php?message=The email address is already in use.");
        exit();
    }
    
    // Check current entry count from the database
    $category = $_POST['category'];
    $query = "SELECT COUNT(*) AS total_entries FROM donations WHERE category = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $current_entries = $data['total_entries'];
   if ($current_entries >= 9) {
     echo "The entry limit for this category has been reached.";
     exit;
    }


    // Prepare SQL query to insert data into database using prepared statements
    try {
        $stmt = $conn->prepare("INSERT INTO donations (category, name, contact, email, upi_id, donation) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $category, $name, $contact, $email, $upi_id, $donation);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to a success page after successful submission
            header("Location: success.php?message=Your entry has been successfully submitted!");
            exit();
        } else {
            // Redirect with error message if the insert fails
            header("Location: error.php?message=There was an error submitting your entry. Please try again.");
            exit();
        }

        // Close the prepared statement
        $stmt->close();
    } catch (Exception $e) {
        // Log the error for internal tracking
        error_log($e->getMessage());
        // Redirect with error message
        header("Location: error.php?message=An error occurred. Please try again later.");
        exit();
    }

    // Close database connection
    $conn->close();
}
?>
