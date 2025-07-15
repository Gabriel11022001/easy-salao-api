<?php

namespace Servico;

use Exception;
use Models\Cliente;
use Repositorio\ClienteRepositorio;
use Repositorio\UsuarioRepositorio;
use Utils\Log;
use Utils\Resposta;

class ClienteServico extends ServicoBase {

    private ClienteRepositorio $clienteRepositorio;
    private UsuarioRepositorio $usuarioRepositorio;

    public function __construct()
    {
        parent::__construct();

        $this->clienteRepositorio = new ClienteRepositorio($this->bancoDados);
        $this->usuarioRepositorio = new UsuarioRepositorio($this->bancoDados);
    }

    // validar campos no cadastrado de usuário
    private function validarCamposCadastroCliente($camposValidar = array()) {
        $erros = [];

        return $erros;
    }

    // cadastrar cliente
    public function cadastrarCliente() {

        try {
            $nomeCompleto = getParametro("nome_completo");
            $telefone = getParametro("telefone");
            $email = getParametro("email");
            $clienteEhVip = getParametro("cliente_vip");
            $usuarioSalaoId = getParametro("usuario_salao_id");
            $usuarioId = getParametro("usuario_id");

            $errosCampos = $this->validarCamposCadastroCliente([
                "nome_completo" => $nomeCompleto,
                "telefone" => $telefone,
                "email" => $email,
                "usuario_salao_id" => $usuarioSalaoId,
                "usuario_id" => $usuarioId
            ]);

            if (!empty($errosCampos)) {
                Resposta::response(false, "Erros nos campos.", $errosCampos);
            }

            // validar se existe um usuário(salão) com o id informado
            if (empty($this->usuarioRepositorio->buscaPeloTipoUsuarioEId($usuarioSalaoId, "salão"))) {
                Resposta::response(false, "Não foi encontrado um usuário(salão) cadastrado com o id informado.");
            }

            // validar se existe um usuário(cliente) com o id informado, caso o usuário tenha passado esse parâmetro
            if (!empty($usuarioId) && empty($this->usuarioRepositorio->buscaPeloTipoUsuarioEId($usuarioId, "cliente"))) {
                Resposta::response(false, "Não foi encontrado um perfil de usuário cliente com o id informado.");
            }

            /**
             * validar se o usuário em questão(representante do salão, possui outro cliente com esse mesmo e-mail)
             */
            if (!empty($this->clienteRepositorio->buscarClientePeloEmailEIdUsuarioSalao($email, $usuarioSalaoId))) {
                Resposta::response(false, "Você já possui outro cliente cadastrado com esse mesmo e-mail.");
            }

            $clienteCadastrar = new Cliente();
            $clienteCadastrar->setNomeCompleto($nomeCompleto);
            $clienteCadastrar->setTelefone($telefone);
            $clienteCadastrar->setEmail($email);
            $clienteCadastrar->setClienteEhVip($clienteEhVip);
            $clienteCadastrar->setUsuarioSalaoId($usuarioSalaoId);
            $clienteCadastrar->setUsuarioId($usuarioId);

            $this->clienteRepositorio->cadastrar($clienteCadastrar);

            Resposta::response(true, "Cliente cadastrado com sucesso.", [
                "cliente_id" => $clienteCadastrar->getClienteId(),
                "nome_completo" => $clienteCadastrar->getNomeCompleto(),
                "telefone" => $clienteCadastrar->getTelefone(),
                "email" => $clienteCadastrar->getEmail(),
                "cliente_vip" => $clienteCadastrar->getClienteEhVip(),
                "usuario_id" => $clienteCadastrar->getUsuarioId(),
                "usuario_salao_id" => $clienteCadastrar->getUsuarioSalaoId()
            ]);
        } catch (Exception $e) {
            Log::erro("Erro ao tentar-se cadastrar o cliente: " . $e->getMessage());

            Resposta::response(false, "Erro ao tentar-se cadastrar o cliente.");
        }

    }

    // buscar todos os clientes do usuário(representante do salão)
    public function buscarTodos() {

        try {
            
            if (!isset($_GET["usuario_salao_id"])) {
                Resposta::response(false, "Informe na url o id do usuário representante do salão.");
            }

            $usuarioRepresentanteSalaoId = $_GET["usuario_salao_id"];

            if (empty($usuarioRepresentanteSalaoId)) {
                Resposta::response(false, "Informe na url o id do usuário representante do salão.");
            }

            // validar se existe um representante de salão cadastrado com o id informado
            if (empty($this->usuarioRepositorio->buscaPeloTipoUsuarioEId($usuarioRepresentanteSalaoId, "salão"))) {
                Resposta::response(false, "Não foi encontrado um representante de salão com o id informado.");
            }

            $clientes = $this->clienteRepositorio->buscarClientesRepresentanteSalao($usuarioRepresentanteSalaoId);

            if (empty($clientes)) {
                Resposta::response(true, "Não existem clientes cadastrados na base de dados.", array());
            }

            Resposta::response(true, "Clientes listados com sucesso.", $clientes);
        } catch (Exception $e) {
            Log::erro("Erro ao tentar-se listar todos os usuários: " . $e->getMessage());

            Resposta::response(false, "Erro ao tentar-se listar todos os usuários.");
        }

    }

}