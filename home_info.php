<?php

// Handle CORS
$allowedOrigins = ['http://surf.co.ke', 'https://surf.co.ke'];
$origin = $_SERVER['HTTP_ORIGIN'];

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if (!isset($_GET['username'])) {
    $response = array('status' => 'error', 'message' => 'Username is not set!');
    echo json_encode($response);
    exit();
}

// Get username from query parameter
$username = $_GET['username'];
$_SESSION['username'] = $username;

// Database connection details
$host = 'localhost';
$username_db = 'root';
$password = '';
$database = 'mpesa';

// Create a connection to the database
$conn = new mysqli($host, $username_db, $password, $database);

// Check if the connection was successful
if ($conn->connect_error) {
    $response = array('status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error);
    echo json_encode($response);
    exit();
}

$sql = "SELECT * FROM home_users WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response = array('status' => 'success', 'data' => $row);
} else {
    $response = array('status' => 'error', 'message' => 'Userdata does not exists!');
}

echo json_encode($response);
