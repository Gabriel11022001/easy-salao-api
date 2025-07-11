<?php

namespace Servico;

use Exception;
use Repositorio\UsuarioRepositorio;
use Utils\Funcoes;
use Utils\Resposta;

class LoginServico extends ServicoBase {

    private UsuarioRepositorio $usuarioRepositorio;

    public function __construct()
    {
        parent::__construct();     

        $this->usuarioRepositorio = new UsuarioRepositorio($this->bancoDados);
    }

    // realizar login
    public function login() {

        try {
            $email = getParametro("email");
            $senha = getParametro("senha");

            if (empty($email)) {
                Resposta::response(false, "Informe o e-mail.");
            }

            if (!Funcoes::validarEmail($email)) {
                Resposta::response(false, "E-mail inválido.");
            }

            if (empty($senha)) {
                Resposta::response(false, "Informe a senha.");
            }

            $usuario = $this->usuarioRepositorio->buscarPeloEmailSenha($email, md5($senha));

            if (empty($usuario)) {
                Resposta::response(false, "E-mail ou senha inválidos.");
            }

            Resposta::response(true, "Login efetuado com sucesso.", $usuario->toArray());
        } catch (Exception $e) {
            Resposta::response(false, "Erro ao tentar-se realizar login.");
        }

    }

}