<?php
if (isset($_POST['submit'])) {
    date_default_timezone_set('Africa/Nairobi');

    // Configuration
    $consumerKey = '4YwHi4pVqVCNKYzGiFXG6fS9P5gFz9KhvjYuLEde2ysyeM5U';
    $consumerSecret = '5wp0yrE3YlEGD6ihauNOgTsC3KtO9xiTdVSeSe16uUwz4FodJD2LWUHn61wTiQwb';
    $BusinessShortCode = '4140577';
    $Passkey = '3cf6e2fff13ba64211ff8e1e83f111630f0ad55450b70732404d629cd4d9a1bf';  
    $CallbackURL = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'; // Replace with your actual callback URL

    $PartyA = $_POST['phonenumber']; // Phone number
    $AccountReference = 'BITEC NETWORKS';
    $TransactionDesc = 'Buy Wifi';
    $Amount = $_POST['amount'];;
    $Timestamp = date('YmdHis');
    $Password = base64_encode($BusinessShortCode . $Passkey . $Timestamp);

    // Function to get access token
    function generateAccessToken($consumerKey, $consumerSecret) {
        $credentials = base64_encode($consumerKey . ':' . $consumerSecret);
        $url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'; 

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl)) {
            throw new Exception('cURL Error: ' . curl_error($curl));
        }

        curl_close($curl);

        if ($http_status != 200) {
            throw new Exception('Failed to obtain access token. HTTP Status: ' . $http_status);
        }

        $result = json_decode($response);
        if (isset($result->access_token)) {
            return $result->access_token;
        } else {
            throw new Exception('Failed to decode access token response.');
        }
    }

    try {
        // Obtain access token
        $accessToken = generateAccessToken($consumerKey, $consumerSecret);

        // STK Push request
        $stkUrl = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $stkHeaders = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ];

        $stkData = [
            'BusinessShortCode' => $BusinessShortCode,
            'Password' => $Password,
            'Timestamp' => $Timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $Amount,
            'PartyA' => $PartyA,
            'PartyB' => $BusinessShortCode,
            'PhoneNumber' => $PartyA,
            'CallBackURL' => $CallbackURL,
            'AccountReference' => $AccountReference,
            'TransactionDesc' => $TransactionDesc
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $stkUrl);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $stkHeaders);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($stkData));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl)) {
            throw new Exception('cURL Error: ' . curl_error($curl));
        }

        curl_close($curl);

        if ($http_code != 200) {
            throw new Exception('STK Push request failed. HTTP Status: ' . $http_code);
        }

        echo "STK Push Request Successful:\n";
        echo $response;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
