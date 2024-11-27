<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "individual";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result();
} else {
    header('location: home.php'); 
}
// Handle adding product to cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id']) && isset($_POST['product_quantity'])) {
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['product_quantity']);

    // Check if product already exists in cart for this user
    $userId = $_COOKIE['cookies'];  
    $checkCartQuery = "SELECT * FROM cart WHERE username = ? AND ProductId = ?";
    $checkCartStmt = $conn->prepare($checkCartQuery);
    $checkCartStmt->bind_param("si", $userId, $productId);
    $checkCartStmt->execute();
    $checkCartResult = $checkCartStmt->get_result();

    if ($checkCartResult->num_rows > 0) {
        // Product already exists in cart, update quantity
        $updateCartQuery = "UPDATE cart SET quantity = quantity + ? WHERE username = ? AND ProductId = ?";
        $updateCartStmt = $conn->prepare($updateCartQuery);
        $updateCartStmt->bind_param("isi", $quantity, $userId, $productId);
        $updateCartStmt->execute();
        $updateCartStmt->close();
    } else {
        // Product does not exist in cart, insert new entry
        $insertCartQuery = "INSERT INTO cart (username, ProductId, quantity) VALUES (?, ?, ?)";
        $insertCartStmt = $conn->prepare($insertCartQuery);
        $insertCartStmt->bind_param("sii", $userId, $productId, $quantity);
        $insertCartStmt->execute();
        $insertCartStmt->close();
    }
    // Redirect to product page after adding to cart
    header("Location: cart.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="css/style1.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-body-tertiary py-3">
    <div class="container">
        <img class="logo" src="image/logo.jpeg" width="40px" height="40px">
        <a class="navbar-brand" href="#">Daily Farm</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse nav-buttons" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="home.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="shop.php">Shop</a>
                </li>           
                <li class="nav-item">
                    <a href="logout.php" class="btn btn-warning">Logout</a>
                </li>
                <li class="nav-item">
                    <a href="cart.php" class="nav-link"><i class="fa-solid fa-cart-shopping"></i></a>
                    <a href="account.php" class="nav-link"><i class="fa-solid fa-user"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section class="single-product my-5 pt-5">
    <div class="container">
        <div class="row">
            <?php while($row = $product->fetch_assoc()){ ?>
            <div class="col-md-6">
                <img class="img-fluid" src="image/<?php echo $row['product_image']; ?>" alt="<?php echo $row['product_name']; ?>">
            </div>
            <div class="col-md-6">
                <h2 class="mb-4"><?php echo $row['product_name']; ?></h2>
                <h4 class="mb-3">Category: <?php echo $row['product_category']; ?></h4>
                <div class="col-md-6">
                    <h4 class="mb-3">Description:</h4>
                    <p><?php echo $row['product_description']; ?></p>
                </div>
                <h3 class="mb-4">Price: RM<?php echo $row['product_price']; ?></h3>
                <form action="" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                    <input type="hidden" name="product_image" value="<?php echo $row['product_image']; ?>">
                    <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>">
                    <input type="hidden" name="product_price" value="<?php echo $row['product_price']; ?>">
                    <div class="mb-3">
                        <label for="product_quantity" class="form-label">Quantity:</label>
                        <input type="number" class="form-control" id="product_quantity" name="product_quantity" value="1" min="1">
                    </div>

                    <button class="btn btn-primary mt-3" type="submit" name="add_to_cart">Add To Cart</button>
                </form>
            </div>
            <?php } ?>
        </div>
    </div>
</section>

<footer class="bg-dark text-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <img class="logo" src="image/logo.jpeg" width="40px" height="40px">
                <p class="pt-3">We provide the best products for the most affordable prices</p>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="pb-2">Contact us</h5>
                <p>1234, Jalan Nilai</p>
                <p>Phone: 1235677</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-center">
                <p>eCommerce @ 2024 ALL Right Reserved</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://kit.fontawesome.com/d05c3f029e.js" crossorigin="anonymous"></script>
</body>
</html>
