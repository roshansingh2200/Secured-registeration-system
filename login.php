<?php

require "Mail/phpmailer/PHPMailerAutoload.php";
require 'vendor/autoload.php';

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Connect to the database
    $db = new mysqli('localhost', 'root', '', 'userdatabase');

    // Check if the email exists in the database
    $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        // Email not found, display an error message
        echo 'Email not found!';
        header("Refresh: 4;URL= register.php");
        exit;
    }

    // Verify the reCAPTCHA
    $recaptcha_secret_key = '6Lf9lc8kAAAAAGUJOxLelJ_EYn0UsGMri-eM1Zsj';
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_data = array(
        'secret' => $recaptcha_secret_key,
        'response' => $recaptcha_response
    );
    $recaptcha_options = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query($recaptcha_data)
        )
    );
    $recaptcha_context = stream_context_create($recaptcha_options);
    $recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
    $recaptcha_response_data = json_decode($recaptcha_result);
    if (!$recaptcha_response_data->success) {
        // reCAPTCHA verification failed, display an error message
        echo 'reCAPTCHA verification failed!';
        header("Refresh: 4;URL= register.php");
        exit;
    }

    // Get the details for the email
    $row = $result->fetch_assoc();
    $hashed_password = $row['password'];
    $name = $row['name'];
    $phone = $row['phone'];
    $email = $row['email'];

    // Verify the password
    if (password_verify($password, $hashed_password)) {
        // Password is correct, start a new session
        session_start();
        $_SESSION['email'] = $email;

        // Send notification to user's email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            //Server settings
        $mail->isSMTP();                                           
        $mail->Host       = 'smtp.gmail.com';       
        $mail->SMTPAuth   = true;                                 
        $mail->Username   = 'baddepartment434@gmail.com';               
        $mail->Password   = 'posefoggtloonlrm';                  
        $mail->SMTPSecure = 'tls';                                
        $mail->Port       = 587;                                 

        //Recipients
        $mail->setFrom('roshingh001@gmail.com', 'Host');
        $mail->addAddress($email, $name);

            // Content
            $mail->isHTML(true);                                
            $mail->Subject = 'Notifications';
            $mail->Body    = 'You have been logged in some device';

            $mail->send();
            echo 'Notification sent to your email.';

            session_start();
            $_SESSION['email'] = $email;

            // Redirect to home page
            header('Location: home.php');
            exit();

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        // Password is incorrect, display an error message
        echo 'Password is incorrect!';
        header("Refresh: 4;URL=register.php");
        exit;
    }

    // Close the database connection
    $db->close();
}
?>
