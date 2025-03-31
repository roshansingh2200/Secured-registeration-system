<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: login.html');
    exit();
}

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'userdatabase');

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle password reset form submission
if (isset($_POST['reset_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $email = $_SESSION['email'];

    // Get user from database using email
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    // Verify old password
    if (!password_verify($old_password, $user['password'])) {
        echo "Old password is incorrect.";
    } else {
        // Update user's password in the database
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password='$hashed_password' WHERE email='$email'";
        if (mysqli_query($conn, $sql)) {
            echo "Password reset successful.";
            header("Refresh: 4;URL=register.php");
            exit;
        } else {
            echo "Error updating password: " . mysqli_error($conn);
        }
    }
}

// Logout functionality
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: register.php');
    exit();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="style.css">

    <!-- Add the client-side validation script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.querySelector('#old_password');
            const newPasswordInput = document.querySelector('#new_password');
            const resetPasswordForm = document.querySelector('form[name="reset_password_form"]');

            resetPasswordForm.addEventListener('submit', function (event) {
                if (passwordInput.value.trim() == newPasswordInput.value.trim()) {
                    alert("Old and new passwords  match. Please try another one.");
                    event.preventDefault(); // Prevent form submission
                }
            });
        });
    </script>
</head>
<body>
    <h1>Reset Password</h1>

    <div class="container" id="container" style="min-height: 356px;">
        <form method="post" name="reset_password_form">
            <label for="old_password">Old Password:</label>
            <div class="input-group">
                <input type="password" id="old_password" name="old_password" class="form-control" required style="background: #eee;">
                <button type="button" id="password-visibility-toggle">
                    <i class="fa fa-eye-slash"></i>
                </button>
            </div>
            
            <label for="new_password">New Password:</label>
            <div class="input-group">
                <input type="password" id="new_password" name="new_password" class="form-control" required style="background: #eee;">
                <button type="button" id="password-visibility-toggle1">
                    <i class="fa fa-eye-slash"></i>
                </button>
            </div>
            
            <button type="submit" name="reset_password">Reset Password</button>
        </form>
    </div>

    <div class="container" id="container" style="min-height: 84px;">
        <form method="post">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>

    <script>
        const passwordInput = document.querySelector('#old_password');
        const passwordVisibilityToggle = document.querySelector('#password-visibility-toggle');

        passwordVisibilityToggle.addEventListener('click', function () {
            if (passwordInput.value.trim() !== '') {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                passwordVisibilityToggle.innerHTML = type === 'password' ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>';
            }
        });
    </script>

    <script>
        const passwordInput1 = document.querySelector('#new_password');
        const passwordVisibilityToggle1 = document.querySelector('#password-visibility-toggle1');

        passwordVisibilityToggle1.addEventListener('click', function () {
            if (passwordInput1.value.trim() !== '') {
                const type = passwordInput1.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput1.setAttribute('type', type);
                passwordVisibilityToggle1.innerHTML = type === 'password' ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>';
            }
        });
    </script>
</body>
</html>
