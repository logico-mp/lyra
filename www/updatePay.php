<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/keys.php';
require_once __DIR__ . '/helpers.php';
require_once '/var/www/html/amex/vendor/libs/market-core/models/LogErrorGateway.php';

$client = new Lyra\Client();
$total = number_format($total_final, 2, '', '');
$uuid = $uu_id;

$store = isset($_GET['requestObject']) ? json_decode($_GET['requestObject']) : [
    "amount" => $total,
    "uuid" => $uuid,
    "cardUpdate" => [
        "amount" => $total,
        "currency" => "ARS"
    ]
];

try {
    $responseUpdate = $client->post("V4/Transaction/Update", $store);

    if ($responseUpdate['status'] !== 'SUCCESS') {
        throw new \Exception("Error de Lyra Update: " . json_encode($responseUpdate['answer']));
    }

    $formToken = $responseUpdate["answer"]["formToken"];

} catch (\Throwable $e) {
    LogErrorGateway::registrar(
        $userId ?? null,
        $compraId ?? null,
        "Error en updatePay: " . $error['errorCode'],
        $store,
        'updatePay'
    );
    header("Content-Type", "application/json");
    http_response_code(500);
    echo json_encode([
        "error" => "Error al actualizar la transacción",
        "message" => $e->getMessage()
    ]);
    die();
}