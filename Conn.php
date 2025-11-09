<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "car_rental";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function registerUser($data) {
    global $conn;
    $sql = "INSERT INTO users 
        (username, password, fullname, nric, age, dob, gender, pwd_status, moNumber, email, add1, add2, city, state, postcode, license_type, expiry_date, photo_path)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;

    $stmt->bind_param(
        "ssssisssssssssssss",
        $data['username'], 
        $data['password'], 
        $data['fullname'], 
        $data['nric'], 
        $data['age'], 
        $data['dob'], 
        $data['gender'], 
        $data['pwd_status'], 
        $data['moNumber'], 
        $data['email'], 
        $data['add1'], 
        $data['add2'], 
        $data['city'], 
        $data['state'], 
        $data['postcode'], 
        $data['license_type'], 
        $data['expiry_date'], 
        $data['photo_path']
    );

    return $stmt->execute();
}


function login($username) {
    global $conn;
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}


function selectAllUsers() {
    global $conn;
    $sql = "SELECT * FROM users";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function deleteUserByID($id) {
    global $conn;
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}


function selectUserByID($id) {
    global $conn;
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}


function updateByID($id, $username, $email, $password) {
    global $conn;
    $sql = "UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $username, $email, $password, $id);
    return $stmt->execute();
}


function getAllCars() {
    global $conn;
    $sql = "SELECT * FROM cars";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getCarByModel($model_name) {
    global $conn;
    $sql = "SELECT * FROM cars WHERE model_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $model_name);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function addBooking($data) {
    global $conn;
    $sql = "INSERT INTO bookings (username, Vehicle_Model, Duration_Type, Date_Start, Date_End, event_time, Fuel_Type, Fee, Deposit, Payment)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssds",
        $data['username'],
        $data['Vehicle_Model'],
        $data['Duration_Type'],
        $data['Date_Start'],
        $data['Date_End'],
        $data['event_time'],
        $data['Fuel_Type'],
        $data['Fee'],
        $data['Deposit'],
        $data['Payment']
    );
    return $stmt->execute();
}

function getBookingsByUser($username) {
    global $conn;
    $sql = "SELECT * FROM bookings WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
