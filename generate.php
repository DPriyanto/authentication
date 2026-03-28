<?php
// Generate API keys for merchants that don't have them yet. This is a one-time script that can be run from the command line.

require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';

try {
    // get merchants where public_key is null (no API key generated yet)
    $stmt = $pdo->query('SELECT * FROM merchants WHERE public_key IS NULL OR private_key IS NULL');
    $merchants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($merchants)) throw new Exception('No merchants found without API keys');

    // generate a new API key for the first merchant found
    foreach ($merchants as $merchant) {
        $public_key = $merchant['public_key'] != '' ?: bin2hex(random_bytes(32)); // generate a random 32-character hex string
        $private_key = $merchant['private_key'] != '' ?: bin2hex(random_bytes(32)); // generate a random 32-character hex string

        // update the merchant record with the new API keys
        $stmt = $pdo->prepare('UPDATE merchants SET public_key = :public_key, private_key = :private_key WHERE id = :id');
        $stmt->execute([
            'public_key' => $public_key,
            'private_key' => $private_key,
            'id' => $merchant['id']
        ]);
        if ($stmt->rowCount() === 0) {
            echo "\e[33mFailed to update merchant ID " . $merchant['id'] . "\e[0m\n";
            continue;
        }
        echo "\e[32mGenerated API keys for merchant ID " . $merchant['id'] . "\e[0m\n";
    }
} catch (Exception $e) {
    echo "\e[31mError: " . $e->getMessage() . "\e[0m\n";
    exit(1);
}
