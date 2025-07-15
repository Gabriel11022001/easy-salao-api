<?php

use Controllers\ClienteController;
use Controllers\LoginController;
use Controllers\Rota;
use Controllers\UsuarioController;
use Utils\Resposta;

require_once "autoload.php";
require_once __DIR__ . "/configurar.php";
require_once __DIR__ . "/../src/Utils/getParametro.php";

try {   
    $rota = new Rota();
    $endpoint = $rota->getRotaAtual();

    // efetuar login
    if ($endpoint === "/usuarios/login") {
        $rota->post("/usuarios/login", LoginController::class, "login");
    }

    // cadastrar usuário
    if ($endpoint === "/usuarios/cadastrar") {
        $rota->post("/usuarios/cadastrar", UsuarioController::class, "cadastrar");
    }

    // cadastrar cliente
    if ($endpoint === "/clientes/cadastrar") {
        $rota->post("/clientes/cadastrar", ClienteController::class, "cadastrar");
    }

    // buscar todos os clientes do representante do salão
    if ($endpoint === "/clientes/listar") {
        $rota->get("/clientes/listar", ClienteController::class, "buscarTodos");
    }

    Resposta::response(false, "404 - Rota inválida.");
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "<br>";
}