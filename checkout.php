<?php
session_start();
include 'database.php';

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: books.php");
    exit();
}

// Calculate total
$total = 0;

foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Process checkout
$message = "";

if (isset($_POST['place_order'])) {

    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));

    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($address)) {

        $message = "<div class='error'>Please fill in all fields.</div>";

    } else {

        // Save order
        $order_sql = "INSERT INTO orders 
                      (customer_name, customer_email, customer_phone, customer_address, total_amount)
                      VALUES 
                      ('$name', '$email', '$phone', '$address', '$total')";

        if (mysqli_query($conn, $order_sql)) {

            $order_id = mysqli_insert_id($conn);

            // Save each book as order item
            foreach ($_SESSION['cart'] as $item) {

                $book_id = $item['id'];
                $quantity = $item['quantity'];
                $price = $item['price'];

                $item_sql = "INSERT INTO order_items 
                             (order_id, book_id, quantity, price)
                             VALUES 
                             ('$order_id', '$book_id', '$quantity', '$price')";

                mysqli_query($conn, $item_sql);
            }

            // Clear cart
            unset($_SESSION['cart']);

            // Redirect to prevent duplicate order
            header("Location: checkout.php?success=1&order_id=$order_id");
            exit();

        } else {

            $message = "<div class='error'>
                        Error placing order: " . mysqli_error($conn) . "
                        </div>";
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

    <title>Checkout - Book Store</title>

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

        /* Container */
        .container {
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
            display: flex;
            gap: 30px;
        }

        /* Sections */
        .checkout-form,
        .order-summary {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .checkout-form {
            width: 60%;
        }

        .order-summary {
            width: 40%;
        }

        h1, h2 {
            color: #333;
        }

        /* Form */
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        /* Button */
        .order-btn {
            width: 100%;
            background: #28a745;
            color: white;
            border: none;
            padding: 14px;
            margin-top: 25px;
            font-size: 17px;
            cursor: pointer;
            border-radius: 5px;
        }

        .order-btn:hover {
            background: #218838;
        }

        /* Order summary */
        .item {
            border-bottom: 1px solid #ddd;
            padding: 12px 0;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .total {
            margin-top: 20px;
            font-size: 22px;
            font-weight: bold;
            text-align: right;
            color: green;
        }

        /* Error */
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        /* Responsive */
        @media(max-width: 768px) {

            .container {
                flex-direction: column;
            }

            .checkout-form,
            .order-summary {
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

    <a href="cart.php">
        Cart
    </a>

    <?php if (isset($_SESSION['user_id'])) { ?>

        <a href="logout.php">Logout</a>

    <?php } else { ?>

        <a href="login.php">Login</a>

    <?php } ?>

</div>


<div class="container">


    <!-- Checkout Form -->

    <div class="checkout-form">

        <h1>Checkout</h1>

        <?php echo $message; ?>

        <form method="POST">

            <label>Full Name</label>

            <input
                type="text"
                name="name"
                required
            >


            <label>Email Address</label>

            <input
                type="email"
                name="email"
                required
            >


            <label>Phone Number</label>

            <input
                type="text"
                name="phone"
                required
            >


            <label>Delivery Address</label>

            <textarea
                name="address"
                required
            ></textarea>


            <button
                type="submit"
                name="place_order"
                class="order-btn"
            >
                Place Order
            </button>

        </form>

    </div>


    <!-- Order Summary -->

    <div class="order-summary">

        <h2>Order Summary</h2>


        <?php foreach ($_SESSION['cart'] as $item) {

            $subtotal =
                $item['price'] * $item['quantity'];

        ?>

            <div class="item">

                <div>

                    <strong>
                        <?php echo htmlspecialchars($item['title']); ?>
                    </strong>

                    <br>

                    Quantity:
                    <?php echo $item['quantity']; ?>

                </div>


                <div>

                    Rs.
                    <?php echo number_format($subtotal, 2); ?>

                </div>

            </div>


        <?php } ?>


        <div class="total">

            Total:
            Rs. <?php echo number_format($total, 2); ?>

        </div>

    </div>


</div>


</body>

</html>