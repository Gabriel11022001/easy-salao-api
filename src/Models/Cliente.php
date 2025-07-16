<?php

namespace Models;

class Cliente {

    private $clienteId;
    private $nomeCompleto;
    private $telefone;
    private $email;
    private $clienteEhVip;
    private $usuarioSalaoId;
    private $usuarioId;

    public function __construct(
        $clienteId = 0,
        $nomeCompleto = "",
        $telefone = "",
        $email = "",
        $clienteEhVip = false,
        $usuarioId = 0,
        $usuarioSalaoId = 0
    )
    {
        $this->clienteId = 0;
        $this->nomeCompleto = "";
        $this->telefone = "";
        $this->email = "";
        $this->clienteEhVip = false;
        $this->usuarioSalaoId = 0;
        $this->usuarioId = 0;
    }

    public function setClienteId($clienteId) {
        $this->clienteId = $clienteId;
    }

    public function getClienteId() {

        return $this->clienteId;
    }

    public function setNomeCompleto($nomeCompleto) {
        $this->nomeCompleto = $nomeCompleto;
    }

    public function getNomeCompleto() {

        return $this->nomeCompleto;
    }

    public function setTelefone($telefone) {
        $this->telefone = $telefone;
    }

    public function getTelefone() {

        return $this->telefone;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getEmail() {

        return $this->email;
    }

    public function setClienteEhVip($clienteEhVip) {
        $this->clienteEhVip = $clienteEhVip;
    }

    public function getClienteEhVip() {

        return $this->clienteEhVip;
    }

    public function setUsuarioSalaoId($usuarioSalaoId) {
        $this->usuarioSalaoId = $usuarioSalaoId;
    }

    public function getUsuarioSalaoId() {

        return $this->usuarioSalaoId;
    }

    public function setUsuarioId($usuarioId) {
        $this->usuarioId = $usuarioId;
    }

    public function getUsuarioId() {

        return $this->usuarioId;
    }

    public function toArray() {

        return [
            "cliente_id" => $this->getClienteId(),
            "nome_completo" => $this->getNomeCompleto(),
            "email" => $this->getEmail(),
            "telefone" => $this->getTelefone(),
            "cliente_vip" => $this->getClienteEhVip(),
            "usuario_id" => $this->getUsuarioId(),
            "usuario_salao_id" => $this->getUsuarioSalaoId()
        ];
    }

}