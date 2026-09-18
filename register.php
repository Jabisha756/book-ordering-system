<?php
session_start();
include 'database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = "";

if (isset($_POST['register'])) {

    $name = mysqli_real_escape_string(
        $conn,
        trim($_POST['name'])
    );

    $email = mysqli_real_escape_string(
        $conn,
        trim($_POST['email'])
    );

    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (
        empty($name) ||
        empty($email) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $message = "<div class='error'>
                        Please fill in all fields.
                    </div>";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "<div class='error'>
                        Please enter a valid email address.
                    </div>";

    } elseif (strlen($password) < 6) {

        $message = "<div class='error'>
                        Password must be at least 6 characters.
                    </div>";

    } elseif ($password !== $confirm_password) {

        $message = "<div class='error'>
                        Passwords do not match.
                    </div>";

    } else {

        // Check if email already exists
        $check_sql = "SELECT id FROM users 
                      WHERE email = '$email'";

        $check_result = mysqli_query(
            $conn,
            $check_sql
        );

        if (mysqli_num_rows($check_result) > 0) {

            $message = "<div class='error'>
                            This email is already registered.
                        </div>";

        } else {

            // Encrypt password
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Insert new user
            $sql = "INSERT INTO users 
                    (name, email, password)
                    VALUES 
                    ('$name', '$email', '$hashed_password')";

            if (mysqli_query($conn, $sql)) {

                $message = "<div class='success'>
                                Registration successful!
                                You can now login.
                            </div>";

            } else {

                $message = "<div class='error'>
                                Registration failed. Please try again.
                            </div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Register - Book Store</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

        /* Navigation */
        .navbar {
            background: #333;
            padding: 15px;
            text-align: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 15px;
            font-size: 17px;
        }

        .navbar a:hover {
            color: #ffc107;
        }

        /* Register Box */
        .register-container {
            width: 90%;
            max-width: 450px;
            margin: 50px auto;
            background: white;
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #333;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        input:focus {
            border-color: #007bff;
            outline: none;
        }

        .register-btn {
            width: 100%;
            background: #28a745;
            color: white;
            border: none;
            padding: 13px;
            margin-top: 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 17px;
        }

        .register-btn:hover {
            background: #218838;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 15px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 15px;
        }

        .login-text {
            text-align: center;
            margin-top: 20px;
            color: #555;
        }

        .login-text a {
            color: #007bff;
            text-decoration: none;
        }

        .login-text a:hover {
            text-decoration: underline;
        }

    </style>

</head>

<body>


<!-- Navigation -->

<div class="navbar">

    <a href="index.php">Home</a>

    <a href="books.php">Books</a>

    <a href="cart.php">Cart</a>

    <a href="login.php">Login</a>

    <a href="register.php">Register</a>

</div>


<!-- Register Form -->

<div class="register-container">

    <h1>Create Account</h1>

    <?php echo $message; ?>


    <form method="POST">

        <label>Full Name</label>

        <input
            type="text"
            name="name"
            placeholder="Enter your full name"
            required
        >


        <label>Email Address</label>

        <input
            type="email"
            name="email"
            placeholder="Enter your email"
            required
        >


        <label>Password</label>

        <input
            type="password"
            name="password"
            placeholder="Minimum 6 characters"
            required
        >


        <label>Confirm Password</label>

        <input
            type="password"
            name="confirm_password"
            placeholder="Confirm your password"
            required
        >


        <button
            type="submit"
            name="register"
            class="register-btn"
        >
            Register
        </button>

    </form>


    <div class="login-text">

        Already have an account?

        <a href="login.php">
            Login here
        </a>

    </div>

</div>

</body>

</html>