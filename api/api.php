<?php

use Controllers\ClienteController;
use Controllers\HorarioController;
use Controllers\LoginController;
use Controllers\Rota;
use Controllers\ServicoSalaoController;
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

    // buscar cliente pelo id
    if ($endpoint === "/clientes/consultar") {
        $rota->get("/clientes/consultar", ClienteController::class, "buscarPeloId");
    }

    // cadastrar serviço do salão
    if ($endpoint === "/servicos/salao/cadastrar") {
        $rota->post("/servicos/salao/cadastrar", ServicoSalaoController::class, "cadastrar");
    }

    // listar os serviços fornecidos pelo salão de beleza
    if ($endpoint === "/servicos/salao/listar") {
        $rota->get("/servicos/salao/listar", ServicoSalaoController::class, "buscarServicosSalao");
    }

    // buscar serviço do salão pelo id
    if ($endpoint === "/servicos/salao/buscar-pelo-id") {
        $rota->get("/servicos/salao/buscar-pelo-id", ServicoSalaoController::class, "buscarPeloId");
    }

    // cadastrar horário
    if ($endpoint === "/horarios/cadastrar") {
        $rota->post("/horarios/cadastrar", HorarioController::class, "cadastrar");
    }

    Resposta::response(false, "404 - Rota inválida.");
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "<br>";
}