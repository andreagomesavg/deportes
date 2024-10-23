<?php
declare(strict_types=1);
require("src/ErrorHandler.php");
set_exception_handler("ErrorHandler::handleException");
set_error_handler("ErrorHandler::handleError");
require("src/Database.php");
require("src/Controllers/SociosController.php");
require("src/Controllers/PistasController.php");
require("src/Gateways/SociosGateway.php");
require("src/Gateways/PistasGateway.php");
require("src/Controllers/ReservasController.php");
require("src/Gateways/ReservasGateway.php");

$database = new Database("localhost", "deportes_db", "root", "");
$gatewaySocios = new SociosGateway($database);
$controllerSocios = new SociosController($gatewaySocios);
$gatewayPistas = new PistasGateway($database);
$controllerPistas = new PistasController($gatewayPistas);
$gatewayReservas = new ReservasGateway($database);
$controllerReservas = new ReservasController($gatewayReservas);

header("Content-type: application/json; charset=UTF-8");
$parts = explode("/",$_SERVER["REQUEST_URI"]);
$endpoint= $parts[2];
$id = $parts[3] ?? null;
$method = $_SERVER["REQUEST_METHOD"];

switch ($endpoint) {
    case "socios":
        $controllerSocios-> processsRequestSocios($method, $id);
        break;
    case "reservas":
        $controllerReservas -> processsRequestReservas($method, $id);
        break;
    case "pistas":
        $controllerPistas->processRequestPistas($method, $id);
        break;
    default:
        http_response_code(404);
        break;
}