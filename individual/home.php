<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "individual";
$conn = new mysqli($servername, $username, $password, $database);
// Check if user is logged in, otherwise redirect to login page
if (!isset($_COOKIE['cookies'])) {
    header("Location: login.php");
    exit();
}

// Fetch user information
$userInfo = getUserInfo($conn);

function getUserInfo($conn) {
    $userId = $_COOKIE['cookies']; // Assuming this is correctly set and secured
    $sql = "SELECT * FROM Account WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row; // Return the user data
    } else {
        return false; // Return false if no user found
    }
}

// Handle form submission for updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    updateUserData($conn);
    // Refresh page after update
    header("Location: home.php");
    exit();
}

// Handle form submission for password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    changePassword($conn);
}

function updateUserData($conn) {
    // Retrieve form data
    $email = $_POST['email'];
    $firstName = $_POST['FirstName'];
    $lastName = $_POST['LastName'];
    $gender = $_POST['Gender'];
    $birthdate = $_POST['Birthdate'];
    $address = $_POST['Address'];
    $phoneNumber = $_POST['PhoneNumber'];
    // Assuming $userId is stored in a cookie or session, and safely retrieved
    $userId = $_COOKIE['cookies']; // Example, ensure this is securely handled

    // Correct SQL with placeholders and matching number of parameters
    $sql = "UPDATE Account SET Email = ?, FirstName = ?, LastName = ?, Gender = ?, Birthdate = ?, Address = ?, PhoneNumber = ? WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters and execute
        $stmt->bind_param("ssssssss", $email, $firstName, $lastName, $gender, $birthdate, $address, $phoneNumber, $userId);
        if ($stmt->execute()) {
            echo "<script>alert('Record updated successfully.');</script>";
        } else {
            echo "<script>alert('Error updating record: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Error preparing statement: " . $conn->error . "');</script>";
    }
}

function changePassword($conn) {
    $userId = $_COOKIE['cookies']; // Example, ensure this is securely handled
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmNewPassword = $_POST['confirm_new_password'];

    // Check if new password matches the confirmation
    if ($newPassword !== $confirmNewPassword) {
        echo "<script>alert('New password and confirm password do not match.');</script>";
        return;
    }

    // Fetch the current password hash from the database
    $sql = "SELECT password FROM Account WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['password'];

        echo "Entered Current Password: " . $currentPassword . "<br>";
        echo "Stored Password: " . $storedPassword . "<br>";

        // Verify if the current password matches the one stored in the database
        if ($currentPassword === $storedPassword) {
            // If the current password matches, update the password with the new one
            $updateSql = "UPDATE Account SET password = ? WHERE username = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("ss", $newPassword, $userId);
            if ($updateStmt->execute()) {
                echo "<script>alert('Password updated successfully.');</script>";
            } else {
                echo "<script>alert('Error updating password: " . $updateStmt->error . "');</script>";
            }
        } else {
            echo "<script>alert('Current password is incorrect.');</script>";
            // Additional debugging output
            echo "Password verification failed.";
        }
    } else {
        echo "<script>alert('User not found.');</script>";
        // Additional debugging output
        echo "User not found.";
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        /* CSS styles for the navigation bar */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            overflow: hidden;
            background-color: #333;
            padding: 10px 0;
        }
        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        .navbar .right {
            float: right;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2, h4 {
            text-align: center;
        }
        hr {
            margin-top: 10px;
            margin-bottom: 20px;
            border: 0;
            border-top: 1px solid #ddd;
        }
        #imgProfile {
            display: block;
            margin: 0 auto;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ccc;
        }
        #panProfile {
            margin-top: 20px;
        }
        table {
            width: 100%;
        }
        table td {
            padding: 10px 0;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:nth-child(odd) {
            background-color: #fff;
        }
        table tr:last-child {
            border-bottom: 1px solid #ddd;
        }
        table tr:hover {
            background-color: #f2f2f2;
        }
        input[type="text"], input[type="email"], input[type="tel"], select {
            width: calc(100% - 20px);
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        span {
            display: block;
            margin-bottom: 10px;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>
    <!-- Your navigation bar -->
    <div class="navbar">
        <a href="product.php">Product</a>
        <a href="cart.php" class="right">Cart</a>
        <a href="logout.php" class="right">Logout</a>
    </div>

    <!-- User information -->
    <div class="container">
        <h1>Welcome, <?php echo $userInfo['FirstName'] . ' ' . $userInfo['LastName']; ?></h1>
        <hr>
        <h2>User Information</h2>
        <form method="POST">
            <p><strong>Email:</strong> <input type="email" name="email" value="<?php echo $userInfo['Email']; ?>"></p>
            <p><strong>First Name:</strong> <input type="text" name="FirstName" value="<?php echo $userInfo['FirstName']; ?>"></p>
            <p><strong>Last Name:</strong> <input type="text" name="LastName" value="<?php echo $userInfo['LastName']; ?>"></p>
            <p><strong>Gender:</strong>
                <select name="Gender">
                    <option value="Male" <?php echo ($userInfo['Gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($userInfo['Gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                </select>
            </p>
            <p><strong>Birthdate:</strong> <input type="date" name="Birthdate" value="<?php echo $userInfo['Birthdate']; ?>"></p>
            <p><strong>Address:</strong> <input type="text" name="Address" value="<?php echo $userInfo['Address']; ?>"></p>
            <p><strong>Phone Number:</strong> <input type="tel" name="PhoneNumber" value="<?php echo $userInfo['PhoneNumber']; ?>"></p>
            <input type="submit" name="update" value="Update">
            
        </form>
                <!-- Change password form -->
                <hr>
        <h4>Change Password</h4>
        <form method="post">
            <input type="password" name="current_password" placeholder="Current Password" required><br>
            <input type="password" name="new_password" placeholder="New Password" required><br>
            <input type="password" name="confirm_new_password" placeholder="Confirm New Password" required><br>
            <input type="submit" name="change_password" value="Change Password">
        </form>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
