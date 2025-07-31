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

    // espelhar os horários
    public function espelharHorarios() {
        $this->horarioServico->espelharHorarios();
    }

    // buscar os horários do salão
    public function buscarHorariosSalao() {
        $this->horarioServico->buscarHorariosSalao();
    }

    // deletar horário
    public function deletar() {
        $this->horarioServico->deletar();
    }

}