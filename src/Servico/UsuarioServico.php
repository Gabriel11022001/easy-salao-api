<?php

namespace Servico;

use Exception;
use Models\Endereco;
use Models\Usuario;
use Repositorio\UsuarioRepositorio;
use Utils\Resposta;

class UsuarioServico extends ServicoBase {

    private UsuarioRepositorio $usuarioRepositorio;

    public function __construct()
    {
        parent::__construct();

        $this->usuarioRepositorio = new UsuarioRepositorio($this->bancoDados);
    }

    // validar dados do usuário durante o cadastro
    private function validarCamposCadastroUsuario($dadosUsuario) {
        $erros = array();

        if (empty($dadosUsuario["nome_completo"])) {
            $erros["nome_completo"] = "Informe o nome completo do usuário.";
        }

        if (empty($dadosUsuario["email"])) {
            $erros["email"] = "Informe o e-mail do usuário.";
        }

        if (empty($dadosUsuario["senha"])) {
            $erros["senha"] = "Informe a senha do usuário.";
        } else if (strlen($dadosUsuario["senha"]) < 6) {
            $erros["senha"] = "A senha deve possuir no mínimo 6 caracteres.";
        }

        if (empty($dadosUsuario["senha_validacao"])) {
            $erros["senha_validacao"] = "Informe a senha de confirmação.";
        } else if (strlen($dadosUsuario["senha_validacao"]) < 6) {
            $erros["senha_validacao"] = "A senha de confirmação deve possuir no mínimo 6 caracteres.";
        }

        if (!empty($dadosUsuario["senha"]) && !empty($dadosUsuario["senha_validacao"])
        && $dadosUsuario["senha"] !== $dadosUsuario["senha_validacao"]) {
            $erros["senha"] = "As senhas não coicidem.";
        }

        if (empty($dadosUsuario["tipo_usuario"])) {
            $erros["tipo_usuario"] = "Informe o tipo de usuário.";
        } else if ($dadosUsuario["tipo_usuario"] !== "salão" && $dadosUsuario["tipo_usuario"] !== "cliente") {
            $erros["tipo_usuario"] = "Tipo de usuário inválido.";
        }

        if ($dadosUsuario["tipo_usuario"] === "salão") {

            if (empty($dadosUsuario["cep"])) {
                $erros["cep"] = "Informe o cep.";
            }

            if (empty($dadosUsuario["logradouro"])) {
                $erros["cep"] = "Informe o logradouro.";
            }

            if (empty($dadosUsuario["cidade"])) {
                $erros["cidade"] = "Informe a cidade.";
            }

            if (empty($dadosUsuario["bairro"])) {
                $erros["bairro"] = "Informe o bairro.";
            }

            if (empty($dadosUsuario["estado"])) {
                $erros["estado"] = "Informe o estado.";
            }

            if (!empty($dadosUsuario["numero"])) {

                if (is_int($dadosUsuario["numero"])) {

                    if ($dadosUsuario["numero"] <= 0) {
                        $erros["numero"] = "Número inválido.";
                    }

                } else if ($dadosUsuario["numero"] != "s/n") {
                    $erros["numero"] = "Número inválido.";
                }

            }

        }

        return $erros;
    }

    // cadastrar usuário
    public function cadastrar() {

        try {
            $nomeCompleto = getParametro("nome_completo");
            $email = getParametro("email");
            $senha = getParametro("senha");
            $senhaValidacao = getParametro("senha_validacao");
            $tipoUsuario = getParametro("tipo_usuario");
            $cep = getParametro("cep");
            $complemento = getParametro("complemento");
            $logradouro = getParametro("logradouro");
            $cidade = getParametro("cidade");
            $bairro = getParametro("bairro");
            $estado = getParametro("estado");
            $numero = getParametro("numero");

            $errosCampos = $this->validarCamposCadastroUsuario([
                "nome_completo" => $nomeCompleto,
                "email" => $email,
                "senha" => $senha,
                "senha_validacao" => $senhaValidacao,
                "tipo_usuario" => $tipoUsuario,
                "cep" => $cep,
                "logradouro" => $logradouro,
                "cidade" => $cidade,
                "bairro" => $bairro,
                "estado" => $estado,
                "numero" => $numero
            ]);

            if (!empty($errosCampos)) {
                Resposta::response(false, "Erros nos campos.", $errosCampos);
            }

            // validar se já existe outro usuário cadastrado com o mesmo e-mail na base de dados
            if (!empty($this->usuarioRepositorio->buscarPeloEmail($email))) {
                Resposta::response(false, "Informe outro e-mail.");
            }

            $usuario = new Usuario();
            $usuario->setNomeCompleto($nomeCompleto);
            $usuario->setEmail($email);
            $usuario->setSenha(md5($senha));
            $usuario->setStatus("ativo");
            $usuario->setTipoUsuario($tipoUsuario);

            $this->usuarioRepositorio->cadastrar($usuario);

            if ($usuario->getTipoUsuario() === "salão") {
                $endereco = new Endereco();

                $endereco->setCep($cep);
                $endereco->setComplemento($complemento);
                $endereco->setLogradouro($logradouro);
                $endereco->setCidade($cidade);
                $endereco->setBairro($bairro);
                $endereco->setEstado($estado);
                $endereco->setNumero($numero);
                $endereco->setUsuarioId($usuario->getUsuarioId());

                $usuario->setEndereco($endereco);

                // salvar o endereço do usuário
                if ($usuario->getTipoUsuario() === "salão") {
                    $this->usuarioRepositorio->salvarEndereco($endereco);
                }

            }

            Resposta::response(true, "Usuário cadastrado com sucesso no banco de dados.", [
                "usuario_id" => intval($usuario->getUsuarioId()),
                "nome_completo" => $usuario->getNomeCompleto(),
                "email" => $usuario->getEmail(),
                "tipo_usuario" => $usuario->getTipoUsuario(),
                "endereco" => empty($usuario->getEndereco()) ? null : [
                    "endereco_id" => $usuario->getEndereco()->getEnderecoId(),
                    "cep" => $usuario->getEndereco()->getCep(),
                    "complemento" => $usuario->getEndereco()->getComplemento(),
                    "logradouro" => $usuario->getEndereco()->getLogradouro(),
                    "cidade" => $usuario->getEndereco()->getCidade(),
                    "bairro" => $usuario->getEndereco()->getBairro(),
                    "numero" => $usuario->getEndereco()->getNumero(),
                    "estado" => $usuario->getEndereco()->getEstado()
                ]
            ]);
        } catch (Exception $e) {
            Resposta::response(false, "Erro ao tentar-se cadastrar o usuário.");
        }

    }

}