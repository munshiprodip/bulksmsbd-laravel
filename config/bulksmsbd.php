<?php

return [

    /*
    |--------------------------------------------------------------------------
    | BulkSMSBD API Credentials
    |--------------------------------------------------------------------------
    |
    | Set your BulkSMSBD API Key and default Sender ID here. You can obtain your
    | API key from the BulkSMSBD admin panel dashboard.
    |
    */

    'api_key' => env('BULKSMSBD_API_KEY', ''),

    'sender_id' => env('BULKSMSBD_SENDER_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Base API URL
    |--------------------------------------------------------------------------
    |
    | The default base URL for the BulkSMSBD HTTP API endpoints.
    |
    */

    'base_url' => env('BULKSMSBD_BASE_URL', 'https://bulksmsbd.net'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout in seconds for API HTTP requests.
    |
    */

    'timeout' => (int) env('BULKSMSBD_TIMEOUT', 15),

    /*
    |--------------------------------------------------------------------------
    | Exception Handling
    |--------------------------------------------------------------------------
    |
    | When set to true, any non-success API response code (1001-1032) or HTTP
    | error will throw a typed BulkSmsBdException. When set to false, responses
    | will be returned as SmsResponse/BalanceResponse DTOs containing the status.
    |
    */

    'throw_exceptions' => (bool) env('BULKSMSBD_THROW_EXCEPTIONS', true),

];
