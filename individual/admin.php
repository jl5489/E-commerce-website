<?php
session_start();
if (!isset($_COOKIE['cookies']) && $_SESSION['User_type'] == "Admin") {
    header("Location: login.php");
    exit();
}
$servername = "localhost";
$username = "root";
$password = "";
$database = "individual";

$conn = new mysqli($servername, $username, $password, $database);
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Add'])) {
    include 'db_connection.php'; 
    
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $product_category = $_POST['product_category'];
    $product_stock = $_POST['product_stock'];
    
    $targetDir = "C:/xampp/htdocs/individual/images/";

    $targetFile = $targetDir . basename($_FILES["product_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));
    
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["product_image"]["tmp_name"]);
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }


    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFile)) {
            echo "The file ". htmlspecialchars( basename( $_FILES["product_image"]["name"])). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
    
    // Insert product into database
    $insertProductSql = "INSERT INTO products (product_name, product_description, product_price, product_image, product_category, product_stock) VALUES (?, ?, ?, ?, ?, ?)";
    $insertProductStmt = $conn->prepare($insertProductSql);
    $insertProductStmt->bind_param("ssdssi", $product_name, $product_description, $product_price, basename($_FILES["product_image"]["name"]), $product_category, $product_stock);
    
    if ($insertProductStmt->execute()) {
        header("Location: admin.php"); 
        exit();
    } else {
        echo "Error: " . $insertProductStmt->error;
    }

    $insertProductStmt->close();
    
    $conn->close();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Update'])) {
    
    // Get form data
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $product_category = $_POST['product_category'];
    $product_stock = $_POST['product_stock'];
    
    // Update product in database
    $updateProductSql = "UPDATE products SET product_name = ?, product_description = ?, product_price = ?, product_category = ?, product_stock = ? WHERE product_id = ?";
    $updateProductStmt = $conn->prepare($updateProductSql);
    $updateProductStmt->bind_param("ssdssi", $product_name, $product_description, $product_price, $product_category, $product_stock, $product_id);
    
    if ($updateProductStmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        echo "Error: " . $updateProductStmt->error;
    }
    $updateProductStmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Remove'])) {
    
   
    $product_id = $_POST['product_id'];

   
    $removeProductSql = "DELETE FROM products WHERE product_id = ?";
    $removeProductStmt = $conn->prepare($removeProductSql);
    $removeProductStmt->bind_param("i", $product_id);
    
    if ($removeProductStmt->execute()) {
        header("Location: admin.php"); 
        exit();
    } else {
        echo "Error: " . $removeProductStmt->error;
    }

    $removeProductStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #007bff;
            color: #fff;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        h1 {
            margin: 0;
            font-size: 28px;
        }
        .add-product-container {
            padding: 20px;
            border-bottom: 1px solid #ccc;
        }
        .add-product-container form {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .product-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }
        .product {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin: 10px;
            width: 250px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
            background-color: #fff;
        }
        .product:hover {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        .product img {
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }
        .product form {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
        }
        .product input[type="text"],
        .product textarea,
        .product select {
            margin-bottom: 5px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 14px;
        }
        .product input[type="submit"] {
            padding: 8px 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .product input[type="submit"]:hover {
            background-color: #0056b3;
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
        </style>
</head>
<body>
<div class="navbar">
        <a href="login.php">Sign Out</a>
    </div>
<div class="container">
        <h1>Admin Panel</h1>
  
    <div class="container">
        <div id="add-form" class="form-container">
            <h2>Add Product</h2>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="text" name="product_name" placeholder="Product Name" required>
                <textarea name="product_description" placeholder="Product Description" required></textarea>
                <input type="number" name="product_price" placeholder="Price" required>
                <input type="file" name="product_image" accept="image/*" required>
                <select name="product_category" id="category">
                    <?php
                    $categorySql = "SELECT DISTINCT product_category FROM products";
                    $categoryResult = $conn->query($categorySql);
                    if ($categoryResult->num_rows > 0) {
                        while ($row = $categoryResult->fetch_assoc()) {
                            $selected = ($row['product_category'] == $product_category) ? 'selected' : ''; // Assuming $product_category contains the selected category value
                            echo '<option value="' . $row['product_category'] . '" ' . $selected . '>' . $row['product_category'] . '</option>';
                        }
                    }
                    ?>
                </select>

                <input type="number" name="product_stock" placeholder="Stock" required>
                <input type="submit" name="Add" value="Add">
            </form>

    </div>

    <div class="container">
    </div>
    <div class="product-container">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="product">';
            echo '<img src="/individual/images/' . htmlspecialchars($row['product_image']) . '" alt="' . htmlspecialchars($row['product_name']) . '" onclick="showProductDetails(' . $row['product_id'] . ', \'' . htmlspecialchars($row['product_description']) . '\')">';

            echo '<form method="post" action="">';
            echo '<input type="text" name="product_id" value="' . $row['product_id'] . '" style="display: none;">';
            echo 'Name: <input type="text" name="product_name" value="' . $row['product_name'] . '"><br>';
            echo 'Description: <input type="text" name="product_description" value="' . $row['product_description'] . '"><br>';
            echo 'Price: <input type="text" name="product_price" value="' . $row['product_price'] . '"><br>';
            echo 'Category: <select name="product_category" id="category">';
            
            $categorySql = "SELECT DISTINCT product_category FROM products";
            $categoryResult = $conn->query($categorySql);
            if ($categoryResult->num_rows > 0) {
                while ($categoryRow = $categoryResult->fetch_assoc()) {
                    $selected = ($categoryRow['product_category'] == $row['product_category']) ? 'selected' : ''; // Assuming $row['product_category'] contains the selected category value
                    echo '<option value="' . $categoryRow['product_category'] . '" ' . $selected . '>' . $categoryRow['product_category'] . '</option>';
                }
            }
            echo '</select>';
            
            echo 'Stock: <input type="text" name="product_stock" value="' . $row['product_stock'] . '"><br>';
            echo '<input type="submit" name="Update" value="Update">';
            echo '</form>';

            echo '<form method="post" action="">';
            echo '<input type="text" name="product_id" value="' . $row['product_id'] . '" style="display: none;">';
            echo '<input type="submit" name="Remove" value="Remove">';
            echo '</form>';

            echo '</div>'; 
        }
    } else {
        echo "No products found.";
    }
    ?>
</div>


</body>
<footer>
the business is fictitious and part of a university course.
</footer>
</html>

