# Example: Test test.php endpoint

This folder contains the minimum files needed to test `test.php` with valid HMAC authentication.

## Files

- `request.php`: Builds a payload array, signs it, and sends a POST request.
- `functions.php`: Shared output helper used by the example page.

## Setup

1. Open `request.php` and set these PHP variables:
   - `$baseUrl`: Base URL of your app (for example, `http://localhost/authentication/`).
   - `$publicKey`: Merchant public key (Authorization header).
   - `$privateKey`: Merchant private key (for signature).
2. Optionally adjust `$endpoint` and the `$payload` array inside `request.php`.

## Run

Open this URL in your browser:

```text
http://localhost/authentication/example/request.php
```

Expected output:

- A result page showing target URL, HTTP status code, and JSON response from `test.php`
