<?php
session_start();

if (!isset($_GET['username'])) {
    header("Location: http://surf.co.ke/login");
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
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data for the user with username = 'admin'
$sql = "SELECT * FROM home_users WHERE username = '$username'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title id="title"></title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta name="theme-color" content="#3B5998" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;"/>
    <link rel="stylesheet" href="style.css">
    
</head>
<body>
    <div id="main" class="main" >
    <div class="box" style="margin-top: 10px;">
    <header>
        <h2>Surf Internet</h2>
        <marquee style="font-weight:bold;">Welcome to Surf Hotspot! Enjoy cheap unlimited internet
                browsing all day!!</marquee>
            </header>
        </div>
        <form action="http://localhost/mikhmon/mpesa/checkout.php?product_name=monthlyHome&username=<?php echo $username ?>" method="post">
                <h3 style="color: red;">Your Subscription has expired!</h3>
                <p><b>Renew your subscription to continue service</b></p>
                <button class="button3" type="submit">RENEW SUBSCRIPTION</button>
                <br><br>
            </form>
            <form action="logout.php" name="logout" >
                <table class="table2 ">
                    <?php
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $username = $row['username'];   
                        $ip = $row['ip'];
                        $phone = $row['phone'];
                        $product_name = $row['product_name'];
                        $devices  = $row['devices'];
                        $subscription_date = $row['subscription_date'];
                        $expiry_date = $row['expiry_date'];
                        $date_created = $row['date_created'];
                        $subscription_status = ($row['subscription_status'] == 1) ? 'Active' : 'Expired';
                        ?>
                    <tr><td align="right" style="width: 50%;">IP Address <img width="20" src="img/ip.png"> </td><td><?php echo $ip;?></td></tr>    
                    <tr><td align="right">Username  </td><td><?php echo $username;?></td></tr>
                    <tr><td align="right">Phone Number  </td><td><?php echo $phone;?></td></tr>
                    <tr><td align="right">Subscription Plan  </td><td><?php echo $product_name;?></td></tr>
                    <tr><td align="right">Devices Allowed  </td><td><?php echo $devices;?></td></tr>
                    <tr><td align="right">Subscription Status  </td><td style="color: <?php echo ($subscription_status == 'Expired') ? 'red' : 'white';?>;"><?php echo $subscription_status;?></td></tr>
                    <tr><td align="right">Subscription Date  </td><td><?php echo $subscription_date;?></td></tr>
                    <tr><td align="right">Expiry Date  </td><td><?php echo $expiry_date;?></td></tr>
                   <?php } ?>
                </table>
                <br>
                <div>
             
                </div>
                <button class="button2" type="submit"><i class="icon icon-logout">&#xe804;</i> LOGOUT</button>
            </form>
           
    </div>
</div>

   
<script type="text/javascript">
    document.getElementById('title').innerHTML = window.location.hostname + " > home";
//get validity
    var usr = document.getElementById('user').innerHTML
    var url = "https://example.com/status/status.php?name="; 
    var SessionName = "wifijoss"
    var getvalid = url+usr+"&session="+SessionName
    document.getElementById('exp').src = getvalid;
        
</script>
</body>
</html>


