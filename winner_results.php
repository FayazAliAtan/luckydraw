<?php
// Include database connection
include 'db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Fetch winners securely for each category
$query_normal = "SELECT * FROM winners WHERE category = 'Normal' ORDER BY position ASC";
$query_pro = "SELECT * FROM winners WHERE category = 'Pro' ORDER BY position ASC";
$query_mega = "SELECT * FROM winners WHERE category = 'Mega' ORDER BY position ASC";

// Execute queries and handle errors
$result_normal = $conn->query($query_normal);
if (!$result_normal) {
    die("Error executing Normal category query: " . $conn->error);
}

$result_pro = $conn->query($query_pro);
if (!$result_pro) {
    die("Error executing Pro category query: " . $conn->error);
}

$result_mega = $conn->query($query_mega);
if (!$result_mega) {
    die("Error executing Mega category query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Winner Results - Lucky Draw</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Lucky Draw</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="winner_results.php">Winner Results</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Winner Results for Normal Category -->
<div class="container my-5">
  <h3>Normal Category Winners</h3>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Position</th>
        <th>Name</th>
        <th>Prize</th>
      </tr>
    </thead>
    <tbody>
      <?php
        if ($result_normal->num_rows > 0) {
          while ($row = $result_normal->fetch_assoc()) {
            echo "<tr><td>" . htmlspecialchars($row['position']) . "</td><td>" . htmlspecialchars($row['name']) . "</td><td>" . htmlspecialchars($row['prize']) . "</td></tr>";
          }
        } else {
          echo "<tr><td colspan='3' class='text-center'>No results available</td></tr>";
        }
      ?>
    </tbody>
  </table>
</div>

<!-- Winner Results for Pro Category -->
<div class="container my-5">
  <h3>Pro Category Winners</h3>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Position</th>
        <th>Name</th>
        <th>Prize</th>
      </tr>
    </thead>
    <tbody>
      <?php
        if ($result_pro->num_rows > 0) {
          while ($row = $result_pro->fetch_assoc()) {
            echo "<tr><td>" . htmlspecialchars($row['position']) . "</td><td>" . htmlspecialchars($row['name']) . "</td><td>" . htmlspecialchars($row['prize']) . "</td></tr>";
          }
        } else {
          echo "<tr><td colspan='3' class='text-center'>No results available</td></tr>";
        }
      ?>
    </tbody>
  </table>
</div>

<!-- Winner Results for Mega Category -->
<div class="container my-5">
  <h3>Mega Category Winners</h3>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Position</th>
        <th>Name</th>
        <th>Prize</th>
      </tr>
    </thead>
    <tbody>
      <?php
        if ($result_mega->num_rows > 0) {
          while ($row = $result_mega->fetch_assoc()) {
            echo "<tr><td>" . htmlspecialchars($row['position']) . "</td><td>" . htmlspecialchars($row['name']) . "</td><td>" . htmlspecialchars($row['prize']) . "</td></tr>";
          }
        } else {
          echo "<tr><td colspan='3' class='text-center'>No results available</td></tr>";
        }
      ?>
    </tbody>
  </table>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3">
  <p>&copy; 2025 Lucky Draw Contest. All Rights Reserved.</p>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
