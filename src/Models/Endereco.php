<?php

namespace Models;

class Endereco {

    private $enderecoId;
    private $cep;
    private $complemento;
    private $logradouro;
    private $cidade;
    private $bairro;
    private $estado;
    private $numero;
    private $usuarioId;

    public function __construct()
    {
        $this->enderecoId = 0;
        $this->cep = "";
        $this->complemento = "";
        $this->logradouro = "";
        $this->cidade = "";
        $this->bairro = "";
        $this->estado = "";
        $this->numero = "";
        $this->usuarioId = 0;
    }

    public function setEnderecoId($enderecoId) {
        $this->enderecoId = $enderecoId;
    }

    public function getEnderecoId() {

        return $this->enderecoId;
    }

    public function setCep($cep) {
        $this->cep = $cep;
    }

    public function getCep() {

        return $this->cep;
    }

    public function setComplemento($complemento) {
        $this->complemento = $complemento;
    }

    public function getComplemento() {

        return $this->complemento;
    }

    public function setLogradouro($logradouro) {
        $this->logradouro = $logradouro;
    }

    public function getLogradouro() {

        return $this->logradouro;
    }

    public function setCidade($cidade) {
        $this->cidade = $cidade;
    }

    public function getCidade() {

        return $this->cidade;
    }

    public function setBairro($bairro) {
        $this->bairro = $bairro;
    }

    public function getBairro() {

        return $this->bairro;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    public function getEstado() {

        return $this->estado;
    }

    public function setNumero($numero) {
        $this->numero = $numero;
    }

    public function getNumero() {

        return $this->numero;
    }

    public function setUsuarioId($usuarioId) {
        $this->usuarioId = $usuarioId;
    }

    public function getUsuarioId() {

        return $this->usuarioId;
    }

}