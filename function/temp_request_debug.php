<?php
header('Content-Type: application/json');
$body = file_get_contents('php://input');
$headers = function_exists('getallheaders') ? getallheaders() : [];
echo json_encode([
    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? null,
    'body' => $body,
    'headers' => $headers,
], JSON_PRETTY_PRINT);
