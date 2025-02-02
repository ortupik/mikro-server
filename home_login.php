<?php
session_start();

// Handle CORS
$allowedOrigins = ['http://surf.co.ke', 'https://surf.co.ke'];
$origin = $_SERVER['HTTP_ORIGIN'];

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Check if username and password have been posted
if (isset($_POST['username']) && isset($_POST['password'])) {

    $host       = 'localhost';  // Your database host
    $dbUsername = 'root';       // Your database username
    $dbPassword = '';           // Your database password
    $database   = 'mpesa';      // Your database name

    // Create a new MySQLi connection
    $conn = new mysqli($host, $dbUsername, $dbPassword, $database);

    // Check connection
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
        exit;
    }

    // Prepare and bind to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM home_users WHERE username = ? AND password = ?");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("ss", $_POST['username'], $_POST['password']);

    // Execute statement
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $_SESSION['username'] = $_POST['username'];
        $response = ['username' => $_POST['username'], 'success' => true];
        echo json_encode($response);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
    }

    // Clean up
    $stmt->close();
    $conn->close();

} else {
    http_response_code(400);
    echo json_encode(['error' => 'Please enter username and password.']);
}
?>