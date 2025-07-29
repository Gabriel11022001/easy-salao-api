<?php

namespace Models;

class Reserva extends Model {

    private $reservaId;
    private $usuarioId;
    private $usuarioSalaoId;
    private $servicos;
    private $horarioSalaoId;
    private $valorTotal;

    public function __construct() {
        $this->reservaId = 0;
        $this->usuarioId = 0;
        $this->usuarioSalaoId = 0;
        $this->servicos = array();
        $this->horarioSalaoId = 0;
        $this->valorTotal = 0;
    }

    public function setReservaId($reservaId) {
        $this->reservaId = $reservaId;
    }

    public function getReservaId() {

        return $this->reservaId;
    }

    public function setUsuarioId($usuarioId) {
        $this->usuarioId = $usuarioId;
    }

    public function getUsuarioId() {

        return $this->usuarioId;
    }

    public function setUsuarioSalaoId($usuarioSalaoId) {
        $this->usuarioSalaoId = $usuarioSalaoId;
    }

    public function getUsuarioSalaoId() {

        return $this->usuarioSalaoId;
    }

    public function setServicos($servicos) {
        $this->servicos = $servicos;
    }

    public function getServicos() {

        return $this->servicos;
    }

    public function setHorarioSalaoId($horarioSalaoId) {
        $this->horarioSalaoId = $horarioSalaoId;
    }

    public function getHorarioSalaoId() {

        return $this->horarioSalaoId;
    }

    public function setValorTotal($valorTotal) {
        $this->valorTotal = $valorTotal;
    }

    public function getValorTotal() {

        return $this->valorTotal;
    }

    public function toArray() {
        
        return [
            "reserva_id" => $this->getReservaId(),
            "usuario_id" => $this->getUsuarioId(),
            "usuario_salao_id" => $this->getUsuarioSalaoId(),
            "valor_total" => $this->getValorTotal(),
            "horario_id" => $this->getHorarioSalaoId()
        ];
    }

}