<?php 
session_start();

$errors  = array();
$errmsg  = '';
$url = "https://42c2-102-0-15-222.ngrok-free.app/mikhmon/";

function generateRandomString() {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $result = '#';
    for ($i = 0; $i < 9; $i++) {
        $result .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $result;
}


if (isset($_POST['phone_number']) && isset($_POST["product_name"])) {

    $phone = $_POST['phone_number'];
    $productName = $_POST['product_name'];
    $orderNo = generateRandomString();

    $config = array(
        "env"              => "sandbox",
        "BusinessShortCode"=> "174379",
        "key"              => "Ag3WMhXZnR0c19fPKV42VgpArCb9kdakGuc8vIdKC53w7SQP", 
        "secret"           => "5NqBQGqGxecLEbMGCAaYVAfwK0LpB2UJFRbggxtb032jtQOp3z14roYtOcPreStY", 
        "username"         => "testapi",
        "TransactionType"  => "CustomerPayBillOnline",
        "passkey"          => "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919", 
        "CallBackURL"      =>  $url."mpesa/callback.php",
        "AccountReference" => "Smurf",
        "TransactionDesc"  => $productName ,
    );

    $packages = array(
        array("id" => "quick30", "validity" => "30 Min", "amount" => 5),
        array("id" => "hourly3", "validity" => "1 Hour", "amount" => 10),
        array("id" => "halfday12", "validity" => "12 Hours", "amount" => 20),
        array("id" => "oneday24", "validity" => "24 Hours", "amount" => 30),
        array("id" => "weekly", "validity" => "1 Week", "amount" => 170),
        array("id" => "monthly", "validity" => "1 Month", "amount" => 700),
    );


    $filteredPackage = array_filter($packages, function($package) use ($productName) {
        return $package["id"] == $productName;
    });

    $validity = array_column($filteredPackage, "validity");
    $price = array_column($filteredPackage, "amount");

    //$amount = 1;
    $amount = $price[0];

    $_SESSION["validity"] = $validity[0];
    $_SESSION["price"] = $amount;
    $_SESSION["product_name"] = $productName;


    $phone = (substr($phone, 0, 1) == "+") ? str_replace("+", "", $phone) : $phone;
    $phone = (substr($phone, 0, 1) == "0") ? preg_replace("/^0/", "254", $phone) : $phone;
    $phone = (substr($phone, 0, 1) == "7") ? "254{$phone}" : $phone;



    $access_token = ($config['env']  == "live") ? "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials" : "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"; 
    $credentials = base64_encode($config['key'] . ':' . $config['secret']); 
    
    $ch = curl_init($access_token);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic " . $credentials]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response); 
    $token = isset($result->{'access_token'}) ? $result->{'access_token'} : "N/A";

    $timestamp = date("YmdHis");
    $password  = base64_encode($config['BusinessShortCode'] . "" . $config['passkey'] ."". $timestamp);

    $curl_post_data = array( 
        "BusinessShortCode" => $config['BusinessShortCode'],
        "Password" => $password,
        "Timestamp" => $timestamp,
        "TransactionType" => $config['TransactionType'],
        "Amount" => 1,
        "PartyA" => $phone,
        "PartyB" => $config['BusinessShortCode'],
        "PhoneNumber" => $phone,
        "CallBackURL" => $config['CallBackURL'],
        "AccountReference" => $config['AccountReference'],
        "TransactionDesc" => $config['TransactionDesc'],
    ); 

    $data_string = json_encode($curl_post_data);

    $endpoint = ($config['env'] == "live") ? "https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest" : "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest"; 

    $ch = curl_init($endpoint );
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer '.$token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response     = curl_exec($ch);
    curl_close($ch);

    $result = json_decode(json_encode(json_decode($response)), true);

    if(!preg_match('/^[0-9]{10}+$/', $phone) && array_key_exists('errorMessage', $result)){
        $errors['phone'] = $result["errorMessage"];
    }

    if($result['ResponseCode'] === "0"){
        
        $MerchantRequestID = $result['MerchantRequestID'];
        $CheckoutRequestID = $result['CheckoutRequestID'];
        $ResultDesc = $result['ResponseDescription'];


       // $conn = mysqli_connect("174.138.68.225","evstrjmuys","fxy7fQqTBR","evstrjmuys");
        $conn = mysqli_connect("localhost","root","","mpesa");
       
        $sql = "INSERT INTO `orders`( `OrderNo`, `Amount`, `Phone`, `CheckoutRequestID`, `MerchantRequestID`,`ResultDesc`) VALUES ('".$orderNo."','".$amount."','".$phone."','".$CheckoutRequestID."','".$MerchantRequestID."','".$ResultDesc."');";
        
        if ($conn->query($sql) === TRUE){
            $_SESSION["MerchantRequestID"] = $MerchantRequestID;
            $_SESSION["CheckoutRequestID"] = $CheckoutRequestID;
            $_SESSION["phone"] = $phone;
            $_SESSION["orderNo"] = $orderNo;
            $_SESSION["ResultDesc"] = $ResultDesc;
            header('location: confirm-payment.php');
        }else{
            $errors['database'] = "Unable to make payment!: ".$conn->error;;  
            foreach($errors as $error) {
                $errmsg .= $error . '<br />';
            } 
        }
        
    }else{
        $errors['message'] = $result['ResponseDescription'];
        $errors['mpesastk'] = $result['errorMessage'];
        foreach($errors as $error) {
            $errmsg .= $error . '<br />';
        }
    }
    
}else{
     $errmsg .= 'Missing Product Name or Phone Number';
}

?>
