<?php

function sendSms($phoneNumbers, $message) {
    // Your credentials
    $username = 'sandbox'; // Use 'sandbox' for testing or your live username
    $apiKey = 'atsk_6c2a8288a9c8ba442073662c611fc735ea128ebf1e9f98a93d0db5f296c44f3e599410f4';

    // API endpoint
    $url = 'https://api.sandbox.africastalking.com/version1/messaging';

    // Prepare POST data
    $data = [
        'username'  => $username,
        'message'   => $message,
        'to'        => $phoneNumbers, // Comma-separated string
    ];
    $payload = http_build_query($data);

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/x-www-form-urlencoded',
        "apiKey: $apiKey"
    ]);

    // Execute the request
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
       // echo 'cURL Error: ' . curl_error($ch);
    } else {
       // echo 'Response: ' . $response;
    }

    // Close cURL
    curl_close($ch);
}
