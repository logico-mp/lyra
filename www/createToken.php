<?php
/**
 * I initialize the PHP SDK
 */
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/keys.php';
require_once __DIR__ . '/helpers.php';
/**
 * Initialize the SDK
 * see keys.php
 */
$client = new Lyra\Client();
$total = number_format($total_final, 2, '', '');
//$total = $total . 0000;
if (isset($_GET['requestObject'])) {
    $store = json_decode($_GET['requestObject']);
} else {
    $store = array( "amount" => $total,
                    "formAction" => "REGISTER_PAY",
                    "currency" => "ARS",
                    "customer" =>
                        array(
                            "email" => $_usuario->get_email(),
                             "billingDetails" => array(
                                 "firstName" => $_usuario->get_nombre(),
                                 "lastName" => $_usuario->get_apellido(),
//                                 "phoneNumber" => $compra->idDomicilio->telefono,
//                                 "streetNumber" => is_string( str_replace(array(',', '.'), '', $compra->idDomicilio->numero)),
//                                 "address" => is_string( str_replace(array(',', '.'), '', $compra->idDomicilio->calle)),
//                                 "zipCode" => is_string($compra->idDomicilio->cp),
//                                 "city" => is_string($compra->idDomicilio->localidad),
//                                 "state" => $compra->idDomicilio->provinciaId->nombre,
                                 "identityCode" => $_usuario->get_id()
                             ),
                        "reference" => $compra->get_id()
                        ),
                    "orderId" => uniqid($compra->get_id()),
                    "transactionOptions" => array(
                        "cardOptions" => array(
                            "restrictedInstallments" =>  array(1, 3,8)
                        )
                    ),
                    "strongAuthentication" => "CHALLENGE_MANDATE"
//                    "strongAuthentication" => "CHALLENGE_REQUESTED"

                    );
}

/**
 * I create a formToken
 */

$response = $client->post("V4/Charge/CreatePayment", $store);

//* I check if there are some errors */
if ($response['status'] != 'SUCCESS') {
    /* an error occurs */
    $error = $response['answer'];
    header("Content-Type", "application/json");
    header('HTTP/1.1 500 Internal Server Error');
    echo '{"error": "' . $error['errorCode'] . '", "_type": "DemoError" }';
    die();
}

/* everything is fine, I extract the formToken */
$formToken = $response["answer"]["formToken"];

//header("Content-Type", "application/json");
//echo '{"formToken": "' . $formToken . '"", "_type": "DemoFormToken" }';
