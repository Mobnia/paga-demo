<?php
/**
 * Created by PhpStorm.
 * User: teliov
 * Date: 11/19/15
 * Time: 9:19 AM
 */

require_once __DIR__ . '/vendor/autoload.php';

$klein = new \Klein\Klein();

function getBase($project = NULL, $project_dir = NULL, $scheme = NULL) {
    $scheme = $scheme ? $scheme : "http://";
    $project = $project ? $project : $_SERVER['SERVER_NAME'];
    $project_dir = $project_dir ? $project_dir : "";
    return "$scheme$project$project_dir";
}

$cartItems = [
    [
        "qty" => 1,
        "name" => "Birthday Cake",
        "price" => 5000
    ],

    [
        "qty" => 100,
        "name" => "Party Cups",
        "price" => 50
    ],

    [
        "qty" => 10,
        "name" => "Beer (crate)",
        "price" => 50000
    ],

    [
        "qty" => 18,
        "name" => "Meat (1 kilo)",
        "price" => 500
    ],

    [
        "qty" => 1,
        "name" => "Bullet Proof Vest",
        "price" => 50000
    ],
];

$pagaCredentials = [
    "principal" => "",
    "client_id" => "E0B62936-F8BB-4713-9AC1-8C5127A050C2",
    "scope" => "MERCHANT_PAYMENT MONEY_TRANSFER",
    "redirect_uri" => getBase()."/redirect",
    "return_url" => getBase()."/paid",
    "response_type" => "code",
    "auth_url" => "https://mypaga.com/paga-webservices/oauth2/authorization/"
];

$klein->respond('GET', '/', function(\Klein\Request $request, \Klein\Response $response, \Klein\ServiceProvider $service) use($cartItems){
    $service->title = "Checkout Cart";
    $service->base = getBase();
    $service->items = $cartItems;
    $service->render(__DIR__ . '/views/cart.php');
});


$klein->respond('POST', '/pay?/?', function(\Klein\Request $request, \Klein\Response $response, \Klein\ServiceProvider $service) use($pagaCredentials){
    $data = json_decode($request->body(), true);
    if ($data){
        $data = array_merge($data, $request->paramsPost()->all());
    }

    $total = $data['total'];
    $productCode = $data['product_code'];
    $customer = $data['customer'];
    $invoice = $data['invoice'];


    $query = [
        'client_id' => $pagaCredentials['client_id'],
        'response_type' => $pagaCredentials['response_type'],
        'redirect_uri' => $pagaCredentials['redirect_uri'],
        'scope' => $pagaCredentials['scope'],
        'k' => $pagaCredentials['client_id'],
        'customer_account' => $customer,
        'subtotal' => $total,
        'product_code' => $productCode,
        'email' => $customer,
        'return_url' => $pagaCredentials['return_url'],
        'invoice' => $invoice
    ];

    if (isset($data['with_card'])){
        $query['method'] = $data['card_type'];
    }

    $queryString = http_build_query($query);

    $url = $pagaCredentials['auth_url']."?".$queryString;

    return $response->json([
        'url' => $url
    ]);

});

$klein->dispatch();

