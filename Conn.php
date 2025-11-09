<?php

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "car_rental";

$conn = new mysqli($servername, $username, $password, $dbname);

function register($username, $email, $password) {
    global $conn;
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
    return $conn->query($sql);
}

function login($username, $password) {
    global $conn;
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function selectAllUsers(){
    global $conn;
    $sql = "SELECT * FROM users";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function deleteUserByID($id){
    global $conn;
    $sql = "DELETE FROM users WHERE id = '$id'";
    $conn->query($sql);
}

function selectUserByID($id){
    global $conn;
    $sql = "SELECT * FROM users WHERE id = '$id'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function updateByID($id, $username, $email, $password){
    global $conn;
    $sql = "UPDATE users SET username = '$username', email = '$email', password = '$password' WHERE id = '$id'";
    return $conn->query($sql);
}

