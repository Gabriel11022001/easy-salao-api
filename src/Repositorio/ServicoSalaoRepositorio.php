<?php

namespace Repositorio;

use Exception;
use Models\ServicoSalao;
use PDO;

class ServicoSalaoRepositorio extends Repositorio {

    public function __construct(PDO $bancoDados)
    {
        parent::__construct($bancoDados);
    }

    // salvar o serviço do salão
    public function salvar($servicoSalaoSalvar) {
        $query = "";

        if (!empty($servicoSalaoSalvar->getServicoSalaoId())) {
            // editar
            $query = "UPDATE tb_servicos_salao SET nome_servico = :nome_servico, usuario_salao_id = :usuario_salao_id,
            preco_servico = :preco_servico, salao_fornece_servico = :salao_fornece_servico
            WHERE servico_salao_id = :servico_salao_id";
        } else {
            // cadastrar
            $query = "INSERT INTO tb_servicos_salao(nome_servico, usuario_salao_id, preco_servico, salao_fornece_servico) 
            VALUES(:nome_servico, :usuario_salao_id, :preco_servico, :salao_fornece_servico)";
        }

        $stmt = $this->bancoDados->prepare($query);
        $stmt->bindValue(":nome_servico", $servicoSalaoSalvar->getNomeServico());
        $stmt->bindValue(":usuario_salao_id", $servicoSalaoSalvar->getUsuarioSalaoId());
        $stmt->bindValue(":preco_servico", $servicoSalaoSalvar->getPrecoServico());
        $stmt->bindValue(":salao_fornece_servico", $servicoSalaoSalvar->getSalaoForneceEsseServico());

        if (!empty($servicoSalaoSalvar->getServicoSalaoId())) {
            $stmt->bindValue(":servico_salao_id", $servicoSalaoSalvar->getServicoSalaoId());
        }

        if ($stmt->execute()) {
            
            if (empty($servicoSalaoSalvar->getServicoSalaoId())) {
                $servicoSalaoSalvar->setServicoSalaoId($this->bancoDados->lastInsertId());
            }

            return;
        }

        throw new Exception("Erro ao tentar-se salvar o serviço do salão.");

    }

    // buscar os serviços fornecidos pelo salão
    public function buscarServicosSalao($idUsuarioSalao) {

    }

    // buscar o serviço pelo nome e o id_usuario_salao
    public function buscarServicoSalaoPeloNomeIdUsuario($idUsuarioSalao, $nome) {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_servicos_salao WHERE nome_servico = :nome_servico
        AND usuario_salao_id = :usuario_salao_id");

        $stmt->bindValue(":usuario_salao_id", $idUsuarioSalao);
        $stmt->bindValue(":nome_servico", $nome);
        $stmt->execute();

        $servicoArray = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($servicoArray)) {

            return null;
        }

        $servico = new ServicoSalao();
        $servico->setServicoSalaoId($servicoArray["servico_salao_id"]);
        $servico->setNomeServico($servicoArray["nome_servico"]);
        $servico->setPrecoServico($servicoArray["preco_servico"]);
        $servico->setSalaoForneceEsseServico($servicoArray["salao_fornece_servico"]);
        $servico->setUsuarioSalaoId($servicoArray["usuario_salao_id"]);

        return $servico;
    }

}