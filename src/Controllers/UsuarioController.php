<?php

namespace Controllers;

use Servico\UsuarioServico;

class UsuarioController {

    private UsuarioServico $usuarioServico;

    public function __construct()
    {
        $this->usuarioServico = new UsuarioServico();
    }

    // cadastrar usuário
    public function cadastrar() {
        $this->usuarioServico->cadastrar();
    }

}