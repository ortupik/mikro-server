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

include_once('lib/routeros_api.class.php');

$router = new RouterosAPI();
$router->debug = false;
$ip = '192.168.6.1';
$user = 'admin';
$password = '12345678';

$conn = new mysqli("localhost", "root", "", "mpesa");
if ($conn->connect_error) {
    $response = array('status' => 'error', 'message' => 'Could not connect to database !');
    echo json_encode($response);
    exit();
}

if ($router->connect($ip, $user, $password)) {  
    if (isset($_POST['change_username']) && isset($_POST['current_username']) && isset($_POST['new_username'])) {
        $result = $router->comm('/ip/hotspot/user/print', array(
            '?name' => $_POST['current_username']
        ));
        if (count($result) > 0) {
            $router->comm('/ip/hotspot/user/set', array(
                '.id' => $result[0]['.id'],
                'name' => $_POST['new_username']
            ));         
            $sql = "UPDATE home_users SET username = ? WHERE mikrotik_id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ss", $_POST['new_username'], $result[0]['.id']);
                $stmt->execute();
                $stmt->close();
            }
            $conn->close();
            $response = array('status' => 'success', 'message' => 'Username changed successfully', 'username' => $_POST['new_username']);
        }else{
            $response = array('status' => 'error', 'message' => 'Username does not exist!');
        }
    }else{
      //  $response = array('status' => 'error', 'message' => 'Wrong operation!', 'post'=> $_POST);
    }

    if (isset($_POST['change_password']) && isset($_POST['current_username']) && isset($_POST['current_password']) && isset($_POST['new_password'])) {
        $result = $router->comm('/ip/hotspot/user/print', array(
            '?name' => $_POST['current_username']
        ));
        if (count($result) > 0) {
            $result_mikrotik = $router->comm('/ip/hotspot/user/print', array(
                '?name' => $_POST['current_username'],
                '?password' => $_POST['current_password']
            ));
            if (count($result_mikrotik) > 0) {
                $sql = "SELECT * FROM home_users WHERE username = ? AND password = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("ss", $_POST['current_username'], $_POST['current_password']);
                    $stmt->execute();
                    $result_db = $stmt->get_result();
                    if ($result_db->num_rows > 0) {
                        $router->comm('/ip/hotspot/user/set', array(
                            '.id' => $result_mikrotik[0]['.id'],
                            'password' => $_POST['new_password']
                        ));
                        $sql = "UPDATE home_users SET password = ? WHERE username = ? AND password = ?";
                        if ($stmt = $conn->prepare($sql)) {
                            $stmt->bind_param("sss", $_POST['new_password'], $_POST['current_username'], $_POST['current_password']);
                            $stmt->execute();
                            $stmt->close();
                        }
                        $response = array('status' => 'success', 'message' => 'Password changed successfully');
                    } else {
                        $response = array('status' => 'error', 'message' => 'Wrong current password!');
                    }
                } else {
                    $response = array('status' => 'error', 'message' => 'Error preparing statement!');
                }
            } else {
                $response = array('status' => 'error', 'message' => 'Wrong current password!');
            }
            $conn->close();
        } else {
            $response = array('status' => 'error', 'message' => 'Username does not exist!');
        }
    }else{
       // $response = array('status' => 'error', 'message' => 'Wrong operation!', 'post'=> $_POST);
    }
}else{
    $response = array('status' => 'error', 'message' => 'Could not connect to router !');
}

echo json_encode($response);

