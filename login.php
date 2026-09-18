<?php
session_start();
include 'database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = "";

if (isset($_POST['login'])) {

    $email = mysqli_real_escape_string(
        $conn,
        trim($_POST['email'])
    );

    $password = $_POST['password'];

    if (empty($email) || empty($password)) {

        $message = "<div class='error'>Please fill in all fields.</div>";

    } else {

        $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";

        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {

            $user = mysqli_fetch_assoc($result);

            // Verify password
            if (password_verify($password, $user['password'])) {

                // Store user information in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];

                header("Location: index.php");
                exit();

            } else {

                $message = "<div class='error'>Incorrect password.</div>";
            }

        } else {

            $message = "<div class='error'>Email not registered.</div>";
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

    <title>Login - Book Store</title>

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

        /* Login Box */

        .login-container {
            width: 90%;
            max-width: 420px;
            margin: 60px auto;
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

        .login-btn {
            width: 100%;
            background: #007bff;
            color: white;
            border: none;
            padding: 13px;
            margin-top: 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 17px;
        }

        .login-btn:hover {
            background: #0056b3;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 15px;
        }

        .register-text {
            text-align: center;
            margin-top: 20px;
            color: #555;
        }

        .register-text a {
            color: #007bff;
            text-decoration: none;
        }

        .register-text a:hover {
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


<!-- Login Form -->

<div class="login-container">

    <h1>Login</h1>

    <?php echo $message; ?>


    <form method="POST">

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
            placeholder="Enter your password"
            required
        >


        <button
            type="submit"
            name="login"
            class="login-btn"
        >
            Login
        </button>

    </form>


    <div class="register-text">

        Don't have an account?

        <a href="register.php">
            Register here
        </a>

    </div>

</div>


</body>

</html>