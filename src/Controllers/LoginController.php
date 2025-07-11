<?php

namespace Controllers;

use Servico\LoginServico;

class LoginController {

    private LoginServico $loginServico;

    public function __construct()
    {
        $this->loginServico = new LoginServico();
    }

    // efetuar login
    public function login() {
        $this->loginServico->login();
    }

}