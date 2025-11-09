<?php
require "Conn.php";
session_start();

$users = selectAllUsers();
?>

<html>
<head>
    <meta charset="UTF-8">
    <title>User List</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #a8c0ff, #3f2b96);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            color: #fff;
            margin: 30px 0 10px;
            text-align: center;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
        }

        .top-bar {
            width: 90%;
            max-width: 1000px;
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
        }

        .top-bar a {
            text-decoration: none;
            background: #3f2b96;
            color: #fff;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: 0.3s;
        }

        .top-bar a:hover { background: #5d4ab2; }

        table {
            width: 90%;
            max-width: 1000px;
            border-collapse: collapse;
            background: rgba(255,255,255,0.95);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }

        th {
            background: #3f2b96;
            color: #fff;
            font-weight: 600;
        }

        tr:hover {
            background: rgba(63,43,150,0.05);
        }

        .action-links a {
            text-decoration: none;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            margin-right: 5px;
            font-size: 14px;
            transition: 0.3s;
        }

        .action-links a.edit { background: #007bff; }
        .action-links a.edit:hover { background: #0056b3; }
        .action-links a.delete { background: #dc3545; }
        .action-links a.delete:hover { background: #a71d2a; }

        @media(max-width:768px){
            table { width: 100%; }
            th, td { padding: 12px 10px; }
            .top-bar { flex-direction: column; gap: 10px; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <h2>User List</h2>

    <div class="top-bar">
        <a href="Logout.php">Logout</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Action</th>
        </tr>

        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="action-links">
                        <a href="edit.php?id=<?php echo $user['id']; ?>" class="edit">Edit</a>
                        <a href="delete.php?id=<?php echo $user['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" style="text-align:center;">No users found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>