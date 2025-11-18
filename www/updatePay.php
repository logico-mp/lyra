<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/keys.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../../vendor/libs/market-core/models/LogErrorGateway.php';

use MarketCore\Models\LogErrorGateway;

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

    // Log temprano si viene raro
    if (!isset($responseUpdate['status'])) {
        LogErrorGateway::registrar(
            $userId ?? null,
            $compraId ?? null,
            "Lyra Update sin status",
            ['request' => $store, 'response' => $responseUpdate],
            'updatePay'
        );
        throw new \Exception("Lyra Update sin status");
    }

    if ($responseUpdate['status'] !== 'SUCCESS') {
        LogErrorGateway::registrar(
            $userId ?? null,
            $compraId ?? null,
            "Lyra Update status != SUCCESS",
            ['request' => $store, 'response' => $responseUpdate],
            'updatePay'
        );

        // Si querés ver el error:
        // $errorAnswer = isset($responseUpdate['answer']) ? $responseUpdate['answer'] : null;
        throw new \Exception("Error de Lyra Update");
    }

    // Si llega acá, fue SUCCESS
    $formToken = $responseUpdate["answer"]["formToken"] ?? null;
    
    
} catch (\Throwable $e) {
    LogErrorGateway::registrar(
        $userId ?? null,
        $compraId ?? null,
        "Excepción en updatePay: " . $e->getMessage(),
        [
            'request' => $store,
            'trace'   => $e->getTraceAsString(),
        ],
        'updatePay'
    );

//    header("Content-Type", "application/json");
//    http_response_code(500);
//    echo json_encode([
//        "error" => "Error al actualizar la transacción",
//        "message" => $e->getMessage()
//    ]);
    header('Location:' . HOME . 'finaliza_compra_error.html');
    echo '<script>window.location.assign("' . HOME . 'finaliza_compra_error.html");</script>';
    
    
    die();
}