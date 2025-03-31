<?php
session_start();

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the email address from the form
    $email = $_POST['email'];

    // Check if the email exists in the database
    $conn = mysqli_connect("localhost", "root", "", "userdatabase");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Email doesn't exist in the database, display an error message
        echo '<div class="alert alert-danger">Email does not exist.</div>';
        header("Refresh: 4; URL=forgot-password.html");
        exit;
    }

    // Generate a new password
    $new_password = bin2hex(random_bytes(8));

    // Update the user's password in the database
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare('UPDATE users SET password = ? WHERE email = ?');
    $stmt->bind_param('ss', $hashed_password, $email);
    $result = $stmt->execute();

    if ($result === false) {
        // Password update query failed
        echo '<div class="alert alert-danger">Failed to update password.</div>';
        exit;
    }

    // Send the new password to the user's email using PHPMailer
    require "Mail/phpmailer/PHPMailerAutoload.php";
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();                                           
        $mail->Host       = 'smtp.gmail.com';       
        $mail->SMTPAuth   = true;                                 
        $mail->Username   = 'baddepartment434@gmail.com';               
        $mail->Password   = 'posefoggtloonlrm';                  
        $mail->SMTPSecure = 'tls';                                
        $mail->Port       = 587;                                 

        // Recipients
        $mail->setFrom('roshingh001@gmail.com', 'Host');
        $mail->addAddress($email); // No need for name if it's not defined

        // Content
        $mail->isHTML(true);                                 
        $mail->Subject = 'New Password';
        $mail->Body    = 'Your new password is: ' . $new_password;

        $mail->send();
        echo '<div class="alert alert-success">New password sent to your email.</div>';

        // Redirect to login page
        header('Location: register.php');
        exit();

    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Message could not be sent. Mailer Error: ' . $mail->ErrorInfo . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container" id="container" style="min-height: 346px;">
        <p>Enter your email address and we'll send you a new password.</p>
        <form method="post" action="forget_password.php">
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>
</html>
