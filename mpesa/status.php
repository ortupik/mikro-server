<?php

include_once('../lib/routeros_api.class.php');

$API = new RouterosAPI();
$API->debug = false;

$iphost = "192.168.6.1";
$userhost = "admin";
$passwdhost = '12345678';

//$API->connect($iphost, $userhost, $passwdhost);

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
    $shortcode = '174379'; 
    $type = '4';
    $key = "Ag3WMhXZnR0c19fPKV42VgpArCb9kdakGuc8vIdKC53w7SQP"; //Put your key here
    $secret = "5NqBQGqGxecLEbMGCAaYVAfwK0LpB2UJFRbggxtb032jtQOp3z14roYtOcPreStY";  //Put your secret here
    $initiatorName = "testapi";
    $initiatorPassword = "Safaricom999!*!";
    $results_url = "https://174.138.68.225/mpesa/callback_test.php"; //Endpoint to receive results Body
    $timeout_url = "https://174.138.68.225/mpesa/callback_test.php"; //Endpoint to to go to on timeout
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

    if (isset($_POST['phone_number'])) {
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
        $SecurityCredential = "fFnAVzdRiGDBFHzY9p0J+vRqUsZ+fpkaCAw1LxpfbJjVGLO6avzF6slhI3u1TepXVhzK7U0j+fj5R3/pgvKmIwPIkCwR02LX84nEwxvVTpG04zKXMzgQKMCh5dwwIuJ4Lkid1EARZbFRDaPk9GLliKk5hsjJzGRtxRt2UNT1DdqWIjir5oLBnZSKbC/sbqaOORp7WnkMje6mBKW2e5vCSJnMVtmZKqR1sV2Ae3hxDW0ba7pkx/PxUTeKA0wejUzxpARrxHD/5w9pUNsGH/t7VMLFUzLyd7MtPSJAOedz7UWT0hP/zhBDjNSu2vzpw+3gNFwWr9fMMRoWyDX6zrr/VA==";

    
        //echo $token;
    
        $curl_post_data = array( 
            "Initiator" => $initiatorName, 
            "SecurityCredential" => $SecurityCredential, 
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

        var_dump($result);
            
        $verified = $result->{'ResponseCode'};
        if($verified === "0"){
            $msg .=  "Verification Request SUCCESSFUL! Redirecting...";
            $server = "hotspot1";
            $name = generateRandomString();
            $password = $name;
            $profile = 'bronze';
            $disabled = false;
            $timelimit = '30m';
            $datalimit = '100M';
            $comment = $timelimit;
            $chkvalid = false;
            $mbgb = '';
            $usermode = "vc-";

    
          /* $response = $API->comm("/ip/hotspot/user/add", array(
            "server" => "$server",
            "name" => "$name",
            "password" => "$password",
            "profile" => "$profile",
            "disabled" => "no",
            "limit-uptime" => "$timelimit",
            "limit-bytes-total" => "$datalimit",
            "comment" => "$comment",
            ));
            $getuser = $API->comm("/ip/hotspot/user/print", array(
            "?name" => "$name",
            ));
            $voucher = $getuser[0]['name'];*/

           // echo "<script>window.location='http://smurf.co.ke/login?voucher=" . $voucher."'</script>";

           // header('Location: http://free.wifi?voucher'.);
            exit;
        }else{
           echo $msg .=  "Verification Request UNSUCCESSFUL! ";
        }
    }




    
