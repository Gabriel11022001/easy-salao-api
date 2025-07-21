<?php

namespace Controllers;

use Servico\ServicoSalaoServico;

class ServicoSalaoController {

    private ServicoSalaoServico $servicoSalaoServico;

    public function __construct()
    {
        $this->servicoSalaoServico = new ServicoSalaoServico();
    }

    // cadastrar serviço do salão
    public function cadastrar() {
        $this->servicoSalaoServico->cadastrar();
    }

}