<?php

namespace Controllers;

use Servico\ClienteServico;

class ClienteController {

    private ClienteServico $clienteServico;

    public function __construct()
    {
        $this->clienteServico = new ClienteServico();
    }

    // cadastrar cliente
    public function cadastrar() {
        $this->clienteServico->cadastrarCliente();
    }

    // buscar todos os clientes do representante de salão
    public function buscarTodos() {
        $this->clienteServico->buscarTodos();
    }

}