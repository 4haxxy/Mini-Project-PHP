<?php
require 'Conn.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit;
}

$username = $_SESSION['username'];

// Get only this user's bookings
$sql = "SELECT * FROM bookings WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Bookings | Abdul Capydeng's Car Rental</title>
<style>
    :root {
        --primary: #007bff;
        --primary-dark: #0056b3;
        --accent: #f8f9fa;
        --success: #28a745;
        --danger: #dc3545;
        --warning: #ffc107;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: #f0f3f8;
        margin: 0;
        padding: 0;
        color: #333;
    }

    /* NAVIGATION BAR */
    nav {
        background: var(--primary);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 50px;
        color: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .logo {
        font-size: 20px;
        font-weight: 600;
    }

    nav ul {
        list-style: none;
        display: flex;
        gap: 30px;
        margin: 0;
        padding: 0;
    }

    nav ul li a {
        color: white;
        text-decoration: none;
        font-weight: 500;
        transition: 0.3s;
    }

    nav ul li a:hover {
        color: #dce8ff;
    }

    /* HEADER */
    .header {
        text-align: center;
        margin-top: 60px;
        color: var(--primary-dark);
    }

    .header h1 {
        margin-bottom: 5px;
        font-size: 32px;
        letter-spacing: 0.5px;
    }

    .header p {
        color: #555;
        font-size: 16px;
    }

    /* BOOKING CONTAINER */
    .bookings-container {
        width: 90%;
        max-width: 1200px;
        margin: 40px auto 100px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
    }

    /* BOOKING CARD */
    .booking-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        padding: 20px;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .booking-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }

    .booking-card h3 {
        color: var(--primary);
        margin-top: 0;
        font-size: 20px;
        margin-bottom: 10px;
    }

    .booking-card .detail {
        font-size: 15px;
        margin: 6px 0;
    }

    .detail strong {
        color: #444;
    }

    .status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        color: white;
        font-size: 13px;
        margin-top: 10px;
    }

    .confirmed { background: var(--success); }
    .pending { background: var(--warning); color: #333; }
    .cancelled { background: var(--danger); }

    /* EMPTY STATE */
    .no-bookings {
        text-align: center;
        font-size: 18px;
        color: #555;
        margin: 80px 0;
    }

    .no-bookings a {
        background: var(--primary);
        color: white;
        text-decoration: none;
        padding: 10px 18px;
        border-radius: 8px;
        margin-top: 10px;
        display: inline-block;
        transition: background 0.3s;
    }

    .no-bookings a:hover {
        background: var(--primary-dark);
    }

    /* FOOTER */
    footer {
        background: var(--primary);
        color: white;
        text-align: center;
        padding: 12px;
        font-size: 14px;
        position: fixed;
        bottom: 0;
        width: 100%;
    }

    /* RESPONSIVE NAV */
    @media (max-width: 768px) {
        nav {
            flex-direction: column;
            align-items: flex-start;
            padding: 15px 25px;
        }

        nav ul {
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
        }

        .header h1 {
            font-size: 26px;
        }
    }
</style>
</head>
<body>

<!-- NAVIGATION BAR -->
<nav>
    <div class="logo"> Abdul Capydeng's Car Rental</div>
    <ul>
        <li><a href="Home.php">Home</a></li>
        <li><a href="MyBookings.php">My Bookings</a></li>
        <li><a href="CarForm.php">Book a Car</a></li>
        <li><a href="Logout.php">Logout</a></li>
    </ul>
</nav>

<!-- HEADER -->
<div class="header">
    <h1>Welcome, <?php echo htmlspecialchars($username); ?> 👋</h1>
    <p>Your latest bookings are shown below.</p>
</div>

<!-- BOOKINGS SECTION -->
<?php if ($result->num_rows > 0): ?>
<div class="bookings-container">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="booking-card">
            <h3><?php echo htmlspecialchars($row['Vehicle_Model']); ?></h3>
            <p class="detail"><strong>Duration:</strong> <?php echo htmlspecialchars($row['Duration']); ?></p>
            <p class="detail"><strong>Start Date:</strong> <?php echo htmlspecialchars($row['Date_Start']); ?></p>
            <p class="detail"><strong>End Date:</strong> <?php echo htmlspecialchars($row['Date_End']); ?></p>
            <p class="detail"><strong>Fee:</strong> RM <?php echo htmlspecialchars($row['Fee']); ?></p>
            <p class="detail"><strong>Deposit:</strong> RM <?php echo htmlspecialchars($row['Deposit']); ?></p>
            <p class="detail"><strong>Payment:</strong> <?php echo htmlspecialchars($row['Payment']); ?></p>
            <span class="status confirmed">Confirmed</span>
        </div>
    <?php endwhile; ?>
</div>
<?php else: ?>
    <div class="no-bookings">
        <p>You haven’t made any bookings yet.</p>
        <a href="CarForm.php">+ Make a New Booking</a>
    </div>
<?php endif; ?>

<footer>
    © <?php echo date("Y"); ?> Abdul Capydeng's Car Rental. All rights reserved.
</footer>

</body>
</html>
