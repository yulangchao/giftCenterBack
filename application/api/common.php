<?php
require __DIR__ . '/../../vendor/autoload.php';
use Twilio\Rest\Client;

function sendSMS($phone,$body){
    $account_sid = 'AC96f62189508816d46cf6188453dc44c1';
    $auth_token = '8b3964668371d093e696d3b2025a72d9';
    // In production, these should be environment variables. E.g.:
    // $auth_token = $_ENV["TWILIO_ACCOUNT_SID"]
    
    // A Twilio number you own with SMS capabilities
    $twilio_number = "+16042299404";
    
    $client = new Client($account_sid, $auth_token);
    $result = $client->messages->create(
        // Where to send a text message (your cell phone?)
        $phone,
        array(
            'from' => $twilio_number,
            'body' => $body
        )
    );
    return $result;
}
