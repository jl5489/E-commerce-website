<?php
session_start();

// Check if user is logged in
if (!isset($_COOKIE['cookies'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "individual";
$totalPrice = 0;
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle rating submission
function submitRating($orderId, $productId, $rating) {
    global $conn;

    $updateRatingSql = "UPDATE order_products SET score = ? WHERE order_id = ? AND product_id = ?";
    $updateRatingStmt = $conn->prepare($updateRatingSql);
    $updateRatingStmt->bind_param("iii", $rating, $orderId, $productId);
    $updateRatingStmt->execute();

    if ($updateRatingStmt->affected_rows > 0) {
        echo "Rating submitted successfully!";
    } else {
        echo "Error submitting rating. Please try again later.";
    }
}

// Check if the rating submission AJAX request is made
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'], $_POST['product_id'], $_POST['rating'])) {
    $orderId = $_POST['order_id'];
    $productId = $_POST['product_id'];
    $rating = $_POST['rating'];

    submitRating($orderId, $productId, $rating);
    exit();
}

// Get cart data for the logged-in user
$userId = $_SESSION['username'];
$cartSql = "SELECT cart.ProductId, cart.quantity, products.product_name, products.product_price, products.product_stock, products.product_image FROM cart INNER JOIN products ON cart.ProductId = products.product_id WHERE cart.username = ?";
$cartStmt = $conn->prepare($cartSql);
$cartStmt->bind_param("s", $userId);
$cartStmt->execute();
$cartResult = $cartStmt->get_result();

while ($row = $cartResult->fetch_assoc()) {
    $totalPrice += $row['product_price'] * $row['quantity'];
}
mysqli_data_seek($cartResult, 0);

// Handle adding items to cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_cart'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $checkStockSql = "SELECT product_stock FROM products WHERE product_id = ?";
    $checkStockStmt = $conn->prepare($checkStockSql);
    $checkStockStmt->bind_param("i", $productId);
    $checkStockStmt->execute();
    $checkStockResult = $checkStockStmt->get_result();
    $row = $checkStockResult->fetch_assoc();
    $stock = $row['product_stock'];

    if ($quantity > $stock) {
        $error = "Selected quantity exceeds available stock!";
    } else {
        $updateCartSql = "UPDATE cart SET quantity = ? WHERE username = ? AND ProductId = ?";
        $updateCartStmt = $conn->prepare($updateCartSql);
        $updateCartStmt->bind_param("isi", $quantity, $userId, $productId);
        $updateCartStmt->execute();
        header("Location: cart.php");
        exit();
    }
}

// Handle removing items from cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_cart'])) {
    $productId = $_POST['remove_product_id'];

    $removeCartSql = "DELETE FROM cart WHERE username = ? AND ProductId = ?";
    $removeCartStmt = $conn->prepare($removeCartSql);
    $removeCartStmt->bind_param("si", $userId, $productId);
    $removeCartStmt->execute();
    header("Location: cart.php");
    exit();
}

// Inside the checkout block
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checkout'])) {
    // Insert the order
    $insertOrderSql = "INSERT INTO orders (username, order_date) VALUES (?, NOW())";
    $insertOrderStmt = $conn->prepare($insertOrderSql);
    $insertOrderStmt->bind_param("s", $userId);
    $insertOrderStmt->execute();

    // Check if the order was inserted successfully
    if ($insertOrderStmt->affected_rows > 0) {
        $orderId = $insertOrderStmt->insert_id; // Get the ID of the newly inserted order
        
        // Insert order products
        $insertOrderProductSql = "INSERT INTO order_products (order_id, product_id, quantity) VALUES (?, ?, ?)";
        $insertOrderProductStmt = $conn->prepare($insertOrderProductSql);

        // Iterate over cart items and insert into order_products
        while ($row = $cartResult->fetch_assoc()) {
            $productId = $row['ProductId'];
            $quantity = $row['quantity'];
    
            $insertOrderProductStmt->bind_param("iii", $orderId, $productId, $quantity);
            $insertOrderProductStmt->execute();
        }

        // Clear the cart after successful checkout
        $removeCartSql = "DELETE FROM cart WHERE username = ?";
        $removeCartStmt = $conn->prepare($removeCartSql);
        $removeCartStmt->bind_param("s", $userId);
        $removeCartStmt->execute();

        // Redirect to cart page or show success message
        echo '<script>
            if (confirm("Checkout successful!")) {
                window.location.href = "cart.php"; // Redirect to cart page
            }
          </script>';
    } else {
        // Handle case where order insertion failed
        echo '<script>
            alert("Error: Checkout failed. Please try again later.");
            window.location.href = "cart.php"; // Redirect to cart page
          </script>';
    }
}

$orderHistorySql = "SELECT o.order_id, o.order_date, op.quantity, p.product_name, p.product_image, p.product_price, p.product_id, op.score
                    FROM orders o
                    INNER JOIN order_products op ON o.order_id = op.order_id
                    INNER JOIN products p ON op.product_id = p.product_id
                    WHERE o.username = ?";

