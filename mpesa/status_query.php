<!DOCTYPE html>
<html>
    <head>
    <script src="sweetalert2.js"></script>
</head>
</html>
<?php
include_once('../lib/routeros_api.class.php');
include_once('../sms/sendSms.php');

//$conn = new mysqli("174.138.68.225", "evstrjmuys", "fxy7fQqTBR", "evstrjmuys");
$conn = new mysqli("localhost", "root", "", "mpesa");

if ($conn->connect_error) {
    //die("Connection failed: " . $conn->connect_error);
}

$API = new RouterosAPI();
$API->debug = false;


//$iphost = "192.168.6.1";
$iphost = "sg-10.hostddns.us";
$userhost = "admin";
$passwdhost = '12345678';
$url = "http://204.13.232.131/";
$port = 18421;


$API->connect($iphost, $userhost, $passwdhost, $port);


$env="sandbox";
$shortcode = '174379'; 
$key = "Ag3WMhXZnR0c19fPKV42VgpArCb9kdakGuc8vIdKC53w7SQP"; 
$secret = "5NqBQGqGxecLEbMGCAaYVAfwK0LpB2UJFRbggxtb032jtQOp3z14roYtOcPreStY";  
$initiatorName = "testapi";
$initiatorPassword = "Safaricom999!*!";
$passKey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
$msg = '';

if (isset($_POST['phone_number']) && isset($_SESSION["product_name"])) {

    $profile = $_SESSION["product_name"];
    $phoneNumber = $_POST["phone_number"];
    
    $access_token = ($env == "live") ? "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials" : "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"; 
    $credentials = base64_encode($key . ':' . $secret); 
    
    $ch = curl_init($access_token);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic " . $credentials]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response); 
    
    $token = isset($result->{'access_token'}) ? $result->{'access_token'} : "N/A";

    $timestamp = date('YmdHis');
    $password =  base64_encode($shortcode. $passKey. $timestamp);
    
    $checkoutRequestId = $_SESSION["CheckoutRequestID"];

    $curl_post_data = array( 
        "BusinessShortCode" => $shortcode,    
        "Password"=> $password,    
        "Timestamp" => $timestamp,
        "CheckoutRequestID" => $checkoutRequestId, 
    ); 
    
    $data_string = json_encode($curl_post_data);
        
    $endpoint = ($env == "live") ? "https://api.safaricom.co.ke/mpesa/stkpushquery/v1/query" : "https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query"; 
    
    $ch2 = curl_init($endpoint);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer '.$token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch2, CURLOPT_POST, 1);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
    $response     = curl_exec($ch2);
    curl_close($ch2);

    json_encode($response);
    
    $result = json_decode($response); 

        
    $ResultCode = $result->{'ResultCode'};

   /* $status = ($ResultCode != 0) ? 'CANCELLED' : 'SUCCESS';
    $updateSql = "UPDATE `orders` SET `Status` = ? WHERE `CheckoutRequestID` = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $ResultCode, $checkoutRequestId);
    $updateStmt->execute();*/

    if($ResultCode === "0"){
        $msg .= "Payment Successful!";
        $server = "hotspot1";
            $name = generateRandomString();
            $password = $name;
            $disabled = false;
            $timelimit = '30m';
            $comment = $timelimit;
            $chkvalid = false;
            $mbgb = '';
            $usermode = "vc-";

           $response = $API->comm("/ip/hotspot/user/add", array(
            "server" => "$server",
            "name" => "$name",
            "password" => "$password",
            "profile" => "$profile",
            "disabled" => "no",
            "limit-uptime" => "$timelimit",
            "comment" => "$comment",
            ));
            
            $voucher = $name;

            
            $message = 'surf Wifi: Congratulations! You have successfully subscribed to 1 hour internet plan. Your subscription code is '.$voucher;
            //sendSms($phoneNumber, $message);

            $msg .= "Payment Successfull!";
            //add the popup here
            echo "<script>
        Swal.fire({
            title: 'Payment Successful!',
            text: 'Your subscription code is: $voucher',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'http://surf.co.ke/login?voucher=$voucher';
            }
        });
    </script>";

    // header('Location: http://surf.co.ke/login?voucher=' . $voucher);
    }else{
        $msg .=  $result->{'ResultDesc'};
        echo "<script>
        Swal.fire({
            title: 'Payment Failed!!',
            text: '{$result->{'ResultDesc'}}',
            icon: 'error',
            confirmButtonText: 'Retry'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'http://surf.co.ke/login';
            }
        });
    </script>";
    }    
            
}

function generateRandomString($length = 7) {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $result .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $result;
}
