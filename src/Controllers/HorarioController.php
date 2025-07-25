<?php

namespace Controllers;

use Servico\HorarioServico;

class HorarioController {

    /**
     * @property HorarioServico
     */
    private $horarioServico;

    public function __construct()
    {
        $this->horarioServico = new HorarioServico();
    }

    // cadastrar horário do serviço
    public function cadastrar() {
        $this->horarioServico->cadastrar();
    }

}