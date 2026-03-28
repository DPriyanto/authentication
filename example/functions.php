<?php

function renderPage(string $title, array $lines, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: text/html; charset=utf-8');

    echo '<!doctype html>';
    echo '<html lang="en"><head><meta charset="utf-8"><title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>';
    echo '<style>body{font-family:Segoe UI,Tahoma,sans-serif;margin:0;min-height:100vh;padding:24px;box-sizing:border-box;display:grid;place-items:center;background:#f7f7f8;color:#111}';
    echo '.card{width:min(900px,100%);background:#fff;padding:18px 20px;border:1px solid #ddd;border-radius:10px}';
    echo 'h1{margin:0 0 12px;font-size:20px}pre{margin:0;white-space:pre-wrap;word-wrap:break-word;background:#111;color:#eaeaea;padding:14px;border-radius:8px}</style>';
    echo '</head><body><div class="card">';
    echo '<h1>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>';
    echo '<pre>' . htmlspecialchars(implode("\n", $lines), ENT_QUOTES, 'UTF-8') . '</pre>';
    echo '</div></body></html>';
    exit;
}