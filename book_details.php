<?php
session_start();
include 'database.php';

// Check book ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: books.php");
    exit();
}

$book_id = intval($_GET['id']);

// Get book details
$sql = "SELECT * FROM books WHERE id = $book_id";
$result = mysqli_query($conn, $sql);

// Check if book exists
if (mysqli_num_rows($result) == 0) {
    echo "Book not found!";
    exit();
}

$book = mysqli_fetch_assoc($result);

// Add book to cart
if (isset($_POST['add_to_cart'])) {

    $quantity = intval($_POST['quantity']);

    if ($quantity < 1) {
        $quantity = 1;
    }

    // Create cart session if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // If book already exists in cart
    if (isset($_SESSION['cart'][$book_id])) {
        $_SESSION['cart'][$book_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$book_id] = [
            'id' => $book['id'],
            'title' => $book['title'],
            'price' => $book['price'],
            'image' => $book['image'],
            'quantity' => $quantity
        ];
    }

    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $book['title']; ?> - Book Store</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

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

        .container {
            width: 85%;
            max-width: 1000px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            display: flex;
            gap: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .book-image {
            width: 40%;
            text-align: center;
        }

        .book-image img {
            width: 280px;
            max-width: 100%;
            height: 380px;
            object-fit: cover;
            border-radius: 5px;
        }

        .book-info {
            width: 60%;
        }

        h1 {
            color: #333;
        }

        .price {
            color: green;
            font-size: 26px;
            font-weight: bold;
        }

        .details {
            margin: 15px 0;
            line-height: 1.8;
            font-size: 17px;
        }

        .quantity {
            padding: 10px;
            width: 60px;
            margin: 10px 0;
        }

        .cart-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 17px;
            cursor: pointer;
            border-radius: 5px;
        }

        .cart-btn:hover {
            background: #218838;
        }

        .back-btn {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        @media(max-width: 700px) {
            .container {
                flex-direction: column;
            }

            .book-image,
            .book-info {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<!-- Navigation -->
<div class="navbar">
    <a href="index.php">Home</a>
    <a href="books.php">Books</a>
    <a href="cart.php">Cart</a>

    <?php if (isset($_SESSION['user_id'])) { ?>
        <a href="logout.php">Logout</a>
    <?php } else { ?>
        <a href="login.php">Login</a>
    <?php } ?>
</div>


<!-- Book Details -->
<div class="container">

    <div class="book-image">

        <?php
        if (!empty($book['image'])) {
            echo '<img src="images/' . htmlspecialchars($book['image']) . '" alt="' . htmlspecialchars($book['title']) . '">';
        } else {
            echo '<img src="images/no-image.jpg" alt="No Image">';
        }
        ?>

    </div>


    <div class="book-info">

        <h1><?php echo htmlspecialchars($book['title']); ?></h1>

        <div class="details">
            <strong>Author:</strong>
            <?php echo htmlspecialchars($book['author']); ?>
        </div>

        <div class="details">
            <strong>Category:</strong>
            <?php echo htmlspecialchars($book['category']); ?>
        </div>

        <div class="details">
            <strong>Price:</strong>
            <span class="price">
                Rs. <?php echo number_format($book['price'], 2); ?>
            </span>
        </div>

        <div class="details">
            <strong>Description:</strong><br>
            <?php echo nl2br(htmlspecialchars($book['description'])); ?>
        </div>


        <!-- Add to Cart Form -->
        <form method="POST">

            <label><strong>Quantity:</strong></label><br>

            <input
                type="number"
                name="quantity"
                class="quantity"
                value="1"
                min="1"
            >

            <br>

            <button
                type="submit"
                name="add_to_cart"
                class="cart-btn"
            >
                Add to Cart
            </button>

        </form>

        <a href="books.php" class="back-btn">
            ← Back to Books
        </a>

    </div>

</div>

</body>
</html>