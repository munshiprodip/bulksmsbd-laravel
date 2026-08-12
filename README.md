# BulkSMSBD Laravel Wrapper Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/munshiprodip/bulksmsbd-laravel.svg?style=flat-square)](https://packagist.org/packages/munshiprodip/bulksmsbd-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/munshiprodip/bulksmsbd-laravel.svg?style=flat-square)](https://packagist.org/packages/munshiprodip/bulksmsbd-laravel)
[![License](https://img.shields.io/packagist/l/munshiprodip/bulksmsbd-laravel.svg?style=flat-square)](LICENSE)

A clean, modern, PSR-12 compliant, production-ready Laravel wrapper package for the **BulkSMSBD** SMS Gateway API.

---

## Features

- **Core Class (`BulkSmsBd\Laravel\BulkSmsBd`)**: Formats and enriches every API response with `status_message` mapped from `response_code` and `is_success` indicator.
- **One-to-Many SMS (`/api/smsapi`)**: Accepts single, comma-separated string, or array of numbers with message content.
- **Many-to-Many SMS (`/api/smsapimany`)**: Accepts array of recipient objects `[['to' => '...', 'message' => '...']]`.
- **Balance Check API (`/api/getBalanceApi`)**: Retrieves remaining credit balance (supports GET and POST methods).
- **Status Code Resolution**: Dedicated `BulkSmsBdException::$statusCodes` and `BulkSmsBdException::getMessageForCode($code)` resolver.

---

## Requirements

- **PHP**: 8.1 or higher
- **Laravel Framework**: 9.x, 10.x, or 11.x

---

## Installation

Install the package via Composer:

```bash
composer require munshiprodip/bulksmsbd-laravel
```

Publish the configuration file (optional):

```bash
php artisan vendor:publish --tag="bulksmsbd-config"
```

---

## 3. Usage Examples in Laravel

```php
use BulkSmsBd\Laravel\Facades\BulkSmsBd;

// 1. Check Credit Balance
$balanceInfo = BulkSmsBd::getBalance();
/*
  Returns: 
  [
    "response_code" => 202,
    "balance" => 49.65,
    "status_message" => "SMS Submitted Successfully",
    "is_success" => true
  ]
*/

// 2. Send Single / Bulk SMS
$response = BulkSmsBd::send('88016xxxxxxxx', 'Your OTP is 1234');

if ($response['is_success']) {
    // SMS Sent
} else {
    // Show user-friendly error
    echo $response['status_message']; // e.g., "Insufficient Balance"
}

// 3. Send Many-to-Many SMS (Distinct messages to different numbers)
$manyResponse = BulkSmsBd::sendMany([
    ['to' => '8801711111111', 'message' => 'Hello User 1, your invoice is ready.'],
    ['to' => '8801822222222', 'message' => 'Hello User 2, your invoice is ready.'],
]);
```

### Direct Status Code Resolution

```php
use BulkSmsBd\Laravel\Exceptions\BulkSmsBdException;

$message = BulkSmsBdException::getMessageForCode(1007);
// Returns: "Insufficient Balance"
```

---

## Author

- **Prodip Munshi** - [munshiprodip@gmail.com](mailto:munshiprodip@gmail.com)

---

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
