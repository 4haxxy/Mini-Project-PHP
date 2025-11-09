<?php
require 'Conn.php';

if (!isset($_GET['id'])) {
    echo "User ID not provided.";
    exit;
}

$id = $_GET['id'];
$user = selectUserByID($id);

if (!$user) {
    echo "User not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    updateByID($id, $username, $email, $password);
    header('Location: Index.php');
    exit;
}
?>

<html>
<head>
<meta charset="UTF-8">
<title>Edit User</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #74b9ff, #0057b3);
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
    }

    .card {
        background: #fff;
        border-radius: 16px;
        padding: 35px 40px;
        margin: 50px 0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        width: 90%;
        max-width: 480px;
        transition: transform 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    h2 {
        text-align: center;
        color: #0057b3;
        margin-bottom: 25px;
        font-weight: 600;
    }

    label {
        display: block;
        margin-bottom: 6px;
        color: #333;
        font-weight: 500;
    }

    input {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 18px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 14px;
        transition: all 0.3s;
    }

    input:focus {
        border-color: #0057b3;
        box-shadow: 0 0 8px rgba(0,87,179,0.3);
        outline: none;
    }

    .buttons {
        display: flex;
        justify-content: space-between;
        gap: 12px;
    }

    .btn-primary, .btn-cancel {
        flex: 1;
        padding: 12px 0;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #0057b3;
        color: #fff;
    }

    .btn-primary:hover {
        background: #003d80;
    }

    .btn-cancel {
        background: #b0b0b0;
        color: #fff;
        text-align: center;
        display: block;
        text-decoration: none;
        line-height: 32px;
    }

    .btn-cancel:hover {
        background: #8a8a8a;
    }

    @media(max-width:500px){
        .buttons { flex-direction: column; }
    }
</style>
</head>
<body>
    <div class="card">
        <h2>Edit User</h2>
        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>Password</label>
            <input type="text" name="password" value="<?php echo htmlspecialchars($user['password']); ?>" required>

            <div class="buttons">
                <button type="submit" class="btn-primary">Update</button>
                <a href="Index.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>