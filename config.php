<?php

require_once 'checkout.php';

$productName = $total_product;
$productPrice = $price_total;
$currency = "MYR";
$productID = $number;

$conn = mysqli_connect('localhost', 'root', '', 'bacabuku_db') or die('connection failed');

define('STRIPE_API_KEY', 'sk_test_51JL5uGHlU0gM5lElBDyiLqTg2hNI3JxQYjWE1tMKkk2H6ffhqW4OY6LlTzORPpgLclk6Mm0OEIYrIXG5kvIicf0d00Ky1DtBoV');
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_51JL5uGHlU0gM5lElMO5y2VEqQtTEpD9sTcEKQaAVjMZ58Ki3EzXYa4vOS2tYVxlVmQSDfD0nuQkt1UwDH4PGfVdE00fHoo4nyA');
define('STRIPE_SUCCESS_URL', 'http://localhost/bacabukulima/payment_success.php');
define('STRIPE_CANCEL_URL', 'http://localhost/bacabukulima/payment_cancel.php');
