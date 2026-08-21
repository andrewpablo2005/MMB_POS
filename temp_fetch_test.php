<?php
function post($url, $data) {
    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\n",
            'content' => json_encode($data),
            'ignore_errors' => true,
        ],
    ];
    $context = stream_context_create($options);
    return file_get_contents($url, false, $context);
}

$url = 'http://127.0.0.1/MMBPOS/function/process_return.php';
$payload = [
    'original_transaction_id' => 1,
    'void_pin' => 'OWNER_AUTO',
    'refund_method' => 'Cash',
    'reason' => 'Test',
    'items' => [[ 'product_id' => 1, 'qty' => 1, 'price' => 1.00, 'is_restockable' => 1 ]],
];

echo "URL: $url\n";
echo "POST response:\n";
echo post($url, $payload);

$resp = file_get_contents($url);
echo "\nGET response:\n";
echo $resp;
