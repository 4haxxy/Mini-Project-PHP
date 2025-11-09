<?php
require 'Conn.php';
session_start();

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error_message = "Please enter both username and password.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['email'] = $user['email'];
                $success_message = "Login successful! Redirecting...";
                header("refresh:2;url=Home.php");
            } else {
                $error_message = "Invalid password. Please try again.";
            }
        } else {
            $error_message = "Username not found. Please register first.";
        }

        $stmt->close();
    }
}
?>


<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Capydeng Car Rental - Login</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }

body {
    background: linear-gradient(135deg,#0a2540,#07407a);
    height: 100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.login-card {
    background:white;
    border-radius:20px;
    padding:40px 35px;
    width:350px;
    box-shadow:0 15px 30px rgba(0,0,0,0.25);
    text-align:center;
    position: relative;
}

.login-card h2 {
    font-size:28px;
    color:#0a2540;
    margin-bottom:25px;
    font-weight:700;
}

input[type="text"], input[type="password"] {
    width:100%;
    padding:12px 14px;
    margin-bottom:20px;
    border-radius:8px;
    border:1px solid #ccc;
    transition:0.3s;
}

input:focus {
    border-color:#ff8c00;
    box-shadow:0 0 8px rgba(255,140,0,0.3);
    outline:none;
}

button {
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background: linear-gradient(90deg,#ff8c00,#ff6600);
    color:white;
    font-weight:600;
    font-size:16px;
    cursor:pointer;
    transition:0.3s;
}

button:hover {
    background: linear-gradient(90deg,#ff6600,#ff4500);
}

.php-message {
    padding:12px;
    border-radius:8px;
    margin-bottom:15px;
    font-weight:600;
}

.php-error {
    background:#f8d7da;
    color:#721c24;
    border:1px solid #f5c6cb;
}

.php-success {
    background:#d4edda;
    color:#155724;
    border:1px solid #c3e6cb;
}

.extra-links {
    margin-top:15px;
    font-size:0.9em;
}

.extra-links a {
    color:#ff8c00;
    text-decoration:none;
    font-weight:600;
}

.extra-links a:hover {
    text-decoration:underline;
}


</style>
</head>
<body>

<div class="login-card">
    <h2>Abdul Capydeng's Login</h2>

    <?php
    if (!empty($error_message)) {
        echo '<div class="php-message php-error">' . htmlspecialchars($error_message) . '</div>';
    } elseif (!empty($success_message)) {
        echo '<div class="php-message php-success">' . htmlspecialchars($success_message) . '</div>';
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <input type="text" name="username" placeholder="Enter Username" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit">Login</button>
    </form>

    <div class="extra-links">
        <p>Don’t have an account? <a href="Signup.php">Sign up here</a></p>
    </div>
</div>

</body>
</html>
