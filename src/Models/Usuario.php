<?php

namespace Models;

class Usuario {

    private $usuarioId;
    private $nomeCompleto;
    private $email;
    private $senha;
    private $status;
    private $tipoUsuario;
    private $endereco;

    public function __construct()
    {
        $this->usuarioId = 0;
        $this->nomeCompleto = "";
        $this->email = "";
        $this->senha = "";
        $this->status = "ativo";
        $this->tipoUsuario = "";
        $this->endereco = null;
    }

    public function setUsuarioId($usuarioId) {
        $this->usuarioId = $usuarioId;
    }

    public function getUsuarioId() {

        return $this->usuarioId;
    }

    public function setNomeCompleto($nomeCompleto) {
        $this->nomeCompleto = $nomeCompleto;
    }

    public function getNomeCompleto() {

        return $this->nomeCompleto;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getEmail() {

        return $this->email;
    }

    public function setSenha($senha) {
        $this->senha = $senha;
    }

    public function getSenha() {

        return $this->senha;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getStatus() {

        return $this->status;
    }

    public function setTipoUsuario($tipoUsuario) {
        $this->tipoUsuario = $tipoUsuario;
    }

    public function getTipoUsuario() {

        return $this->tipoUsuario;
    }

    public function setEndereco($endereco) {
        $this->endereco = $endereco;
    }

    public function getEndereco() {

        return $this->endereco;
    }

    public function toArray() {

        return [
            "usuario_id" => $this->getUsuarioId(),
            "nome_completo" => $this->getNomeCompleto(),
            "email" => $this->getEmail(),
            "tipo_usuario" => $this->getTipoUsuario(),
            "endereco" => empty($this->getEndereco()) ? null : [
                "endereco_id" => $this->getEndereco()->getEnderecoId(),
                "cep" => $this->getEndereco()->getCep(),
                "complemento" => $this->getEndereco()->getComplemento(),
                "logradouro" => $this->getEndereco()->getLogradouro(),
                "cidade" => $this->getEndereco()->getCidade(),
                "bairro" => $this->getEndereco()->getBairro(),
                "estado" => $this->getEndereco()->getEstado(),
                "numero" => $this->getEndereco()->getNumero()
            ]
        ];
    }

}