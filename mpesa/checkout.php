<?php
  include('express-stk.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Lato:400,100,300,700,900" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Source+Code+Pro:400,200,300,500,600,700,900');


        body {
            background-color: #f2f2f2;
            margin: 0;
            color: #000;
            font-family: -apple-system, BlinkMacSystemFont, "segoe ui", Verdana, Roboto, "helvetica neue", Arial, sans-serif, "apple color emoji";
            
        }

        .container {
            display: flex;
            height: 100vh;
            flex-direction: column;
            padding:  20px;
            max-width: 560px;
            margin: 0% auto;
            height: 100vh;
        }

        .header h1 {
            font-weight: 700;
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 20px;
            color: black;
        }

        .price h1 {
            font-weight: bold;
            color: black;
            text-align: center;
            margin-bottom: 30px;
        }

        .order-summary {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .order-item {
            display: flex;
            align-items: center;
            background-color: #292C58;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            margin-bottom: 15px;
            width: 90%;
        }

        .order-item-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 15px;
            border-radius: 5px;
        }

        .order-item-details {
            flex-grow: 1;
        }

        .order-item-details h3 {
            margin: 0;
            font-size: 1.2rem;
            color: #fff;
        }

        .order-item-details div {
            font-size: 0.9rem;
            color: #fff;
        }

        .order-item-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #18C2C0 ;
        }

        .card {
            background: #f2f2f2;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
        }

        .card .row {
            padding: 1.2rem;
            border-bottom: 1.2px solid #292C58;
            color: black;
        }

        .card .row.number {
            background-color: #292C58;
        }

        .info label {
            font-size: 0.9rem;
            color: #fff;
            display: block;
            margin-bottom: 8px;
        }

        .info input {
            width: 90%;
            padding: 1rem;
            border: none;
            border-radius: 5px;
            outline: none;
            font-size: 16px;
        }

        .info input::placeholder {
            color: #666;
            font-style: bold;
           
        }

        .button button {
            font-size: 1.2rem;
            font-weight: 400;
            letter-spacing: 1px;
            width: 100%;
            background-color: #18C2C0;
            border: none;
            color: #fff;
            padding: 18px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .button button:hover {
            background-color: #15aeac;
        }

        .button button:active {
            background-color: #139b99;
        }

        p {
            color: #000;
            padding: 5px 15px;
            font-size: 1rem;
        }

        p a {
            color: #18C2C0;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
       
       <form name="checkout" action='<?php echo $_SERVER['PHP_SELF'] ?>' method='POST'>
            <div class="price">
                <h1>surf Hotspot - Payment</h1>
                <div class="order-summary">
                    <div class="order-item">
                        <img src="vc.png" class="order-item-image" alt="Bronze Plan">
                        <div class="order-item-details">
                            <h3>WiFi Hotspot Plan</h3>
                            <div id="validity">- Validity Period: </div>
                            <div>- 1 Access User</div>
                            <div>- Unlimited Without Quota</div>
                        </div>
                        <div id="price" class="order-item-price"></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="row">
                    <img src="mpesa.png" style="width:30%; margin: 0 35%;">
                    <p>1. Enter the <b>phone number</b> and press "<b>Confirm and Pay</b>" </p>
                    <p>2. You will receive a popup on your phone. Enter your <b>MPESA PIN</b></p>
                </div>
                <div class="row number">
                    <div class="info">

                        <label for="cardnumber">Phone number</label>
                        <input id="cardnumber" type="text" name="phone_number" maxlength="10" placeholder="e.g 0710000000" />
                        <input type="hidden" name="product_name"  />
                    </div>
                </div>
            </div>

            <div class="button">
                <button type="submit">Confirm and Pay</button>
            </div>
        </form>

        <p style="text-align:center">Copyright 2025| All Rights Reserved | Made by <a href="#">surf</a></p>
    </div>
    <script type="text/javascript">
         const urlParams = new URLSearchParams(window.location.search);
         const productName = urlParams.get('product_name');
         document.checkout.product_name.value = productName;
         
         const packages = [
            { id: "quick30", validity: "30 Min", amount: 5 },
            { id: "hourly3", validity: "1 Hour", amount: 10 },
            { id: "halfday12", validity: "12 Hours", amount: 20 },
            { id: "oneday24", validity: "24 Hours", amount: 30 },
            { id: "weekly", validity: "1 Week", amount: 170 },
            { id: "monthly", validity: "1 Month", amount: 700 },
        ];

        var validity = packages.filter(package => package.id == productName).map(p => "- Valid for "+ p.validity);
        var price = packages.filter(package => package.id == productName).map(p => "Ksh "+ p.amount);;
        document.getElementById("price").innerHTML = price;
        document.getElementById("validity").innerHTML = validity;

     </script>
</body>
</html>
