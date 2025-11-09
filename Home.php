<?php
session_start();
$loggedIn = isset($_SESSION['username']);
$username = $loggedIn ? $_SESSION['username'] : 'Guest';
?>


<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Home | Abdul Capydeng's Car Rental</title>
<style>
    :root {
        --primary: #007bff;
        --primary-dark: #0056b3;
        --background: #f5f7fa;
        --text: #333;
    }

    * { box-sizing: border-box; }

    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        background: var(--background);
        color: var(--text);
    }


    nav {
        background: var(--primary);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 50px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .logo {
        font-size: 22px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    nav ul {
        list-style: none;
        display: flex;
        gap: 25px;
        margin: 0;
        padding: 0;
    }

    nav ul li a {
        color: white;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s;
    }

    nav ul li a:hover {
        color: #dce8ff;
    }


    .hero {
        background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)),
                    url('https://images.unsplash.com/photo-1502877338535-766e1452684a?auto=format&fit=crop&w=1470&q=80');
        background-size: cover;
        background-position: center;
        color: white;
        text-align: center;
        padding: 120px 20px;
    }

    .hero h1 {
        font-size: 48px;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .hero p {
        font-size: 18px;
        margin-bottom: 30px;
        color: #e6e6e6;
    }

    .btn {
        background: var(--primary);
        color: white;
        padding: 12px 25px;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn:hover {
        background: var(--primary-dark);
    }


    .about {
        text-align: center;
        padding: 70px 20px;
        background: white;
    }

    .about h2 {
        font-size: 30px;
        color: var(--primary-dark);
        margin-bottom: 20px;
    }

    .about p {
        max-width: 700px;
        margin: 0 auto;
        color: #555;
        line-height: 1.6;
    }


    .cars-section {
        padding: 60px 50px;
        background: #eef3fb;
    }

    .cars-section h2 {
        text-align: center;
        color: var(--primary-dark);
        font-size: 28px;
        margin-bottom: 40px;
    }

    .car-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
    }

    .car-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .car-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }

    .car-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .car-info {
        padding: 15px 20px;
    }

    .car-info h3 {
        color: var(--primary);
        margin-top: 0;
        font-size: 20px;
        margin-bottom: 8px;
    }

    .car-info p {
        font-size: 15px;
        color: #666;
        margin: 0 0 10px;
    }

    .car-info .btn {
        display: inline-block;
        margin-top: 5px;
    }


    footer {
        text-align: center;
        background: var(--primary);
        color: white;
        padding: 15px;
        font-size: 14px;
        margin-top: 40px;
    }


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

        .hero h1 {
            font-size: 36px;
        }

        .hero p {
            font-size: 16px;
        }
    }
</style>
</head>
<body>

<nav>
    <div class="logo"> Abdul Capydeng's Car Rental</div>
    <ul>
        <li><a href="Home.php">Home</a></li>
        <li><a href="MyBookings.php">My Bookings</a></li>
        <li><a href="CarForm.php">Book a Car</a></li>
        <?php if ($loggedIn): ?>
            <li><a href="Logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="Login.php">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

<section class="hero">
    <h1>Welcome, <?php echo htmlspecialchars($username); ?> 👋</h1>
    <p>Drive your dream car today — affordable, fast, and easy booking experience.</p>
    <a href="CarForm.php" class="btn">Book Now</a>
</section>

<section class="about">
    <h2>Why Choose Us?</h2>
    <p>
        At Abdul Capydeng’s Car Rental, we provide a wide range of cars that fit your needs —
        from budget-friendly compact cars to luxurious SUVs. Enjoy transparent pricing,
        flexible rental durations, and trusted service that makes your journey smooth.
    </p>
</section>

<section class="cars-section">
    <h2>Our Popular Cars</h2>
    <div class="car-grid">
        <div class="car-card">
            <img src="https://i.pinimg.com/1200x/eb/44/40/eb444019e3967ce0f2648143ca8b8a72.jpg" alt="Perodua Axia">
            <div class="car-info">
                <h3>Perodua Axia</h3>
                <p>Reliable sedan for city and highway driving.</p>
                <a href="CarForm.php" class="btn">Book Now</a>
            </div>
        </div>
        <div class="car-card">
            <img src="https://i.pinimg.com/736x/89/c1/21/89c121941a9a41fe872e8ce77a0f062c.jpg" alt="Perodua Myvi">
            <div class="car-info">
                <h3>Perodua Myvi</h3>
                <p>Malaysia’s favorite hatchback — stylish and efficient.</p>
                <a href="CarForm.php" class="btn">Book Now</a>
            </div>
        </div>
    <div class="car-card">
            <img src="https://i.pinimg.com/1200x/67/9d/a3/679da35624feaedb9307653204a3ba32.jpg" alt="Perodua Alza">
            <div class="car-info">
                <h3>Perodua Alza</h3>
                <p>Malaysia’s favorite MPV — spacious and versatile.</p>
                <a href="CarForm.php" class="btn">Book Now</a>
            </div>
        </div>
        <div class="car-card">
            <img src="https://i.pinimg.com/736x/23/41/30/234130a5c77e4e78c5c5638245199da0.jpg" alt="Honda HR-V">
            <div class="car-info">
                <h3>Honda HR-V</h3>
                <p>Comfortable SUV with premium interior and space.</p>
                <a href="CarForm.php" class="btn">Book Now</a>
            </div>
        </div>
    </div>
</section>

<footer>
    © <?php echo date("Y"); ?> Abdul Capydeng's Car Rental. All rights reserved.
</footer>

</body>
</html>