$orderHistoryStmt = $conn->prepare($orderHistorySql);
$orderHistoryStmt->bind_param("s", $userId);
$orderHistoryStmt->execute();
$orderHistoryResult = $orderHistoryStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <style>
        .navbar {
            background-color: #333;
            overflow: hidden;
        }

        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }

        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Form styles */
        form {
            margin-bottom: 20px;
        }

        input[type="number"] {
            width: 60px;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        a {
            color: blue;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Rating styles */
        .rating .star {
            display: inline-block;
            position: relative;
            width: 1.1em;
            cursor: pointer; /* Add cursor pointer */
        }

        .rating .star:hover::before,
        .rating .star:hover ~ .star::before {
            content: "\2606"; /* Empty star */
            position: absolute;
        }

        .rating .selected-rating::before,
        .rating .star:hover::before,
        .rating .star:hover ~ .star:hover::before {
            content: "\2605"; /* Filled star */
            position: absolute;
        }
    </style>
</head>
<body>
<div class="navbar">
    <a href="login.php">Sign Out</a>
    <a href="cart.php">Cart</a>
    <a href="home.php">User Profile</a>
    <a href="product.php">Product Page</a>
</div>
<h2>Cart</h2>
<?php if ($cartResult->num_rows > 0): ?>
    <table>
        <thead>
        <tr>
            <th>Product Image</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total</th>
            <th>Delivery or Collection</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $cartResult->fetch_assoc()): ?>
            <tr>
                <td><img src="/individual/images/<?php echo $row['product_image']; ?>" alt="<?php echo $row['product_name']; ?>" width="100"></td>
                <td><?php echo $row['product_name']; ?></td>
                <td>
                    <form method="post" action="">
                        <input type="hidden" name="product_id" value="<?php echo $row['ProductId']; ?>">
                        <input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" style="width: 60px;">
                        <button type="submit" name="update_cart">Update</button>
                    </form>
                </td>
                <td><?php echo $row['product_price']; ?></td>
                <td><?php echo $row['product_price'] * $row['quantity']; ?></td>
                <td>
                    <select name="delivery_or_collection[]">
                        <option value="delivery">Delivery</option>
                        <option value="collection">Collection</option>
                    </select>
                </td>
                <td>
                    <form method="post" action="">
                        <input type="hidden" name="remove_product_id" value="<?php echo $row['ProductId']; ?>">
                        <button type="submit" name="remove_cart">Remove</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <form method="post" action="">
        <?php echo "Total Price: $" . number_format($totalPrice, 2);?>
        <button type="submit" name="checkout">Proceed to Checkout</button>
    </form>
<?php else: ?>
    <p>Your cart is empty.</p>
<?php endif; ?>

<h2>Order History</h2>
<?php

$currentOrderId = null; // Variable to store the current order ID
if ($orderHistoryResult->num_rows > 0): ?>
    <table>
        <thead>
        <tr>
            <th>Order ID</th>
            <th>Order Date</th>
            <th>Product Image</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total Price</th>
            <th>Rating</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $orderHistoryResult->fetch_assoc()): ?>
            <?php if ($currentOrderId !== $row['order_id']): // Check if this row is from a new order ?>
                <?php $currentOrderId = $row['order_id']; ?>
                <tr>
                    <td colspan="8"><strong>Order #<?php echo $currentOrderId; ?></strong></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td><?php echo $row['order_id']; ?></td>
                <td><?php echo $row['order_date']; ?></td>
                <td><img src="/individual/images/<?php echo $row['product_image']; ?>" alt="<?php echo $row['product_name']; ?>" width="100"></td>
                <td><?php echo $row['product_name']; ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo $row['product_price']; ?></td>
                <td><?php echo $row['quantity'] * $row['product_price']; ?></td> <!-- Total Price -->
                <td>
                    <form method="post" action="">
                        <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                        <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                        <div class="rating" data-orderid="<?php echo $row['order_id']; ?>" data-productid="<?php echo $row['product_id']; ?>" data-rating="<?php echo $row['score']; ?>">
                            <?php
                            $score = $row['score'];
                            for ($i = 1; $i <= 5; $i++) {
                                $class = ($i <= $score) ? 'star selected-rating' : 'star';
                                echo "<span class='$class'>☆</span>";
                            }
                            ?>
                        </div>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No order history found.</p>
<?php endif; ?>

<a href="product.php">Continue Shopping</a>
</body>
<footer>
    The business is fictitious and part of a university course.
</footer>
<script>
// Function to handle the rating click event
function handleRatingClick(event) {
    var ratingElement = event.target.closest('.rating');
    var stars = ratingElement.querySelectorAll('.star');
    var selectedRating = ratingElement.querySelector('.selected-rating');
    var orderId = ratingElement.dataset.orderid;
    var productId = ratingElement.dataset.productid;
    var previousRating = ratingElement.dataset.rating;

    // Check if the product has already been rated
    if (previousRating !== '0' && previousRating !== 'null') {
        alert('You have already rated this product.');
        return;
    }

    // Remove the 'selected-rating' class from all stars
    stars.forEach(function(star) {
        star.classList.remove('selected-rating');
    });

    // Add the 'selected-rating' class to the clicked star and its previous siblings
    var rating = Array.from(stars).indexOf(event.target) + 1;
    Array.from(stars).slice(0, rating).forEach(function(star) {
        star.classList.add('selected-rating');
    });

    var confirmRating = confirm("Do you want to rate this product?");
    if (confirmRating) {
        // Submit the rating via AJAX
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "cart.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    alert(xhr.responseText);
                } else {
                    alert("Error submitting rating. Please try again later.");
                }
            }
        };
        xhr.send("order_id=" + encodeURIComponent(orderId) + "&product_id=" + encodeURIComponent(productId) + "&rating=" + encodeURIComponent(rating));
    }
}

// Attach event listener to all rating stars
var ratingStars = document.querySelectorAll('.rating .star');
ratingStars.forEach(function(star) {
    star.addEventListener('click', handleRatingClick);
});
</script>

</html>

<?php
$conn->close();
?>
