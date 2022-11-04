<?php

// Include the configuration file
require_once 'config.php';

// Include the checkout file
// require_once 'checkout.php';

// Include the Stripe PHP library
require_once './stripe-php/init.php';

// Set API key
$stripe = new \Stripe\StripeClient(STRIPE_API_KEY);

// Default response
$response = array(
    'status' => 0,
    'error' => array(
        'message' => 'Invalid Request'
    )
);

// Get request data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = file_get_contents('php://input');
    $request = json_encode($response);
    exit;
}

if (!empty($request->createCheckOutSession)) {
    // Convert product price to cent
    $stripeAmount = round($grand_total * 100, 2);

    // Create new Checkout Session for the order
    try {
        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => [
                [
                    'price_data' => [
                        'product_data' => [
                            'name' => $productName,
                            'metadata' => [
                                'pro_id' => $productID
                            ]
                        ],
                        'unit_amount' => $stripeAmount,
                        'currency' => $currency,
                    ],
                    'quantity' => 1
                ]
            ],
            'mode' => 'payment',
            'success_url' => STRIPE_SUCCESS_URL.'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => STRIPE_CANCEL_URL,
        ]);
    } catch(Exception $e) {
        $api_error = $e->getMessage();
    }

    if(empty($api_error) && $checkout_session) {
        $response = array(
            'status' => 1,
            'message' => 'Checkout Session created successfully',
            'sessionId' => $checkout_session->id
        )
    }else{
        $response = array(
            'status' => 0,
            'error' => array(
                'message' => 'Checkout Session created failed. '.$api_error
            )
        );
    }
}

// Return response
echo json_encode($response);


?>
