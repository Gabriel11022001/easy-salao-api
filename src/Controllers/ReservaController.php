<?php

namespace Controllers;

use Servico\ReservaServico;

class ReservaController {

    /**
     * @property ReservaServico
     */
    private $reservaServico;

    public function __construct()
    {
        $this->reservaServico = new ReservaServico();
    }

    // realizar reserva de horário do salão
    public function realizarReserva() {
        $this->reservaServico->realizarReserva();
    }

    // listar/filtrar as reservas
    public function listar() {
        $this->reservaServico->listar();
    }

}