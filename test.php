<?php
include_once('lib/routeros_api.class.php');

$API = new RouterosAPI();

// MikroTik credentials
$routerIP = 'sg-10.hostddns.us'; // Replace with your router IP
$username = 'admin';        // Replace with your username
$password = '12345678';     // Replace with your password
$port = '18421';               // Replace with your custom API port if not the default (8728)

// Connect to the MikroTik router
if ($API->connect($routerIP, $username, $password)) {
    echo "Connected successfully!";
    
    // Perform your API operations here

    $API->disconnect(); // Disconnect when done
} else {
    echo "Failed to connect to the MikroTik router.";
}
