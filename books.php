<?php
session_start();
include 'database.php';

$search = "";

// Search books
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM books
         WHERE title LIKE ? OR author LIKE ?
         ORDER BY id DESC"
    );

    $searchTerm = "%" . $search . "%";

    mysqli_stmt_bind_param($stmt, "ss", $searchTerm, $searchTerm);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Books | BookHub</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        /* Header */
        header {
            background: #2c3e50;
            color: white;
            padding: 15px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 25px;
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

        /* Main */
        .container {
            padding: 40px 7%;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
        }

        /* Search */
        .search-box {
            text-align: center;
            margin: 30px 0;
        }

        .search-box input {
            width: 400px;
            max-width: 70%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .search-box button {
            padding: 12px 20px;
            background: #f39c12;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-box button:hover {
            background: #d35400;
        }

        /* Books Grid */
        .book-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
        }

        /* Book Card */
        .book-card {
            background: white;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: 0.3s;
        }

        .book-card:hover {
            transform: translateY(-5px);
        }

        .book-card img {
            width: 100%;
            height: 260px;
            object-fit: cover;
            border-radius: 5px;
        }

        .book-card h3 {
            color: #2c3e50;
            margin: 15px 0 8px;
        }

        .author {
            color: #777;
        }

        .price {
            color: #e67e22;
            font-size: 18px;
            font-weight: bold;
        }

        .btn {
            display: inline-block;
            background: #f39c12;
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 8px;
        }

        .btn:hover {
            background: #d35400;
        }

        .no-books {
            text-align: center;
            grid-column: 1 / -1;
            font-size: 18px;
        }

        /* Mobile */
        @media (max-width: 600px) {

            header {
                flex-direction: column;
                gap: 15px;
            }

            nav a {
                margin: 0 8px;
            }

            .search-box input {
                width: 100%;
                max-width: 100%;
                margin-bottom: 10px;
            }

            .search-box button {
                width: 100%;
            }

        }

    </style>
</head>

<body>

<header>

    <div class="logo">
        📚 BookHub
    </div>

    <nav>
        <a href="index.php">Home</a>
        <a href="books.php">Books</a>
        <a href="cart.php">Cart 🛒</a>

        <?php if (isset($_SESSION['user_id'])) { ?>

            <span>
                Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>

            <a href="logout.php">Logout</a>

        <?php } else { ?>

            <a href="login.php">Login</a>
            <a href="register.php">Register</a>

        <?php } ?>

    </nav>

</header>


<div class="container">

    <h1>Our Books</h1>

    <!-- Search Form -->
    <form class="search-box" method="GET">

        <input
            type="text"
            name="search"
            placeholder="Search by book title or author..."
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <button type="submit">
            Search
        </button>

    </form>


    <div class="book-container">

        <?php if (mysqli_num_rows($result) > 0) { ?>

            <?php while ($book = mysqli_fetch_assoc($result)) { ?>

                <div class="book-card">

                    <img
                        src="images/<?php echo htmlspecialchars($book['image']); ?>"
                        alt="<?php echo htmlspecialchars($book['title']); ?>"
                    >

                    <h3>
                        <?php echo htmlspecialchars($book['title']); ?>
                    </h3>

                    <p class="author">
                        By <?php echo htmlspecialchars($book['author']); ?>
                    </p>

                    <p class="price">
                        Rs. <?php echo number_format($book['price'], 2); ?>
                    </p>

                    <a
                        href="book_details.php?id=<?php echo $book['id']; ?>"
                        class="btn"
                    >
                        View Details
                    </a>

                </div>

            <?php } ?>

        <?php } else { ?>

            <p class="no-books">
                No books found.
            </p>

        <?php } ?>

    </div>

</div>

</body>
</html>