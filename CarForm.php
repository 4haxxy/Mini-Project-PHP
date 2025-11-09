<?php
require 'Conn.php';
session_start();

$showPopup = false;
$error = "";

if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit;
}

$username = $_SESSION['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $nric = htmlspecialchars($_POST['nric']);
    $email = htmlspecialchars($_POST['email']);
    $vehicle = $_POST['Vehicle_Model'];
    $durationType = $_POST['Duration_Type'];
    $date_start = $_POST['Date_Start'];
    $date_end = $_POST['Date_End'];
    $event_time = $_POST['event_time'];
    $fuel_type = $_POST['Fuel_Type'];
    $fee = $_POST['Fee'];
    $deposit = $_POST['Deposit'];
    $payment = $_POST['Payment'];

    // Handle file upload
    $photoPath = "";
    if (!empty($_FILES['photo']['name'])) {
        $photoName = basename($_FILES['photo']['name']);
        $photoTmp = $_FILES['photo']['tmp_name'];
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        // Add timestamp to avoid duplicate file names
        $photoPath = $uploadDir . time() . "_" . $photoName;
        if (!move_uploaded_file($photoTmp, $photoPath)) {
            $error = "⚠️ Failed to upload photo.";
        }
    }

    if (empty($error)) {
        // Check overlapping bookings
        $checkQuery = "SELECT * FROM bookings 
                       WHERE Vehicle_Model = ? 
                       AND (
                           (Date_Start <= ? AND Date_End >= ?)
                           OR (Date_Start <= ? AND Date_End >= ?)
                       )";
        $stmt = $conn->prepare($checkQuery);
        if (!$stmt) {
            die("Prepare failed for checkQuery: " . $conn->error);
        }
        $stmt->bind_param("sssss", $vehicle, $date_start, $date_start, $date_end, $date_end);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "❌ Sorry, this car is already booked for the selected date range.";
        } else {
            // Insert booking including name, nric, email, and photo
            $insertQuery = "INSERT INTO bookings 
                (username, name, nric, email, Vehicle_Model, Duration_Type, Date_Start, Date_End, event_time, Fuel_Type, Fee, Deposit, Payment, photo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($insertQuery);
            if (!$stmt) {
                die("Prepare failed for insertQuery: " . $conn->error);
            }

            $stmt->bind_param(
                "ssssssssssssss",
                $username, $name, $nric, $email, $vehicle, $durationType,
                $date_start, $date_end, $event_time, $fuel_type,
                $fee, $deposit, $payment, $photoPath
            );

            if ($stmt->execute()) {
                $showPopup = true;
            } else {
                $error = "⚠️ Booking failed to save. Please try again. Error: " . $stmt->error;
            }
        }
    }
}
?>





