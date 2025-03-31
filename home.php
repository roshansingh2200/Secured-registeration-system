<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header('Location:register.php');
    exit();
}

// Logout functionality
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: register.php');
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Home Page</title>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Welcome to the Home Page!</h1>

       <p>You are logged in as: <?php echo $_SESSION['email']; ?></p>

 <div class="container" id="container" style="
    min-height: 84px;
">   <form method="post" action="reset_password.php">
        <input type="hidden" name="email" value="<?php echo $_SESSION['email']; ?>">
        <button type="submit">Reset Password</button>
    </form></div>
     <div class="container" id="container" style="
    min-height: 84px;
">
    <form method="post">
        <button type="submit" name="logout">Logout</button>
    </form>
</div>

</body>
</html>