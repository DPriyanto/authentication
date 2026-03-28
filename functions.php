<?php
$data = [
    'status' => 'ok',
    'message' => '',
    'code' => 200
];

function dd($arr, $exit = false)
{
    print_r($arr);
    if ($exit) {
        exit;
    }
}

function handleException($e)
{
    global $data;
    $data['status'] = 'error';
    $data['message'] = $e->getMessage();
    $data['code'] = $e->getCode() ?: 200;
}

function returnJson($data)
{
    header('Content-Type: application/json');
    http_response_code(is_numeric($data['code']) ? $data['code'] : 200);
    echo json_encode($data);
}

// check headers for authentication from api_keys and return merchant if found
function authenticate($db)
{
    global $data;

    try {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) throw new Exception('API Key not provided', 401);

        $public_key = $headers['Authorization'];

        $stmt = $db->prepare('SELECT * FROM merchants WHERE public_key = :public_key');
        $stmt->execute(['public_key' => $public_key]);
        $merchant = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$merchant) throw new Exception('Invalid API Key', 404);

        if (!isset($headers['X-Signature'])) throw new Exception('X-Signature header not provided', 401);

        // read json from php://input and decode it
        $body = file_get_contents('php://input');
        $payload = json_decode($body, true);

        if ($payload === null) throw new Exception('Invalid JSON body');

        if (!verify_signature($payload, $merchant['private_key'])) throw new Exception('Invalid signature', 401);

        // return $merchant;
        return [
            'user' => $merchant,
            'payload' => $payload
        ];
    } catch (Exception $e) {
        handleException($e);

        returnJson($data);
        exit;
    }
}

function generate_signature($data, $private_key)
{
    // create a string from the data array
    $string = json_encode($data);
    return base64_encode(hash_hmac('sha256', $string, $private_key, true));
}


function verify_signature($data, $private_key)
{
    $headers = getallheaders();
    $xsignature = $headers['X-Signature'] ?? '';
    $expected_signature = generate_signature($data, $private_key);

    return hash_equals($expected_signature, $xsignature);
}