<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Abdul Capydeng's Car Rental</title>
<style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f3f6fa; margin:0; padding:0; }
    h1 { text-align:center; background:#007bff; color:white; padding:20px 0; margin-bottom:30px; font-size:28px; border-radius:0 0 20px 20px; }
    form { max-width: 1000px; margin:auto; background:white; padding:25px 40px; border-radius:15px; box-shadow:0 0 15px rgba(0,0,0,0.1); }
    fieldset { border:none; margin-bottom:25px; }
    legend { font-size:22px; font-weight:bold; color:#007bff; margin-bottom:10px; }
    label { display:block; margin:5px 0; }
    input[type="text"], input[type="date"], input[type="time"], select { width:95%; padding:8px; border-radius:5px; border:1px solid #ccc; margin-top:5px; }
    input[type="radio"], input[type="file"] { margin-right:8px; }
    .car-selection { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px,1fr)); gap:20px; margin-top:20px; }
    .car-card { background:#f9fbff; border:1px solid #ddd; border-radius:10px; text-align:center; padding:15px; transition:0.3s; }
    .car-card:hover { box-shadow:0 0 10px rgba(0,0,0,0.15); transform:translateY(-3px); }
    .car-img { width:100%; height:120px; object-fit:cover; border-radius:10px; }
    .duration-options { margin-top:10px; text-align:left; background:#f0f6ff; padding:10px; border-radius:8px; }
    .overlay { position:fixed; top:0; left:0; width:100%; height:100%; background-color: rgba(0,0,0,0.6); display:none; justify-content:center; align-items:center; z-index:1000; }
    .popup { background-color:white; padding:40px; border-radius:15px; text-align:center; box-shadow:0 4px 15px rgba(0,0,0,0.3); max-width:400px; width:90%; }
    .popup-text { font-size:20px; color:#333; margin-bottom:25px; }
    .popup-button-link { background-color:#007bff; color:white; padding:12px 25px; text-decoration:none; border-radius:8px; transition:0.3s; }
    .popup-button-link:hover { background-color:#0056b3; }
    #qrSection { display:none; text-align:center; margin-top:15px; }
    
    /* ✅ Updated button row */
    .button-row {
      display: flex;
      justify-content: space-between;
      gap: 15px;
      margin-top: 20px;
    }

    .back-button,
    .confirm-button {
      flex: 1;
      text-align: center;
      font-size: 18px;
      padding: 14px;
      border: none;
      border-radius: 10px;
      color: white;
      cursor: pointer;
      transition: background 0.3s;
      text-decoration: none;
    }

    .back-button {
      background-color: #6c757d;
    }

    .back-button:hover {
      background-color: #5a6268;
    }

    .confirm-button {
      background-color: #007bff;
    }

    .confirm-button:hover {
      background-color: #0056b3;
    }

    @media (max-width: 600px) {
      .button-row {
        flex-direction: column;
      }
    }
</style>
</head>
<body>

<h1>Abdul Capydeng's Car Rental</h1>

<form id="rentalForm" method="POST" enctype="multipart/form-data">
<fieldset>
<legend>Personal Details</legend>
<label><b>Full Name:</b></label>
<input type="text" name="name" required>
<label><b>NRIC:</b></label>
<input type="text" name="nric" required>
<label><b>Email:</b></label>
<input type="text" name="email" required>
</fieldset>

<fieldset>
<legend>Rental Details</legend>
<div class="car-selection">
    <div class="car-card">
        <label><input type="radio" name="Vehicle_Model" value="axia" onclick="selectCar('axia')" required> Axia</label>
        <img src="https://i.pinimg.com/1200x/eb/44/40/eb444019e3967ce0f2648143ca8b8a72.jpg" class="car-img">
        <div class="duration-options">
            <label><input type="radio" name="Duration_Type" value="halfday" onclick="updateFee()"> Half Day</label>
            <label><input type="radio" name="Duration_Type" value="fullday" onclick="updateFee()"> Full Day</label>
            <label><input type="radio" name="Duration_Type" value="month" onclick="updateFee()"> Monthly</label>
        </div>
    </div>
    <div class="car-card">
        <label><input type="radio" name="Vehicle_Model" value="myvi" onclick="selectCar('myvi')"> Myvi</label>
        <img src="https://i.pinimg.com/736x/89/c1/21/89c121941a9a41fe872e8ce77a0f062c.jpg" class="car-img">
        <div class="duration-options">
            <label><input type="radio" name="Duration_Type" value="halfday" onclick="updateFee()"> Half Day</label>
            <label><input type="radio" name="Duration_Type" value="fullday" onclick="updateFee()"> Full Day</label>
            <label><input type="radio" name="Duration_Type" value="month" onclick="updateFee()"> Monthly</label>
        </div>
    </div>
    <div class="car-card">
        <label><input type="radio" name="Vehicle_Model" value="alza" onclick="selectCar('alza')"> Alza</label>
        <img src="https://i.pinimg.com/1200x/67/9d/a3/679da35624feaedb9307653204a3ba32.jpg" class="car-img">
        <div class="duration-options">
            <label><input type="radio" name="Duration_Type" value="halfday" onclick="updateFee()"> Half Day</label>
            <label><input type="radio" name="Duration_Type" value="fullday" onclick="updateFee()"> Full Day</label>
            <label><input type="radio" name="Duration_Type" value="month" onclick="updateFee()"> Monthly</label>
        </div>
    </div>
    <div class="car-card">
        <label><input type="radio" name="Vehicle_Model" value="hrv" onclick="selectCar('hrv')"> Honda HRV</label>
        <img src="https://i.pinimg.com/736x/23/41/30/234130a5c77e4e78c5c5638245199da0.jpg" class="car-img">
        <div class="duration-options">
            <label><input type="radio" name="Duration_Type" value="halfday" onclick="updateFee()"> Half Day</label>
            <label><input type="radio" name="Duration_Type" value="fullday" onclick="updateFee()"> Full Day</label>
            <label><input type="radio" name="Duration_Type" value="month" onclick="updateFee()"> Monthly</label>
        </div>
    </div>
</div>

<label>Rental Start:</label>
<input type="date" name="Date_Start" onchange="updateFee()" required>
<label>Rental End:</label>
<input type="date" name="Date_End" onchange="updateFee()" required>
<label>Pickup Time:</label>
<input type="time" name="event_time" onchange="updateFee()" required>

<label>Fuel Type:</label>
<select name="Fuel_Type" required>
<option value="">- Select Fuel Type -</option>
<option value="Full-To-Full">Full-To-Full</option>
<option value="Prepaid Fuel">Prepaid Fuel</option>
</select>
</fieldset>

<fieldset>
<legend>Payment & Deposit</legend>
<label>Rental Fee:</label>
<input type="text" name="Fee" readonly>
<label>Security Deposit:</label>
<select name="Deposit" required>
<option value="">- Select -</option>
<option value="RM300">RM300</option>
<option value="RM400">RM400</option>
<option value="RM500">RM500</option>
</select>

<label>Payment Method:</label>
<label><input type="radio" name="Payment" value="Online" onchange="showQR()"> Online Banking</label>
<label><input type="radio" name="Payment" value="Tng" onchange="showQR()"> Touch 'n Go</label>
<label><input type="radio" name="Payment" value="Card" onchange="showQR()"> Credit/Debit Card</label>
<label><input type="radio" name="Payment" value="Cash" onchange="showQR()"> Cash</label>

<div id="qrSection">
<p id="qrText" style="font-weight:bold;"></p>
<img id="qrImage" src="" alt="QR Code" style="width:250px; border-radius:10px;">
</div>

<label>Upload Proof of Payment:</label>
<input type="file" name="photo" required>
</fieldset>

<div class="button-row">
  <a href="Home.php" class="back-button">⬅ Back to Home</a>
  <button type="submit" class="confirm-button">Confirm Booking</button>
</div>
</form>

<?php if ($showPopup): ?>
<div class="overlay" style="display:flex;">
<div class="popup">
<p class="popup-text">✅ Thank you! Your booking has been confirmed.</p>
<a href="Home.php" class="popup-button-link">Go to Homepage</a>
</div>
</div>
<?php elseif (!empty($error)): ?>
<div class="overlay" style="display:flex;">
<div class="popup">
<p class="popup-text" style="color:red;"><?php echo $error; ?></p>
<a href="CarForm.php" class="popup-button-link">Go Back</a>
</div>
</div>
<?php endif; ?>

<script>
const carRates = {
    axia: { halfDay: 60, fullDay: 100, monthly: 1500 },
    myvi: { halfDay: 70, fullDay: 120, monthly: 1700 },
    alza: { halfDay: 80, fullDay: 140, monthly: 1900 },
    hrv: { halfDay: 100, fullDay: 160, monthly: 2200 }
};

let selectedCar = '';
let durationType = '';

function selectCar(car) {
    selectedCar = car;

    // Disable all durations first
    const allDurations = document.querySelectorAll("input[name='Duration_Type']");
    allDurations.forEach(input => {
        input.disabled = true;
        input.checked = false;
    });

    // Enable selected car’s durations
    const card = document.querySelector(`input[value='${car}']`).closest('.car-card');
    const durations = card.querySelectorAll("input[name='Duration_Type']");
    durations.forEach(input => input.disabled = false);

    updateFee();
}

function updateFee() {
    const feeInput = document.querySelector("input[name='Fee']");
    const startDate = document.querySelector("input[name='Date_Start']").value;
    const endDate = document.querySelector("input[name='Date_End']").value;
    const durTypeRadio = document.querySelector("input[name='Duration_Type']:checked");
    durationType = durTypeRadio ? durTypeRadio.value : '';

    if (!selectedCar || !durationType || !startDate || !endDate) {
        feeInput.value = '';
        return;
    }

    const rates = carRates[selectedCar];
    let total = 0;

    const start = new Date(startDate);
    const end = new Date(endDate);
    const diffMs = end - start;
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24)) + 1;
    const diffMonths = diffDays / 30; // Approximate month length

    if (durationType === 'fullday') {
        total = diffDays * rates.fullDay;
    } else if (durationType === 'halfday') {
        total = diffDays * rates.halfDay;
    } else if (durationType === 'month') {
        total = Math.ceil(diffMonths) * rates.monthly;
    }

    feeInput.value = total;
}

function showQR() {
    const selectedPayment = document.querySelector('input[name="Payment"]:checked').value;
    const qrSection = document.getElementById('qrSection');
    const qrText = document.getElementById('qrText');
    const qrImage = document.getElementById('qrImage');

    if (selectedPayment === "Tng") {
        qrSection.style.display = 'block';
        qrText.textContent = " Scan this Touch 'n Go QR to make your payment:";
        qrImage.src = "tng_qr.png";
    } 
    else if (selectedPayment === "Online") {
        qrSection.style.display = 'block';
        qrText.textContent = " Scan this Bank QR for Online Banking payment:";
        qrImage.src = "bank_qr.png";
    } 
    else {
        qrSection.style.display = 'none';
        qrText.textContent = "";
        qrImage.src = "";
    }
}
</script>

</body>
</html>
