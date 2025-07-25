<?php

namespace Models;

class Horario extends Model {

    private $horariId;
    private $usuarioSalaoId;
    private $ano;
    private $mes;
    private $dia;
    private $de;
    private $ate;
    private $reservado;

    public function __construct()
    {
        $this->horariId = 0;
        $this->usuarioSalaoId = 0;
        $this->ano = 0;
        $this->mes = "";
        $this->de = "";
        $this->ate = "";
        $this->reservado = false;
        $this->dia = 0;
    }

    public function setHorarioId($horariId) {
        $this->horariId = $horariId;
    }

    public function getHorarioId() {

        return $this->horariId;
    }

    public function setUsuarioSalaoId($usuarioSalaoId) {
        $this->usuarioSalaoId = $usuarioSalaoId;
    }

    public function getUsuarioSalaoId() {

        return $this->usuarioSalaoId;
    }

    public function setAno($ano) {
        $this->ano = $ano;
    }

    public function getAno() {

        return $this->ano;
    }

    public function setMes($mes) {
        $this->mes = $mes;
    }

    public function getMes() {

        return $this->mes;
    }

    public function setDia($dia) {
        $this->dia = $dia;
    }

    public function getDia() {

        return $this->dia;
    }

    public function setDe($de) {
        $this->de = $de;
    }

    public function getDe() {

        return $this->de;
    }

    public function setAte($ate) {
        $this->ate = $ate;
    }

    public function getAte() {

        return $this->ate;
    }

    public function setReservado($reservado) {
        $this->reservado = $reservado;
    }

    public function getReservado() {

        return $this->reservado;
    }

    public function toArray() {

        return [
            "horario_id" => $this->getHorarioId(),
            "ano" => $this->getAno(),
            "mes" => $this->getMes(),
            "dia" => $this->getDia(),
            "horario_de" => $this->getDe(),
            "horario_ate" => $this->getAte(),
            "usuario_salao_id" => $this->getUsuarioSalaoId()
        ];
    }

}
