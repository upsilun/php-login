<?php
    // Start session
    session_start();

    // Database connection parameters
    $servername = "localhost";
    $username = "root";
    $password = ""; // Password for MySQL server
    $dbname = "php-bank"; // Replace 'php-bank' with your actual database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Initialize variables to store user input and error messages
    $name = $username = $password = $confirm_password = "";
    $name_err = $username_err = $password_err = $confirm_password_err = "";

    // Process form data when the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate full name
        if (empty(trim($_POST["name"]))) {
            $name_err = "Please enter your full name.";
        } else {
            $name = trim($_POST["name"]);
        }

        // Validate username
        if (empty(trim($_POST["username"]))) {
            $username_err = "Please enter a username.";
        } else {
            // Prepare a select statement to check if the username already exists
            $sql = "SELECT id FROM users WHERE username = ?";
            
            // Prepare the statement
            $stmt = $conn->prepare($sql);

            // Bind parameters
            $stmt->bind_param("s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Execute the statement
            $stmt->execute();

            // Store result
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $username_err = "This username is already taken.";
            } else {
                $username = trim($_POST["username"]);
            }

            // Close statement
            $stmt->close();
        }

        // Validate password
        if (empty(trim($_POST["password"]))) {
            $password_err = "Please enter a password.";     
        } elseif (strlen(trim($_POST["password"])) < 6) {
            $password_err = "Password must have at least 6 characters.";
        } else {
            $password = trim($_POST["password"]);
        }

        // Validate confirm password
        if (empty(trim($_POST["confirm_password"]))) {
            $confirm_password_err = "Please confirm password.";     
        } else {
            $confirm_password = trim($_POST["confirm_password"]);
            if (empty($password_err) && ($password != $confirm_password)) {
                $confirm_password_err = "Password did not match.";
            }
        }

        // Check input errors before inserting into database
        if (empty($name_err) && empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
            // Generate a random 9-digit ID
            $id = mt_rand(100000000, 999999999);

            // Prepare an insert statement
            $sql = "INSERT INTO users (id, name, username, password, balance, status) VALUES (?, ?, ?, ?, ?, ?)";
            
            // Prepare the statement
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                // Bind parameters
                $stmt->bind_param("isssii", $param_id, $name, $param_username, $param_password, $param_balance, $param_status);
                
                // Set parameters
                $param_id = $id;
                $param_name = $name;
                $param_username = $username;
                $param_password = $password; // Store the password as plain text (Not recommended in production)
                $param_balance = 0;
                $param_status = 1; // Assuming '1' represents an active account
                
                // Attempt to execute the statement
                if ($stmt->execute()) {
                    // Redirect to login page after successful registration
                    header("location: index.php");
                    exit;
                } else {
                    echo "Something went wrong. Please try again later.";
                }

                // Close statement
                $stmt->close();
            }
        }
        
        // Close connection
        $conn->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        .register-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .register-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .register-form input[type="text"],
        .register-form input[type="password"],
        .register-form input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .register-form input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
        }
        .register-form input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <form class="register-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                <input type="text" name="name" placeholder="Full Name" value="<?php echo $name; ?>" required>
                <span class="error-message"><?php echo $name_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <input type="text" name="username" placeholder="Username" value="<?php echo $username; ?>" required>
                <span class="error-message"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <input type="password" name="password" placeholder="Password" value="<?php echo $password; ?>" required>
                <span class="error-message"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <input type="password" name="confirm_password" placeholder="Confirm Password" value="<?php echo $confirm_password; ?>" required>
                <span class="error-message"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" value="Register">
            </div>
            <p>Already have an account? <a href="index.php">Login here</a>.</p>
        </form>
    </div>
</body>
</html>
