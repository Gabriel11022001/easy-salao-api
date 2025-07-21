<?php

namespace Models;

class ServicoSalao {

    private $servicoSalaoId;
    private $nomeServico;
    private $usuarioSalaoId;
    private $precoServico;
    private $salaoForneceEsseServico;

    public function __construct()
    {
        $this->servicoSalaoId = 0;
        $this->nomeServico = "";
        $this->usuarioSalaoId = 0;
        $this->precoServico = 0;
        $this->salaoForneceEsseServico = false;
    }

    public function setServicoSalaoId($servicoSalaoId) {
        $this->servicoSalaoId = $servicoSalaoId;
    }

    public function getServicoSalaoId() {

        return $this->servicoSalaoId;
    }

    public function setNomeServico($nomeServico) {
        $this->nomeServico = $nomeServico;
    }

    public function getNomeServico() {

        return $this->nomeServico;
    }

    public function setUsuarioSalaoId($usuarioSalaoId) {
        $this->usuarioSalaoId = $usuarioSalaoId;
    }

    public function getUsuarioSalaoId() {

        return $this->usuarioSalaoId;
    }

    public function setPrecoServico($precoServico) {
        $this->precoServico = $precoServico;
    }

    public function getPrecoServico() {

        return $this->precoServico;
    }

    public function setSalaoForneceEsseServico($salaoForneceEsseServico) {
        $this->salaoForneceEsseServico = $salaoForneceEsseServico;
    }

    public function getSalaoForneceEsseServico() {

        return $this->salaoForneceEsseServico;
    }

}
