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

    // listar serviços fornecidos pelo salão de beleza
    public function buscarServicosSalao() {
        $this->servicoSalaoServico->buscarServicosSalao();
    }

    // buscar serviço do salão pelo id
    public function buscarPeloId() {
        $this->servicoSalaoServico->buscarPeloId();
    }

}