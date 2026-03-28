"# PHP Authentication API

A lightweight PHP API authentication system using HMAC-SHA256 signatures and API keys for secure merchant-to-merchant communication.

## Features

- **API Key Authentication**: Secure API key-based authentication system
- **HMAC-SHA256 Signatures**: Request verification using cryptographic signatures
- **Merchant Management**: Support for multiple merchants with public/private key pairs
- **JSON API**: Clean JSON response format for all endpoints
- **Error Handling**: Comprehensive error handling with meaningful error messages
- **MySQL Database**: Persistent storage using MySQL with PDO

## Requirements

- PHP 7.4+
- MySQL 8.0+
- PDO PHP Extension
- Composer (optional, for dependency management)

## Installation

1. **Clone or download the project**
   ```bash
   cd /path/to/authentication
   ```

2. **Configure database credentials**
   
   Update `config.php` with your database credentials:
   ```php
   $db = [
       'host' => 'localhost',
       'name' => 'php_authentication',
       'username' => 'homestead',
       'password' => 'secret',
       'charset' => 'utf8mb4'
   ];
   ```

3. **Create the database**
   
   Import the SQL schema:
   ```bash
   mysql -u homestead -p php_authentication < schema.sql
   ```

4. **Set up web server**
   
   Point your web server to this directory. The project is configured for use with XAMPP/Vagrant.

## Configuration

### Database Configuration (`config.php`)

```php
$db = [
    'host' => 'localhost',              // Database host
    'name' => 'php_authentication',      // Database name
    'username' => 'homestead',           // Database username
    'password' => 'secret',              // Database password
    'charset' => 'utf8mb4'               // Character set
];

$php_cli_path = '/usr/bin/php';         // PHP CLI path for background tasks
```

## API Authentication

This API uses a two-layer authentication mechanism:

### 1. API Key (Authorization Header)

```
Authorization: your-public-api-key
```

The public key identifies the merchant making the request.

### 2. Request Signature (X-Signature Header)

```
X-Signature: base64-encoded-hmac-sha256-signature
```

The signature is computed using HMAC-SHA256 with:
- **Message**: JSON-encoded request body
- **Key**: Merchant's private key

### Generating a Signature

```php
function generate_signature($data, $private_key)
{
    $string = json_encode($data);
    return base64_encode(hash_hmac('sha256', $string, $private_key, true));
}
```

### Example Request

```bash
curl -X POST http://localhost/authentication/test.php \
  -H "Content-Type: application/json" \
  -H "Authorization: your-public-key" \
  -H "X-Signature: generated-signature" \
  -d '{"data": "your payload"}'
```

## Database Schema

### merchants table
Stores merchant information and API keys.

```sql
CREATE TABLE merchants (
  id int PRIMARY KEY AUTO_INCREMENT,
  created_at datetime,
  modified_at datetime,
  name varchar(255),
  public_key varchar(255),
  private_key varchar(255)
)
```

## Project Structure

```
authentication/
├── README.md              # This file
├── config.php             # Database configuration
├── db.php                 # Database connection setup
├── functions.php          # Authentication and utility functions
├── generate.php           # API key generation utility
├── schema.sql             # Database schema
├── test.php               # Test/example endpoint
├── bruno/                 # API testing files
│   └── Authentication/
│       ├── bruno.json     # Bruno project config
│       └── Test.bru       # API test cases
└── authentication.code-workspace
```

## Core Functions

### authenticate($db)
Validates the request's API key and signature, returns authenticated merchant data.

```php
$auth = authenticate($pdo);
// Returns: ['user' => merchant_data, 'payload' => request_payload]
```

### generate_signature($data, $private_key)
Generates an HMAC-SHA256 signature for request data.

```php
$signature = generate_signature($payload, $private_key);
```

### verify_signature($data, $private_key)
Verifies that the X-Signature header matches the computed signature.

```php
$is_valid = verify_signature($payload, $private_key);
```

### returnJson($data)
Sends a JSON response with appropriate HTTP status code.

```php
returnJson([
    'status' => 'ok',
    'message' => 'Success',
    'code' => 200,
    'item' => $result
]);
```

## Usage Examples

### Using the Test Endpoint

The `test.php` file demonstrates a protected endpoint:

```php
$authenticated = authenticate($pdo);

// Access merchant data
$merchant_id = $authenticated['user']['id'];
$merchant_name = $authenticated['user']['name'];

// Access request payload
$payload = $authenticated['payload'];

// Return JSON response
returnJson([
    'status' => 'ok',
    'message' => 'Authenticated user: ' . $authenticated['user']['name'],
    'item' => [
        'user' => [
            'id' => $authenticated['user']['id'],
            'name' => $authenticated['user']['name']
        ],
        'payload' => $authenticated['payload']
    ]
]);
```

## Testing

### Using Bruno

API testing is configured in the `bruno/` directory using Bruno API client:

1. Open Bruno and load the `bruno.json` project configuration
2. Run the `Test.bru` test cases
3. Tests include authentication validation and signature verification

### Manual Testing

# Via HTTP
curl -X POST http://localhost/authentication/test.php \
  -H "Content-Type: application/json" \
  -H "Authorization: your-public-key" \
  -H "X-Signature: your-signature" \
  -d '{"test": "data"}'
```

## Error Handling

The API returns JSON error responses for all exceptions:

```json
{
  "status": "error",
  "message": "API Key not provided",
  "code": 401
}
```

Common error codes:
- `401`: Missing or invalid authentication (API Key or Signature)
- `404`: Invalid API Key
- `400`: Invalid JSON body

## Security Considerations

1. **Private Keys**: Keep merchant private keys secure and never expose them
2. **HTTPS**: Always use HTTPS in production
3. **Signature Verification**: All requests must include a valid X-Signature header
4. **Database Security**: Use strong database credentials and limit access
5. **Character Encoding**: All data is UTF-8 encoded for security

## Development

### Generate API Keys

Use `generate.php` to create new merchant API keys:

```bash
php generate.php
```

### Database Utilities

Import the schema into a fresh database:

```bash
mysql -u homestead -p php_authentication < schema.sql
```

## API Response Format

All endpoints return a standardized JSON response:

```json
{
  "status": "ok|error",
  "message": "Human-readable message",
  "code": 200,
  "item": {}
}
```

## License

This project is provided as-is for authentication implementation in PHP applications.

## Support

For issues or questions:
1. Check the `bruno/` test cases for usage examples
2. Review the authentication function in `functions.php`
3. Verify database connectivity in `db.php`
4. Check `config.php` for correct database credentials

---

**Last Updated**: March 2026" 
