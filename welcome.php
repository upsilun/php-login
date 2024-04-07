<?php
// Start session
session_start();

// Check if the user is not logged in
if (!isset($_SESSION["username"])) {
    // Redirect to the login page
    header("Location: login.php");
    exit; // Ensure that script execution stops here
}

// If the user is logged in, you can continue rendering the welcome page
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .welcome-container {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <h2>Welcome, <?php echo $_SESSION["username"]; ?></h2>
        <p>This is the welcome page. You are logged in.</p>
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
