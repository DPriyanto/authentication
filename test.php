<?php

require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';

try {
    $authenticated = authenticate($pdo);

    $data['message'] = 'Authenticated user: ' . $authenticated['user']['name'];

    // do something for this endpoint, for example return the authenticated user and the payload

    $data['item'] = [
        'user' => [
            'id' => $authenticated['user']['id'],
            'name' => $authenticated['user']['name']
        ],
        'payload' => $authenticated['payload']
    ];
} catch (Exception $e) {
	handleException($e);
}

returnJson($data);
