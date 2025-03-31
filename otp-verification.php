<?php

// Start the session
session_start();

// Create connection
$conn = mysqli_connect("localhost", "root", "");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS userdatabase";
if (mysqli_query($conn, $sql)) {

} else {
    echo "Error creating database: " . mysqli_error($conn);
}

// Select the database
mysqli_select_db($conn, "userdatabase");

// Create table
$sql = "CREATE TABLE IF NOT EXISTS users (
  id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL
)";

if (mysqli_query($conn, $sql)) {

} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
// Check if the OTP has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $otp = $_POST['otp'];

    // Check if the OTP matches
    if (isset($_SESSION['otp']) && $_SESSION['otp'] == $otp) {

        // Check if the email already exists
        $existingEmail = $_SESSION['email'];

        // Create a new connection object
        $connCheckEmail = new mysqli("localhost", "root", "", "userdatabase");

        // Check connection
        if ($connCheckEmail->connect_error) {
            die("Connection failed: " . $connCheckEmail->connect_error);
        }

        $checkEmailQuery = "SELECT * FROM users WHERE email = '$existingEmail'";
        $result = mysqli_query($connCheckEmail, $checkEmailQuery);

        if (mysqli_num_rows($result) > 0) {
            echo "Email address already exists. Registration failed.";
        } else {
            // Save the user's details in the database
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "userdatabase";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Insert user details into the database using prepared statements
            $name = $_SESSION['name'];
            $email = $_SESSION['email'];
            $hashed_password = $_SESSION['hashed_password'];

            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt->execute()) {
                // Display a success message and redirect to the login page after 4 seconds
                echo 'Registration successful! You will be redirected to the login page in 4 seconds.';
                header("Refresh: 4; URL=register.php");
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close the prepared statement
            $stmt->close();

            // Close the database connection
            $conn->close();

            // Destroy the session
            session_unset();
            session_destroy();
        }

        // Close the check email connection
        $connCheckEmail->close();
    } else {
        echo "Invalid OTP";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container" id="container" style="min-height: 346px;">
        <form method="POST">
            <label>Enter OTP by email:</label>
            <input type="text" name="otp">
            <button type="submit">Submit OTP</button>
        </form>
    </div>
</body>
</html>
