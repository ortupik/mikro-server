<?php

include_once('../lib/routeros_api.class.php');
include_once('../sms/sendSms.php');

$API = new RouterosAPI();
$API->debug = false;

$iphost = "192.168.6.1";
//$iphost = "id-12.hostddns.us:13575";
$userhost = "admin";
$passwdhost = '12345678';
$url = "https://9e53-102-0-15-222.ngrok-free.app/surf/";

$API->connect($iphost, $userhost, $passwdhost);

function generateRandomString($length = 7) {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $result .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $result;
}

/*Call function with these configurations*/
    $env="sandbox";
    $shortcode = '600984'; 
    $type = '4';
    $key = "Ag3WMhXZnR0c19fPKV42VgpArCb9kdakGuc8vIdKC53w7SQP"; //Put your key here
    $secret = "5NqBQGqGxecLEbMGCAaYVAfwK0LpB2UJFRbggxtb032jtQOp3z14roYtOcPreStY";  //Put your secret here
    $initiatorName = "testapi";
    $initiatorPassword = "Safaricom999!*!";
    $results_url = $url."mpesa/callback_test.php"; //Endpoint to receive results Body
    $timeout_url = $url."mpesa/callback_test.php"; //Endpoint to to go to on timeout
/*End  configurations*/

/*Ensure transaction code is entered*/
    // if (!isset($_GET["transactionID"])) {
    //     echo "Technical error";
    //     exit();
    // }
/*End transaction code validation*/

    //$transactionID = $_GET["transactionID"]; 
    $transactionID = "QCS2FC258A";
    $command = "TransactionStatusQuery";
    $remarks = "Transaction Status Query"; 
    $occasion = "Transaction Status Query";
    $callback = null ;
    $msg = '';

    if (isset($_POST['phone_number']) && isset($_SESSION["product_name"])) {
        $access_token = ($env == "live") ? "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials" : "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"; 
        $credentials = base64_encode($key . ':' . $secret); 
        
        $ch = curl_init($access_token);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic " . $credentials]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response); 
    
        //echo $result->{'access_token'};
        
        $token = isset($result->{'access_token'}) ? $result->{'access_token'} : "N/A";
    
        $publicKey = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "certificates" . DIRECTORY_SEPARATOR . "SandboxCertificate.cer"); 
        $isvalid = openssl_public_encrypt($initiatorPassword, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING); 
        $password = base64_encode($encrypted);
    
        //echo $token;
    
        $profile = $_SESSION["product_name"];

        $curl_post_data = array( 
            "Initiator" => $initiatorName, 
            "SecurityCredential" => $password, 
            "CommandID" => $command, 
            "TransactionID" => $transactionID, 
            "PartyA" => $shortcode, 
            "IdentifierType" => $type, 
            "ResultURL" => $results_url, 
            "QueueTimeOutURL" => $timeout_url, 
            "Remarks" => $remarks, 
            "Occasion" => $occasion,
        ); 
    
        $data_string = json_encode($curl_post_data);
    
        //echo $data_string;
    
        $endpoint = ($env == "live") ? "https://api.safaricom.co.ke/mpesa/transactionstatus/v1/query" : "https://sandbox.safaricom.co.ke/mpesa/transactionstatus/v1/query"; 
    
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
        
        $result = json_decode($response); 
            
        $verified = $result->{'ResponseCode'};
        if($verified === "0"){
            $msg .=  "Verification Request SUCCESSFUL! Redirecting...";
            $server = "hotspot1";
            $name = generateRandomString();
            $password = $name;
            $disabled = false;
            $timelimit = '30m';
            $datalimit = '100M';
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
            "limit-bytes-total" => "$datalimit",
            "comment" => "$comment",
            ));
            
            $voucher = $name;
            
            $message = 'surf Wifi: Congratulations! You have successfully subscribed to 1 hour internet plan. Your subscription code is '.$voucher;
            sendSms('+254705130991', $message);

            header('Location: http://surf.co.ke/login?voucher=' . $voucher);
        }else{
           echo $msg .=  "Verification Request UNSUCCESSFUL! ";
        }
    }




    
