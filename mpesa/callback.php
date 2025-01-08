<?php

//$conn = new mysqli("174.138.68.225", "evstrjmuys", "fxy7fQqTBR", "evstrjmuys");
//$conn = new mysqli("localhost", "root", "", "mpesa");

if ($conn->connect_error) {
    //die("Connection failed: " . $conn->connect_error);
}

$content = file_get_contents('php://input');

$res = json_decode($content, true);

file_put_contents('mpesa_callback.log', print_r($res, true), FILE_APPEND);



$ResultDesc = $res['Body']['stkCallback']['ResultDesc'];
$ResultCode = $res['Body']['stkCallback']['ResultCode'];
$CheckoutRequestID = $res['Body']['stkCallback']['CheckoutRequestID'];
$MerchantRequestID = $res['Body']['stkCallback']['MerchantRequestID'];

$amount = $res['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
$MpesaReceiptNumber = $res['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
$TransactionDate = $res['Body']['stkCallback']['CallbackMetadata']['Item'][2]['Value'];
$phone = $res['Body']['stkCallback']['CallbackMetadata']['Item'][3]['Value'];

$sql = "INSERT INTO `payments` (`MpesaReceiptNumber`, `TransactionDate`, `Amount`, `Phone`, `CheckoutRequestID`, `MerchantRequestID`, `ResultCode`, `ResultDesc`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdsssss", $MpesaReceiptNumber, $TransactionDate, $amount, $phone, $CheckoutRequestID, $MerchantRequestID, $ResultCode, $ResultDesc);

if ($stmt->execute()) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE `CheckoutRequestID` = ?");
    $stmt->bind_param("s", $CheckoutRequestID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $status = ($ResultCode == '1032') ? 'CANCELLED' : 'SUCCESS';
    $updateSql = "UPDATE `orders` SET `Status` = ? WHERE `ID` = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $status, $ID);
    $updateStmt->execute();

    header('Location: confirm-payment.php');
} else {
    $errors['database'] = "Unable to initiate your order: " . $conn->error;
    foreach ($errors as $error) {
        $errmsg .= $error . '<br />';
    }
}

$conn->close();

?>

