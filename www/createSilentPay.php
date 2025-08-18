<?php
require_once __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/keys.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../../vendor/libs/market-core/models/LogErrorGateway.php';

use MarketCore\Models\LogErrorGateway;

$client = new Lyra\Client();
$total = number_format($total_final, 2, '', '');
$token = $payToken;

$store = isset($_GET['requestObject']) ? json_decode($_GET['requestObject']) : [
    "amount" => $total,
    "currency" => "ARS",
    "paymentMethodToken" => $token,
    "formAction" => "SILENT",
    "transactionOptions" => [
        "cardOptions" => ["installmentNumber" => $silent_cuotas]
    ]
];

try {
    $responseSilent = $client->post("V4/Charge/CreatePayment", $store);

    if ($responseSilent['status'] !== 'SUCCESS') {
        throw new \Exception("Error de Lyra SilentPay: " . json_encode($responseSilent['answer']));
    }

    $Token = $responseSilent["answer"]["paymentMethodToken"];
    

} catch (\Throwable $e) {
    LogErrorGateway::registrar(
        $userId ?? null,
        $compraId ?? null,
        "Error en createSilentPay: " . $error['errorCode'],
        $store,
        'createSilentPay'
    );

//    header("Content-Type", "application/json");
//    http_response_code(500);
//    echo json_encode([
//        "error" => "Error al crear pago SILENT",
//        "message" => $e->getMessage()
//    ]);
    header('Location:' . HOME . 'finaliza_compra_error.html');
    echo '<script>window.location.assign("' . HOME . 'finaliza_compra_error.html");</script>';
    
    
    die();
}