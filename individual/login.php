<?php
session_start();

if (isset($_COOKIE['cookies'])) {
    setcookie('cookies', '', time() - 3600, "/");
    session_unset();
    session_destroy();
    $_SESSION['username'] = null;
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "individual";

$conn = new mysqli($servername, $username, $password, $database);

$formToShow = isset($_GET['action']) && $_GET['action'] == 'register' ? 'register' : 'login';
$message = "";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($formToShow == 'login') {
        if (!empty($_POST['txtLUsername']) && !empty($_POST['txtLPassword'])) {
            $username = $_POST['txtLUsername'];
            $password = $_POST['txtLPassword'];

            if (validateLogin($conn, $username, $password)) {
                // Set cookies
                $cookie_name = "cookies";
                $cookie_value = $username;
                setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 30 days expiration

                $_SESSION['username'] = $username;

                if ($_SESSION['User_type'] == "Admin") {
                    header("Location: admin.php");
                    exit();
                } else {
                    header("Location: home.php");
                    exit();
                }
            } else {
                $message = "Invalid username or password.";
            }
        } else {
            $message = "Please fill in all information.";
        }
    }
    elseif ($formToShow == 'register') {
        if(!empty($_POST['txtRUsername']) && !empty($_POST['txtRPassword']) && !empty($_POST['txtCRPassword'])){
            $username = $_POST['txtRUsername'];
            $password = $_POST['txtRPassword'];
            $cpassword = $_POST['txtCRPassword'];

            if($_POST['txtRPassword']===$_POST['txtCRPassword']){
                $query = "SELECT * FROM Account WHERE username = ?";
                echo "Username: " . $username;
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0){
                    $message = "Username has already been used. Please choose another.";
                }
                else{
                    $query = "INSERT INTO Account (username, password) VALUES (?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ss", $username, $password);
                    $stmt->execute();
                    echo '<script>
                    if (confirm("Register successful!")) {
                        window.location.href = "login.php"; // Redirect to product page
                    }else {
                        window.location.href = "login.php"; // Redirect to product page if cancel or close is clicked
                    }
                  </script>';
                    exit();
                }
            }
            else{
                $message = "Please comfirm your password.";

            }
        }
        else{
            $message = "Please fill in all information.";
        }
        
    }

    
    
}


function validateLogin($conn, $username, $password) {
    // Combine the query to check both user and admin in one go
    $query = "SELECT User_type FROM Account WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();

    // Check if we got a result
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userType);
        $stmt->fetch();

        // Store user type in session
        $_SESSION['User_type'] = $userType;

        $stmt->close();
        return true; // Login successful
    }

    $stmt->close();
    return false; // Login failed
}

function toggleForm($currentForm) {
    $toggle = $currentForm == 'login' ? 'register' : 'login';
    return [
        'action' => "?action=$toggle",
        'buttonText' => $toggle == 'login' ? 'Login' : 'Register'
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($formToShow); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }
        h1 {
            text-align: center;
            margin-top: 50px;
        }
        .form-container {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container table {
            width: 100%;
        }
        .form-container input[type="text"],
        .form-container input[type="password"] {
            width: calc(100% - 10px);
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-container button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .form-container button[type="submit"]:hover {
            background-color: #0056b3;
        }
        .form-container button[type="button"] {
            width: 100%;
            padding: 10px;
            background-color: #f2f2f2;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .form-container button[type="button"]:hover {
            background-color: #e0e0e0;
        }
        .message {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #ff0000;
        }
    </style>
</head>
<body>
    <h1><?php echo ucfirst($formToShow); ?></h1>
    <div class="form-container">
        <?php if ($formToShow == 'login'): ?>
            <!-- Login Form -->
            <form method="post" action="?action=login">
                <div>
                    <table id="TableLogin" class="TableIn" width="360px">
                        <tr>
                            <td>UserName :</td>
                            <td>
                                <input type="text" id="txtLUsername" name="txtLUsername" />
                            </td>
                        </tr>
                        <tr>
                            <td>Password :</td>
                            <td>
                                <input type="password" id="txtLPassword" name="txtLPassword" />
                            </td>
                        </tr>
                    </table>
                </div>
                <button type="submit">Login</button>
            </form>
        <?php else: ?>
            <!-- Registration Form -->
            <form method="post" action="?action=register">
                <div>
                    <table id="TableRegister" class="TableRe" width="360px">
                        <tr>
                            <td>UserName :</td>
                            <td>
                                <input type="text" id="txtRUsername" name="txtRUsername" />
                            </td>
                        </tr>
                        <tr>
                            <td>Password :</td>
                            <td>
                                <input type="password" id="txtRPassword" name="txtRPassword" />
                            </td>
                        </tr>
                        <tr>
                            <td>Confirm Password :</td>
                            <td>
                                <input type="password" id="txtCRPassword" name="txtCRPassword" />
                            </td>
                        </tr>
                    </table>
                </div>
                <button type="submit">Register</button>
            </form>
        <?php endif; ?>
        <?php $toggle = toggleForm($formToShow); ?>
        <a href="<?php echo $toggle['action']; ?>">
            <button type="button"><?php echo $toggle['buttonText']; ?></button>
        </a>
    </div>
    <span class="message"><?php echo $message; ?></span>
</body>
</html>
