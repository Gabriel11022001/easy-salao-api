<?php

namespace Repositorio;

use Exception;
use Models\Reserva;
use PDO;

class ReservaRepositorio extends Repositorio {

    /**
     * @param PDO $conexaoBancoDados
     */
    public function __construct($conexaoBancoDados)
    {
        parent::__construct($conexaoBancoDados);
    }

    /**
     * cadastrar reserva
     * @param Reserva $reservaCadastrar
     */
    public function cadastrar($reservaCadastrar) {
        $stmt = $this->bancoDados->prepare("INSERT INTO tb_reservas(
            usuario_id,
            usuario_salao_id,
            horario_salao_id,
            valor_total
        )
        VALUES(
            :usuario_id,
            :usuario_salao_id,
            :horario_salao_id,
            :valor_total
        )");

        $stmt->bindValue(":usuario_id", $reservaCadastrar->getUsuarioId());
        $stmt->bindValue(":usuario_salao_id", $reservaCadastrar->getUsuarioSalaoId());
        $stmt->bindValue(":horario_salao_id", $reservaCadastrar->getHorarioSalaoId());
        $stmt->bindValue(":valor_total", $reservaCadastrar->getValorTotal());

        if (!$stmt->execute()) {

            throw new Exception("Erro ao tentar-se registrar a reserva na base de dados.");
        }

        $reservaCadastrar->setReservaId($this->bancoDados->lastInsertId());

        // cadastrar os relacionamentos reserva -> serviços
        foreach ($reservaCadastrar->getServicos() as $servico) {
            $servicoId = $servico->servico_id;
            $precoServico = $servico->preco;

            $stmt = $this->bancoDados->prepare("INSERT INTO tb_reserva_servico(
                servico_id,
                reserva_id,
                preco_servico_momento_reserva
            )
            VALUES(
                :servico_id,
                :reserva_id,
                :preco_servico_momento_reserva
            )");

            $stmt->bindValue(":servico_id", $servicoId);
            $stmt->bindValue(":reserva_id", $reservaCadastrar->getReservaId());
            $stmt->bindValue(":preco_servico_momento_reserva", $precoServico);

            if (!$stmt->execute()) {

                throw new Exception("Erro ao tentar-se cadastrar a reserva.");
            }

        }

    }

}