<?php
session_start();
if (!isset($_COOKIE['cookies'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "individual";
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle adding product to cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

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
    header("Location: product.php");
    exit();
}

// Handle search form submission and update search count
$searchQuery = isset($_GET['search_query']) ? $_GET['search_query'] : '';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'visit_count';
$searchTerm = "%" . $searchQuery . "%";

// Check if the search button was clicked or the clear search results button was clicked
$searchSubmitted = isset($_GET['search_submitted']) ? intval($_GET['search_submitted']) : 0;
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$sql = "SELECT * FROM products";

if (!empty($searchQuery)) {
    $sql .= " WHERE product_name LIKE ?";
}

if ($category != 'all') {
    // If there's already a WHERE clause, use AND, otherwise start a new WHERE clause
    $sql .= (empty($searchQuery) ? " WHERE" : " AND") . " product_category = ?";
}

$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'product_id';
$sql = "SELECT * FROM products";

// Update the SQL query to order by the selected column
$sql .= " ORDER BY $sortBy DESC";

$stmt = $conn->prepare($sql);

// Bind parameters if necessary
if (!empty($searchQuery)) {
    $stmt->bind_param("s", $searchTerm);
}

if ($category != 'all') {
    $stmt->bind_param("s", $category);
}

$stmt->execute();
$result = $stmt->get_result();


// Increment search count or visit count when a product is clicked
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id']) && isset($_POST['searchQuery'])) {
    $searchQuery = $_POST['searchQuery']; // Receive the searchQuery parameter

    $productId = intval($_POST['product_id']);

    // Increment search count for the clicked product if search query exists, otherwise increment visit count
    if (!empty($searchQuery)) {
        $sql = "UPDATE products SET search_count = search_count + 1 WHERE product_id = ?";
    } else {
        $sql = "UPDATE products SET visit_count = visit_count + 1 WHERE product_id = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    if (!$stmt->execute()) {
        error_log("Error updating count: " . $stmt->error);
    }
    $stmt->close();
    
    // Redirect to the single product page with the updated product ID
    header("Location: single_product.php?product_id=" . $productId);
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Listing</title>
    <style>
               body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .navbar {
            background-color: #333;
            overflow: hidden;
        }
        .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .product-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }
        .product {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
            width: 200px;
            display: inline-block;
            text-align: center;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .product img {
            width: 150px;
            height: 150px;
            margin-bottom: 10px;
            cursor: pointer; 
        }
        .product-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .product-price {
            color: green;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .product-form {
            margin-top: 10px;
        }
        .product-form input[type="number"] {
            width: 50px;
            padding: 5px;
            margin-right: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .product-form button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .product-form button:hover {
            background-color: #45a049;
        }

        .modal {
          display: none; 
          position: fixed; 
          z-index: 1;
          left: 0;
          top: 0;
          width: 100%; 
          height: 100%; 
          overflow: auto; 
          background-color: rgba(0,0,0,0.4); 
        }

        .modal-content {
          background-color: #fefefe;
          margin: 15% auto; 
          padding: 20px;
          border: 1px solid #888;
          width: 80%; 
          border-radius: 8px;
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .close {
          color: #aaa;
          float: right;
          font-size: 28px;
          font-weight: bold;
        }

        .close:hover,
        .close:focus {
          color: black;
          text-decoration: none;
          cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="login.php">Sign Out</a>
        <a href="cart.php">Cart</a>
        <a href="home.php">User Profile</a>
        <a href="product.php">Product Page</a>
        <!-- Search form -->
        <form method="GET" action="product.php">
            <input type="text" name="search_query" id="search_query" placeholder="Search..." value="<?= htmlspecialchars($searchQuery) ?>" required>
            <button type="submit" onclick="setSearchSubmitted()">Search</button>
            <button type="button" onclick="clearSearchResults()">Clear Search Results</button>
        </form>
        <a href="product.php?sort=visit_count" style="float: right; padding: 14px 20px;">Most Visited</a>
        <a href="product.php?sort=order_count" style="float: right; padding: 14px 20px;">Most Ordered</a>
        <a href="product.php?sort=search_count" style="float: right; padding: 14px 20px;">Most Searched</a>
    </div>
    <div class="container">
        <form method="get" action="product.php">
            <label for="category">Select Category:</label>
            <select name="category" id="category">
                <option value="all">All</option>
                <?php
                $categorySql = "SELECT DISTINCT product_category FROM products";
                $categoryResult = $conn->query($categorySql);
                if ($categoryResult->num_rows > 0) {
                    while ($row = $categoryResult->fetch_assoc()) {
                        $selected = (isset($_GET['category']) && $_GET['category'] == $row['product_category']) ? 'selected' : '';
                        echo '<option value="' . $row['product_category'] . '" ' . $selected . '>' . $row['product_category'] . '</option>';
                    }
                }
                ?>
            </select>
            <button type="submit">Filter</button>
        </form>
    </div>
    <div class="product-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="product">';
                echo '<img src="/individual/images/' . htmlspecialchars($row['product_image']) . '" alt="' . htmlspecialchars($row['product_name']) . '" onclick="updateSearchCount(' . $row['product_id'] . ', \'' . urlencode($searchQuery) . '\');">';
                echo '<div class="product-name">' . $row['product_name'] . '</div>';
                echo '<div class="product-price">$' . $row['product_price'] . '</div>';
                echo '<form method="post" action="" class="product-form">';
                echo '<input type="hidden" name="product_id" value="' . $row['product_id'] . '">';
                echo 'Quantity: <input type="number" name="quantity" value="1" min="1">';
                echo '<button type="submit">Add to Cart</button>';
                echo '</form>';
                echo '</div>';
            }
        } else {
            echo "No products found.";
        }
        ?>
    </div>

    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p id="product_description">Product details go here.</p>
        </div>
    </div>

    <script>

        function updateSearchCount(productId, searchQuery) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "product.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    // Redirect to the single product page with the updated product ID
                    window.location.href = "single_product.php?product_id=" + productId;
                }
            };
            xhr.send("product_id=" + productId + "&searchQuery=" + searchQuery);
        }

        function setSearchSubmitted() {
            console.log("Search button clicked");
            searchSubmitted = 1; // Set searchSubmitted to 1 when the search button is clicked
            var url = new URL(window.location.href);
            url.searchParams.set("search_submitted", "1"); // Set search_submitted parameter to 1
            window.location.href = url.toString();
        }

        function clearSearchResults() {
            var url = new URL(window.location.href);
            url.searchParams.delete("search_query");
            url.searchParams.set("search_submitted", "0"); // Set search_submitted parameter to 0
            window.location.href = url.toString();
        }

    </script>
</body>
</html>
