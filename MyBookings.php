<?php
require 'Conn.php';
session_start();

if (!isset($conn)) {
    die("Database connection failed."); 
}

if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit;
}

$username = $_SESSION['username'];

$sql = "SELECT * FROM bookings WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

function formatCurrency($value) {
    if (strpos($value, 'RM') === 0) {
        return htmlspecialchars($value);
    }
    return 'RM ' . htmlspecialchars(number_format((float)$value, 2));
}

?>

<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Bookings | Abdul Capydeng's Car Rental</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>

    :root {
        --primary: #1e40af; /* Deep Blue */
        --primary-light: #3b82f6;
        --secondary: #d1d5db; /* Light Gray */
        --background: #f8fafc; /* Very Light Blue-Gray */
        --card-bg: #ffffff;
        --text-dark: #1f2937;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--background);
        margin: 0;
        padding: 0;
        color: var(--text-dark);
        min-height: 100vh; /* Ensure footer stays at the bottom */
        padding-bottom: 70px; /* Space for fixed footer */
    }

    /* --- Navigation Bar --- */
    nav {
        background: var(--primary);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 50px;
        color: #fff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .logo { 
        font-size: 24px; 
        font-weight: 700; 
        letter-spacing: 0.5px;
        color: white;
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
        transition: color 0.3s;
        padding: 5px 0;
        border-bottom: 2px solid transparent;
    }

    nav ul li a:hover, nav ul li a.active { 
        color: #fff; 
        border-bottom: 2px solid #fff;
    }
    
    /* --- Header Section --- */
    .header { 
        text-align: center; 
        margin-top: 40px; 
        margin-bottom: 30px;
    }

    .header h1 { 
        margin-bottom: 8px; 
        font-size: 36px; 
        color: var(--primary); 
        font-weight: 800;
    }

    .header p { 
        color: #6b7280; 
        font-size: 18px; 
        font-weight: 500;
    }

    /* --- Bookings Grid --- */
    .bookings-container {
        width: 90%;
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); /* Adjusted column min-width */
        gap: 25px;
        padding-bottom: 50px;
    }

    /* --- Booking Card --- */
    .booking-card {
        background: var(--card-bg);
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        padding: 25px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-top: 5px solid var(--primary-light); /* Accent border */
    }

    .booking-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }

    .booking-card h3 { 
        color: var(--primary); 
        margin-top: 0; 
        font-size: 22px; 
        margin-bottom: 15px; 
        border-bottom: 1px solid var(--secondary);
        padding-bottom: 10px;
    }

    .booking-card .detail { 
        font-size: 16px; 
        margin: 10px 0; 
        display: flex;
        justify-content: space-between;
    }

    .detail strong { 
        color: var(--text-dark); 
        font-weight: 600;
        flex-basis: 40%; /* Space out label */
    }

    .detail span {
        flex-basis: 60%; /* Space out value */
        text-align: right;
        color: #4b5563;
    }

    .status {
        display: inline-block;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: 700;
        color: white;
        font-size: 14px;
        margin-top: 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .confirmed { background: var(--success); }
    .pending { background: var(--warning); color: var(--text-dark); }
    .cancelled { background: var(--danger); }

    /* --- No Bookings State --- */
    .no-bookings {
        text-align: center;
        margin: 100px auto;
        padding: 50px;
        background: var(--card-bg);
        border-radius: 12px;
        max-width: 600px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .no-bookings p {
        font-size: 20px;
        color: #4b5563;
        margin-bottom: 20px;
    }

    .no-bookings a {
        background: var(--primary-light);
        color: white;
        text-decoration: none;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 600;
        display: inline-block;
        transition: background 0.3s;
    }

    .no-bookings a:hover { background: var(--primary); }

    /* --- Footer --- */
    footer {
        background: var(--primary);
        color: white;
        text-align: center;
        padding: 15px;
        font-size: 14px;
        position: fixed;
        bottom: 0;
        width: 100%;
        box-shadow: 0 -4px 10px rgba(0,0,0,0.1);
        z-index: 10;
    }

    /* --- Mobile Adjustments --- */
    @media (max-width: 768px) {
        nav { flex-direction: column; align-items: center; padding: 15px 25px; }
        nav ul { gap: 20px; margin-top: 10px; }
        .header h1 { font-size: 28px; }
        .bookings-container { width: 95%; }
        .booking-card { padding: 20px; }
        .detail strong, .detail span { font-size: 15px; }
    }
</style>
</head>
<body>

<nav>
    <div class="logo"> Abdul Capydeng's Car Rental</div>
    <ul>
        <li><a href="Home.php">Home</a></li>
        <li><a href="MyBookings.php" class="active">My Bookings</a></li>
        <li><a href="CarForm.php">Book a Car</a></li>
        <li><a href="Logout.php">Logout</a></li>
    </ul>
</nav>

<div class="header">
    <h1>Your Bookings 📑</h1>
    <p>Welcome, **<?php echo htmlspecialchars($username); ?>**! Here are the details of your confirmed rentals.</p>
</div>

<?php if ($result->num_rows > 0): ?>
<div class="bookings-container">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="booking-card">
            <h3><?php echo htmlspecialchars($row['Vehicle_Model'] ?? 'N/A'); ?></h3>
            
            <div class="detail"><strong>Duration:</strong> <span><?php echo htmlspecialchars($row['Duration_Type'] ?? 'N/A'); ?></span></div>
            <div class="detail"><strong>Start Date:</strong> <span><?php echo htmlspecialchars($row['Date_Start'] ?? 'N/A'); ?></span></div>
            <div class="detail"><strong>End Date:</strong> <span><?php echo htmlspecialchars($row['Date_End'] ?? 'N/A'); ?></span></div>
            <div class="detail"><strong>Pickup Time:</strong> <span><?php echo htmlspecialchars($row['event_time'] ?? 'N/A'); ?></span></div>
            <div class="detail"><strong>Fuel Type:</strong> <span><?php echo htmlspecialchars($row['Fuel_Type'] ?? 'N/A'); ?></span></div>
            
            <div class="detail"><strong>Rental Fee:</strong> <span><?php echo formatCurrency($row['Fee'] ?? 0); ?></span></div>
            <div class="detail"><strong>Security Deposit:</strong> <span><?php echo formatCurrency($row['Deposit'] ?? 0); ?></span></div>
            <div class="detail"><strong>Payment Method:</strong> <span><?php echo htmlspecialchars($row['Payment'] ?? 'N/A'); ?></span></div>
            
            <span class="status confirmed">Confirmed</span>
        </div>
    <?php endwhile; ?>
</div>
<?php else: ?>
    <div class="no-bookings">
        <p>You haven’t made any bookings yet.</p>
        <a href="CarForm.php"> Book Your First Car Now</a>
    </div>
<?php endif; ?>

<footer>
    &copy; <?php echo date("Y"); ?> Abdul Capydeng's Car Rental. All rights reserved.
</footer>

</body>
</html>