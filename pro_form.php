<?php
// Include database connection and CSRF protection functions
include 'db.php';
include 'csrf.php'; 

// Start session for CSRF token
session_start();

// CSRF Token Generation (if form is not submitted)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['csrf_token'] = generateCSRFToken();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pro Category Entry</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <style>
    /* Custom Styling */
    body {
      background-color: #f9fafe;
      font-family: 'Arial', sans-serif;
    }
    .form-container {
      background-color: #fff;
      border-radius: 10px;
      padding: 30px;
      margin: 50px auto;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
      max-width: 600px;
    }
    .form-header {
      font-size: 24px;
      font-weight: bold;
      color: #333;
      text-align: center;
      margin-bottom: 20px;
    }
    .form-label {
      font-weight: bold;
      color: #555;
    }
    .btn-custom {
      background-color: #007bff;
      color: white;
      font-weight: bold;
      padding: 10px 15px;
      border-radius: 5px;
    }
    .btn-custom:hover {
      background-color: #0056b3;
    }
    .alert {
      margin-top: 15px;
      font-size: 16px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="form-container">
      <h1 class="form-header">Pro Category Entry</h1>

      <?php
      // Handle form submission
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          // Validate CSRF Token
          if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
              echo "<div class='alert alert-danger'>Invalid CSRF token. Please try again.</div>";
              exit;
          }

          // Sanitize inputs
          $category = 'Pro'; // Static value
          $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
          $contact = filter_input(INPUT_POST, 'contact', FILTER_SANITIZE_STRING);
          $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
          $upi_id = filter_input(INPUT_POST, 'upi_id', FILTER_SANITIZE_STRING);
          $donation = filter_input(INPUT_POST, 'donation', FILTER_SANITIZE_STRING);

          // Validate inputs
          if (empty($name) || empty($contact) || empty($email) || empty($upi_id) || empty($donation)) {
              echo "<div class='alert alert-danger'>All fields are required.</div>";
          } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
              echo "<div class='alert alert-danger'>Invalid email format.</div>";
          } else {
              try {
                  // Insert into the database
                  $stmt = $conn->prepare("INSERT INTO donations (category, name, contact, email, upi_id, donation) VALUES (?, ?, ?, ?, ?, ?)");
                  $stmt->bind_param("ssssss", $category, $name, $contact, $email, $upi_id, $donation);

                  if ($stmt->execute()) {
                      echo "<div class='alert alert-success'>Your entry has been successfully submitted!</div>";
                  } else {
                      echo "<div class='alert alert-danger'>There was an error submitting your entry. Please try again later.</div>";
                  }

                  // Close the statement
                  $stmt->close();
              } catch (Exception $e) {
                  // Log the error (optional)
                  error_log($e->getMessage());
                  echo "<div class='alert alert-danger'>An error occurred. Please try again later.</div>";
              }
          }

          // Close the database connection
          $conn->close();
      }
      ?>

      <form action="process_entry.php" method="POST"  >
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="category" value="Pro">

        <!-- Donation Selection -->
        <div class="mb-3">
          <label for="donationSelectPro" class="form-label">Choose Donation:</label>
          <select class="form-select" name="donation" id="donationSelectPro" required>
            <option value="NGO">NGOs</option>
            <option value="Medical">Medical</option>
            <option value="Red Cross">Red Cross</option>
          </select>
        </div>

        <!-- Name -->
        <div class="mb-3">
          <label for="namePro" class="form-label">Your Name:</label>
          <input type="text" class="form-control" name="name" id="namePro" required>
        </div>

        <!-- Contact -->
        <div class="mb-3">
          <label for="contactPro" class="form-label">Your Contact:</label>
          <input type="text" class="form-control" name="contact" id="contactPro" required>
        </div>

        <!-- Email -->
        <div class="mb-3">
          <label for="emailPro" class="form-label">Your Email:</label>
          <input type="email" class="form-control" name="email" id="emailPro" required>
        </div>

        <!-- UPI ID -->
        <div class="mb-3">
          <label for="upi_idPro" class="form-label">Your UPI ID:</label>
          <input type="text" class="form-control" name="upi_id" id="upi_idPro" required>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-custom w-100">Submit Entry</button>
      </form>
    </div>
  </div>
  
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>


<script>
  document.getElementById('paymentForm').onsubmit = function (e) {
    e.preventDefault();
    document.getElementById('loader').classList.remove('d-none');

    const data = new FormData(e.target);
    fetch('process_payment.php', {
      method: 'POST',
      body: data,
    })
      .then((response) => response.json())
      .then((res) => {
        document.getElementById('loader').classList.add('d-none');
        if (res.status === 'success') {
          const options = {
            key: 'YOUR_API_KEY',
            amount: res.amount * 100,
            currency: 'INR',
            name: 'Lucky Draw',
            description: 'Lucky Draw Ticket Purchase',
            order_id: res.orderId,
            handler: function (response) {
              alert('Payment Successful! Payment ID: ' + response.razorpay_payment_id);
              location.reload();
            },
            prefill: {
              name: data.get('name'),
              contact: data.get('phone'),
            },
          };

          const rzp = new Razorpay(options);
          rzp.open();
        } else {
          alert('Payment failed: ' + res.message);
        }
      });
  };
</script>