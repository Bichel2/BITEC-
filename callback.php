<?php
// Safaricom sends a JSON payload to this URL as a callback for the STK Push
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the JSON payload sent by Safaricom
    $callback_data = file_get_contents('php://input');
    $json_data = json_decode($callback_data, true);

    // Log the raw callback response for debugging purposes
    file_put_contents('mpesa_callback_log.txt', $callback_data . PHP_EOL, FILE_APPEND);

    // Check if we received a valid response
    if (isset($json_data['Body']['stkCallback'])) {
        $stkCallback = $json_data['Body']['stkCallback'];

        // If payment was successful, `ResultCode` will be 0
        if ($stkCallback['ResultCode'] == 0) {
            // Payment successful
            $amount = $stkCallback['CallbackMetadata']['Item'][0]['Value']; // Amount paid
            $mpesa_receipt = $stkCallback['CallbackMetadata']['Item'][1]['Value']; // M-Pesa receipt number

            // Log the successful transaction
            file_put_contents('mpesa_success_log.txt', "Payment received: Amount: $amount, Receipt: $mpesa_receipt" . PHP_EOL, FILE_APPEND);

            // Redirect to success page or external site (like Google)
            echo "<script>alert('Payment successful! Enjoy your WiFi.'); window.location.href='https://www.google.com';</script>";
            exit;
        } else {
            // Payment failed
            $error_message = $stkCallback['ResultDesc'];

            // Log the error
            file_put_contents('mpesa_error_log.txt', "Payment failed: $error_message" . PHP_EOL, FILE_APPEND);

            // Redirect to home page or another page with an error message
            echo "<script>alert('Payment failed! Please try again.'); window.location.href='/index.php';</script>";
            exit;
        }
    } else {
        // Invalid callback structure received from Safaricom
        file_put_contents('mpesa_error_log.txt', "Invalid callback structure received" . PHP_EOL, FILE_APPEND);

        // Redirect to home page with an error message
        echo "<script>alert('Invalid response received! Please try again.'); window.location.href='/index.php';</script>";
        exit;
    }
}
?>
