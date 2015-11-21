<?php
/**
 * Created by PhpStorm.
 * User: teliov
 * Date: 11/19/15
 * Time: 9:19 AM
 */

require_once __DIR__ . '/vendor/autoload.php';

include_once('curl.php');

use GuzzleHttp\Client;

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

$pagaCredentials = json_decode(file_get_contents("credentials.json"), true);
$pagaCredentials = $pagaCredentials['paga_credentials'];
$pagaCredentials = array_merge($pagaCredentials, [
    'redirect_uri' => getBase()."/redirect",
    "return_url" => getBase()."/paid"
]);

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


$klein->respond(array('GET', 'POST'), '/return?/?', function(\Klein\Request $request, \Klein\Response $response){
    $data = $_REQUEST;

    $status = $data['status']?: null;
    if (!$status){
        // TODO redirect the user to a failure page
    }
    $status = strtolower($status);
    switch ($status){
        case "success":
            return $response->json("Payment successful");

        case "error_other":
            return $response->json("some hold up from paga servers");

        case "error_cancelled":
            return $response->json("The user cancelled the request");

        default:
            return $response->json("Unknown status, do some really cools stuff here");
    }
});

$klein->respond(array('GET', 'POST'), '/paid?/?', function(\Klein\Request $request, \Klein\Response $response){
    $data = $_REQUEST;
    return $response->json($data);
});

$klein->respond(array('GET', 'POST'), '/redirect?/?', function(\Klein\Request $request, \Klein\Response $response) use ($pagaCredentials){
    $data = $_REQUEST;
    $query = $request->paramsGet() ? $request->paramsGet()->all() : [];
    $data = array_merge($data, $query);
    if (isset($data['error'])){
        return $response->json($data);
    }


    $url = "https://mypaga.com/paga-webservices/oauth2/token";
    $handler = new \GuzzleHttp\Handler\CurlHandler();
    $stack = \GuzzleHttp\HandlerStack::create($handler);
    $client = new Client(['base_uri'=> $url, 'handler'=>$stack]);
    $auth = [$pagaCredentials['client_id'], $pagaCredentials['credentials']];
    $payload = [
        'grant_type' => "authorization_code",
        'redirect_uri' => $pagaCredentials['redirect_uri'],
        'code' => $data['code'],
        'scope' => $pagaCredentials['scope']
    ];
    $dataString = "";
    foreach($payload as $key=>$value){
        $dataString.=$key.'='.$value;
    }

    $curl = new Curl();
    $curl->setHeader('Authorization',  "Basic ".base64_encode($pagaCredentials['client_id'].":".$pagaCredentials['credentials']));
    //$curl->setHeader('Content-Type', 'application/json');
    $curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
    $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);

    $curl->post($url, $payload);
    $pagaResponse = $curl->response;
    return $response->json($pagaResponse);
});

$klein->dispatch();

