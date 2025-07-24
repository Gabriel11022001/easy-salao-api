<?php

namespace Servico;

use Exception;
use Models\ServicoSalao;
use Repositorio\ServicoSalaoRepositorio;
use Utils\Log;
use Utils\Resposta;

class ServicoSalaoServico extends ServicoBase {

    private ServicoSalaoRepositorio $servicoSalaoRepositorio;

    public function __construct()
    {
        parent::__construct();

        $this->servicoSalaoRepositorio = new ServicoSalaoRepositorio($this->bancoDados);
    }

    // cadastrar serviço do salão
    public function cadastrar() {

        try {
            $nome = getParametro("nome");
            $usuarioSalaoId = getParametro("usuario_salao_id");
            $precoServico = getParametro("preco");
            $salaoForneceEsseServico = getParametro("salao_fornece_esse_servico");

            // validar os campos
            if (empty($nome)) {
                Resposta::response(false, "Informe o nome do serviço");
            }

            if (empty($usuarioSalaoId)) {
                Resposta::response(false, "Informe o id do usuário do salão.");
            }

            if (empty($precoServico)) {
                Resposta::response(false, "Informe o preço do serviço.");
            }

            if ($precoServico <= 0) {
                Resposta::response(false, "Preço inválido.");
            }

            // validar se já existe outro serviço cadastrado para o salão, com o mesmo nome
            if (!empty($this->servicoSalaoRepositorio->buscarServicoSalaoPeloNomeIdUsuario($usuarioSalaoId, $nome))) {
                Resposta::response(false, "Você já possui um serviço cadastrado com o nome informado.");
            }

            $servico = new ServicoSalao();

            $servico->setNomeServico($nome);
            $servico->setPrecoServico($precoServico);
            $servico->setSalaoForneceEsseServico($salaoForneceEsseServico);
            $servico->setUsuarioSalaoId($usuarioSalaoId);

            $this->servicoSalaoRepositorio->salvar($servico);

            Resposta::response(true, "Serviço cadastrado com sucesso.", [
                "servico_salao_id" => $servico->getServicoSalaoId(),
                "nome_servico" => $servico->getNomeServico(),
                "preco_servico" => $servico->getPrecoServico(),
                "usuario_salao_id" => $servico->getUsuarioSalaoId(),
                "salao_fornece_esse_servico" => $servico->getSalaoForneceEsseServico()
            ]);
        } catch (Exception $e) {
            Log::erro("Erro ao tentar-se cadastrar o serviço do salão: " . $e->getMessage());

            Resposta::response(false, "Erro ao tentar-se cadastrar o serviço do salão.");
        }

    }

    // buscar os serviços fornecidos pelo salão
    public function buscarServicosSalao() {

        try {
        
            if (!isset($_GET["id_usuario_salao"])) {
                Resposta::response(false, "Informe o id do usuário do salão na url.");
            }

            $usuarioSalaoId = $_GET["id_usuario_salao"];

            if (empty($usuarioSalaoId)) {
                Resposta::response(false, "Informe o id do usuário do salão na url.");
            }

            $servicos = $this->servicoSalaoRepositorio->buscarServicosSalao($usuarioSalaoId);

            if (empty($servicos)) {
                Resposta::response(true, "Não existem serviços cadastrados na base de dados.", []);
            }

            Resposta::response(true, "Serviços listados com sucesso.", $servicos);
        } catch (Exception $e) {
            Log::erro("Erro ao tentar-se buscar os serviços fornecidos pelo salão: " . $e->getMessage());

            Resposta::response(false, "Erro ao tentar-se buscar os serviços fornecidos pelo salão.");
        }

    }

    // buscar o serviço fornecido pelo salão pelo id
    public function buscarPeloId() {

        try {

            if (!isset($_GET["servico_id"])) {
                Resposta::response(false, "Informe o id do serviço na url.");
            }

            $id = $_GET["servico_id"];

            if (empty($id)) {
                Resposta::response(false, "Informe o id do serviço na url.");
            }

            $servico = $this->servicoSalaoRepositorio->buscarPeloId($id);

            if (empty($servico)) {
                Resposta::response(false, "Servicço não encontrado.");
            }

            Resposta::response(true, "Serviço encontrado com sucesso.", $servico->toArray());
        } catch (Exception $e) {
            Log::erro("Erro ao tentar-se buscar o serviço fornecido pelo salão pelo id: " . $e->getMessage());

            Resposta::response(false, "Erro ao tentar-se buscar o serviço pelo id.");
        }

    }

}