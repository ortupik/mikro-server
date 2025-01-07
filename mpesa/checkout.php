<?php
  include('express-stk.php');
?>
<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css?family=Lato:400,100,300,700,900" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Source+Code+Pro:400,200,300,500,600,700,900');

        body {
            background-color: #171A3D;
            font-family: 'Lato', sans-serif;
            margin: 0;
            color: #fff;
        }

        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            flex-direction: column;
            padding: 0 20px;
        }

        .header h1 {
            font-weight: 700;
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 20px;
            color: #18C2C0;
        }

        .price h1 {
            font-weight: 300;
            color: #18C2C0;
            letter-spacing: 2px;
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
            background-color: #242852;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            margin-bottom: 15px;
            width: 100%;
            max-width: 520px;
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
            color: #8F92C3;
        }

        .order-item-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #18C2C0;
        }

        .card {
            width: 100%;
            max-width: 560px;
            background: #242852;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
        }

        .card .row {
            padding: 1.2rem;
            border-bottom: 1.2px solid #292C58;
        }

        .card .row.number {
            background-color: #292C58;
        }

        .info label {
            font-size: 0.9rem;
            color: #8F92C3;
            display: block;
            margin-bottom: 8px;
        }

        .info input {
            width: 100%;
            padding: 0.7rem;
            background-color: #1D2146;
            border: none;
            border-radius: 5px;
            font-family: 'Source Code Pro', monospace;
            color: white;
            outline: none;
        }

        .info input::placeholder {
            color: #666;
            font-style: italic;
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
            color: #8F92C3;
            margin-top: 40px;
            text-align: center;
            font-size: 0.9rem;
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
       
       <form action='<?php echo $_SERVER['PHP_SELF'] ?>' method='POST'>
            <div class="price">
                <h1>Sleek Internet - Checkout</h1>
                <div class="order-summary">
                    <div class="order-item">
                        <img src="vc.png" class="order-item-image" alt="Bronze Plan">
                        <div class="order-item-details">
                            <h3>Bronze Plan</h3>
                            <div>Validity Period: 1 Hour</div>
                            <div>1 Access User</div>
                            <div>Unlimited Without Quota</div>
                        </div>
                        <div class="order-item-price">Ksh 20</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="row">
                    <img src="mpesa.png" style="width:30%; margin: 0 35%;">
                    <p>1. Enter the <b>phone number</b> and press "<b>Confirm and Pay</b>"<br>2. You will receive a popup on your phone. Enter your <b>MPESA PIN</b></p>
                </div>
                <div class="row number">
                    <div class="info">
                        <label for="cardnumber">Phone number</label>
                        <input id="cardnumber" type="text" name="phone_number" maxlength="10" placeholder="254700000000" />
                    </div>
                </div>
            </div>

            <div class="button">
                <button type="submit">Confirm and Pay</button>
            </div>
        </form>

        <p>Copyright 2025| All Rights Reserved | Made by <a href="#">Smurf</a></p>
    </div>
</body>
</html>
