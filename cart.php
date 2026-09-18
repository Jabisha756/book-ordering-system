<?php
session_start();
include 'database.php';

// Create cart session if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Remove item from cart
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);

    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }

    header("Location: cart.php");
    exit();
}

// Update cart quantities
if (isset($_POST['update_cart'])) {

    if (isset($_POST['quantity'])) {

        foreach ($_POST['quantity'] as $id => $quantity) {

            $id = intval($id);
            $quantity = intval($quantity);

            if ($quantity <= 0) {
                unset($_SESSION['cart'][$id]);
            } else {
                $_SESSION['cart'][$id]['quantity'] = $quantity;
            }
        }
    }

    header("Location: cart.php");
    exit();
}

// Calculate total
$total = 0;

foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Shopping Cart - Book Store</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

        /* Navbar */
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

        /* Container */
        .container {
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th {
            background: #333;
            color: white;
            padding: 15px;
        }

        td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .book-info {
            display: flex;
            align-items: center;
            gap: 15px;
            text-align: left;
        }

        .book-info img {
            width: 70px;
            height: 90px;
            object-fit: cover;
            border-radius: 5px;
        }

        /* Quantity input */
        .quantity {
            width: 60px;
            padding: 8px;
            text-align: center;
        }

        /* Buttons */
        .btn {
            padding: 10px 18px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            font-size: 15px;
        }

        .update-btn {
            background: #007bff;
            color: white;
        }

        .checkout-btn {
            background: #28a745;
            color: white;
        }

        .continue-btn {
            background: #6c757d;
            color: white;
        }

        .remove-btn {
            background: #dc3545;
            color: white;
            padding: 8px 12px;
        }

        .btn:hover {
            opacity: 0.85;
        }

        /* Total */
        .cart-total {
            text-align: right;
            margin-top: 25px;
            font-size: 20px;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* Empty cart */
        .empty-cart {
            text-align: center;
            padding: 50px;
        }

        .empty-cart p {
            font-size: 18px;
            color: #777;
        }

        /* Responsive */
        @media(max-width: 700px) {

            .container {
                width: 95%;
                padding: 15px;
            }

            table {
                font-size: 13px;
            }

            th, td {
                padding: 8px;
            }

            .book-info img {
                width: 45px;
                height: 60px;
            }

            .book-info {
                gap: 5px;
            }

        }

    </style>

</head>

<body>


<!-- Navigation -->
<div class="navbar">

    <a href="index.php">Home</a>

    <a href="books.php">Books</a>

    <a href="cart.php">
        Cart (<?php echo count($_SESSION['cart']); ?>)
    </a>

    <?php if (isset($_SESSION['user_id'])) { ?>

        <a href="logout.php">Logout</a>

    <?php } else { ?>

        <a href="login.php">Login</a>

    <?php } ?>

</div>


<div class="container">

    <h1>My Shopping Cart</h1>


    <?php if (empty($_SESSION['cart'])) { ?>

        <!-- Empty Cart -->

        <div class="empty-cart">

            <h2>Your cart is empty!</h2>

            <p>Add some books to start shopping.</p>

            <a href="books.php" class="btn continue-btn">
                Browse Books
            </a>

        </div>


    <?php } else { ?>


        <!-- Cart Form -->

        <form method="POST">

            <table>

                <tr>
                    <th>Book</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>


                <?php foreach ($_SESSION['cart'] as $id => $item) {

                    $subtotal = $item['price'] * $item['quantity'];

                ?>

                <tr>

                    <td>

                        <div class="book-info">

                            <?php if (!empty($item['image'])) { ?>

                                <img
                                    src="images/<?php echo htmlspecialchars($item['image']); ?>"
                                    alt="<?php echo htmlspecialchars($item['title']); ?>"
                                >

                            <?php } ?>

                            <div>

                                <strong>
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </strong>

                            </div>

                        </div>

                    </td>


                    <td>
                        Rs. <?php echo number_format($item['price'], 2); ?>
                    </td>


                    <td>

                        <input
                            type="number"
                            name="quantity[<?php echo $id; ?>]"
                            value="<?php echo $item['quantity']; ?>"
                            min="0"
                            class="quantity"
                        >

                    </td>


                    <td>

                        Rs.
                        <?php echo number_format($subtotal, 2); ?>

                    </td>


                    <td>

                        <a
                            href="cart.php?remove=<?php echo $id; ?>"
                            class="btn remove-btn"
                            onclick="return confirm('Remove this book from cart?')"
                        >
                            Remove
                        </a>

                    </td>

                </tr>

                <?php } ?>


            </table>


            <!-- Cart Total -->

            <div class="cart-total">

                <strong>
                    Total: Rs. <?php echo number_format($total, 2); ?>
                </strong>

            </div>


            <!-- Buttons -->

            <div class="buttons">

                <a href="books.php" class="btn continue-btn">
                    ← Continue Shopping
                </a>


                <button
                    type="submit"
                    name="update_cart"
                    class="btn update-btn"
                >
                    Update Cart
                </button>


                <a href="checkout.php" class="btn checkout-btn">
                    Proceed to Checkout →
                </a>

            </div>

        </form>


    <?php } ?>

</div>


</body>
</html>