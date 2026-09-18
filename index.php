<?php
session_start();
include 'database.php';

// Get books from database
$sql = "SELECT * FROM books ORDER BY id DESC LIMIT 8";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookHub | Online Book Ordering System</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        header {
            background: #2c3e50;
            color: white;
            padding: 15px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 26px;
            font-weight: bold;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        nav a:hover {
            color: #f39c12;
        }

        .hero {
            background: #34495e;
            color: white;
            text-align: center;
            padding: 80px 20px;
        }

        .hero h1 {
            font-size: 42px;
            margin-bottom: 10px;
        }

        .hero p {
            font-size: 18px;
        }

        .btn {
            display: inline-block;
            background: #f39c12;
            color: white;
            padding: 12px 25px;
            margin-top: 15px;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background: #d35400;
        }

        .books {
            padding: 40px 7%;
        }

        .books h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .book-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
        }

        .book-card {
            background: white;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .book-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .book-card h3 {
            margin: 12px 0 5px;
        }

        .price {
            color: #e67e22;
            font-weight: bold;
            font-size: 18px;
        }

        footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }
    </style>
</head>

<body>

<header>
    <div class="logo">📚 BookHub</div>

    <nav>
        <a href="index.php">Home</a>
        <a href="books.php">Books</a>
        <a href="cart.php">Cart 🛒</a>

        <?php if(isset($_SESSION['user_id'])) { ?>
            <a href="logout.php">Logout</a>
        <?php } else { ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php } ?>
    </nav>
</header>

<section class="hero">
    <h1>Welcome to BookHub</h1>
    <p>Discover, Select and Order Your Favourite Books Online</p>

    <a href="books.php" class="btn">Browse Books</a>
</section>

<section class="books">

    <h2>Latest Books</h2>

    <div class="book-container">

        <?php
        if(mysqli_num_rows($result) > 0) {

            while($book = mysqli_fetch_assoc($result)) {
        ?>

            <div class="book-card">

                <img src="images/<?php echo htmlspecialchars($book['image']); ?>"
                     alt="<?php echo htmlspecialchars($book['title']); ?>">

                <h3>
                    <?php echo htmlspecialchars($book['title']); ?>
                </h3>

                <p>
                    <?php echo htmlspecialchars($book['author']); ?>
                </p>

                <p class="price">
                    Rs. <?php echo htmlspecialchars($book['price']); ?>
                </p>

                <a href="book_details.php?id=<?php echo $book['id']; ?>"
                   class="btn">
                   View Details
                </a>

            </div>

        <?php
            }

        } else {
            echo "<p style='text-align:center;'>No books available.</p>";
        }
        ?>

    </div>

</section>

<footer>
    <p>&copy; <?php echo date("Y"); ?> BookHub - Online Book Ordering System</p>
</footer>

</body>
</html>