<?php
require "Mail/phpmailer/PHPMailerAutoload.php";
require 'vendor/autoload.php';

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    Dotenv\Dotenv::createImmutable(__DIR__)->load();

    // Verify the reCAPTCHA
    $recaptcha_secret_key = getenv('RECAPTCHA_SECRET_KEY');
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
        header("Refresh: 4;URL=register.php");
        exit;
    }

    // Generate OTP
    $otp = rand(100000, 999999);

    // Send OTP to user's email using PHPMailer
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
        $mail->Subject = 'OTP Verification';
        $mail->Body    = 'Your OTP is ' . $otp;

        $mail->send();
        echo 'OTP sent to your email.';

        // Store the OTP in a session variable
        session_start();
        $_SESSION['otp'] = $otp;
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $name;
        $_SESSION['hashed_password'] = password_hash($password, PASSWORD_BCRYPT);
        
        // Redirect to OTP verification page
        header('Location: otp-verification.php');
        exit();

    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="style.css">

    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <!-- Password Strength Checker JS -->
    <script src="password.js"></script>

    <script>
        function onSubmit(token) {
            if (grecaptcha.getResponse() == '') {
                alert('Please complete the reCAPTCHA');
                return false;
            } else {
                return true;
            }
        }
    </script>
</head>
<body>
<div class="container" id="container">
    <div class="form-container sign-up-container">
       <form action="register.php" method="POST" onsubmit="return onSubmit();">
            <h1>Create Account</h1>
            <span>or use your email for registration</span>

            <input type="text" id="name" name="name" placeholder="Name" required>
            <input type="email" id="email" name="email" placeholder="Email" required>

            <div class="input-group">
                <input type="password" id="password" name="password" class="form-control" onkeyup="checkPasswordStrength();" placeholder="Password" required style="background: #eee;">
                <button type="button" id="password-visibility-toggle">
                    <i class="fa fa-eye-slash"></i>
                </button>
            </div>

            <input type="password" id="confirm-password" class="form-control" placeholder="Confirm password" required style="background: #eee;" onkeyup="checkPasswordMatch();">
            <div class="form-text">Password Strength: <span id="password-strength"></span></div>
            <div id="password-missing" class="form-text text-danger"></div>
            <div id="password-matching-message" class="form-text text-danger"></div>

            <div class="mb-3">
                <div class="g-recaptcha" data-sitekey="6Lf9lc8kAAAAAPOuH-zV9RPj00CJddmH7Phtxinz"></div>
            </div>
            <button type="submit" id="register-button">Register</button>
            <button type="button" onclick="generatePassword()">Generate Password</button>
        </form>
    </div>

    <div class="form-container sign-in-container">
        <form action="login.php" method="POST">
            <h1>Sign in</h1>
            <span>or use your account</span>
            <input type="email" id="email1" name="email" placeholder="Email" required>

            <div class="input-group">
                <input type="password" name="password" class="form-control" id="password1" placeholder="Password" required style="background: #eee;">
                <button type="button" id="password-visibility-toggle1">
                    <i class="fa fa-eye-slash"></i>
                </button>
            </div>

            <div class="mb-3">
                <div class="g-recaptcha" style="margin-top: 10px;" data-sitekey="6Lf9lc8kAAAAAPOuH-zV9RPj00CJddmH7Phtxinz"></div>
            </div>
            <a href="forgotpassword.php">Forgot your password?</a>
            <button type="submit">Login</button>
        </form>
    </div>

    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1>Welcome Back!</h1>
                <p>To keep connected with us please login with your personal info</p>
                <button class="ghost" id="signIn">Login</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1>Hello, Friend!</h1>
                <p>Enter your personal details and start your journey with us</p>
                <button class="ghost" id="signUp">Register</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Password Strength Checker JS -->
<script src="password.js"></script>
<script src="user-allow.js"></script>
<script src="password-generator.js"></script>
<script src="password-visible.js"></script>
<script src="password-visible2.js"></script>
<script>
    function checkPasswordMatch() {
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirm-password").value;
        var message = document.getElementById("password-matching-message");

        // Check if passwords match
        if (password !== confirmPassword) {
            message.textContent = "Passwords do not match";
        } else {
            message.textContent = "";
        }
    }

    function onSubmit() {
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirm-password").value;

        // Check if passwords match
        if (password !== confirmPassword) {
            document.getElementById("password-matching-message").textContent = "Passwords do not match";
            return false;
        }

        return true; // Allow form submission
    }
</script>

<script src="swipe.js"></script>

</body>
</html>
