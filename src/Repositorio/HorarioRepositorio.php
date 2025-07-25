<?php

namespace Repositorio;

use Exception;
use Models\Horario;
use PDO;

class HorarioRepositorio extends Repositorio {

    public function __construct(PDO $bancoDados)
    {   
        parent::__construct($bancoDados);
    }

    /**
     * cadastrar horário do salão de beleza
     * @param Horario $horarioCadastrar
     */
    public function cadastrar($horarioCadastrar) {
        $stmt = $this->bancoDados->prepare("INSERT INTO tb_horarios(ano, mes, dia, horario_de, horario_ate, usuario_salao_id) 
        VALUES(:ano, :mes, :dia, :horario_de, :horario_ate, :usuario_salao_id)");

        $stmt->bindValue(":ano", $horarioCadastrar->getAno());
        $stmt->bindValue(":mes", $horarioCadastrar->getMes());
        $stmt->bindValue(":dia", $horarioCadastrar->getDia());
        $stmt->bindValue(":horario_de", $horarioCadastrar->getDe());
        $stmt->bindValue(":horario_ate", $horarioCadastrar->getAte());
        $stmt->bindValue(":usuario_salao_id", $horarioCadastrar->getUsuarioSalaoId());

        if (!$stmt->execute()) {

            throw new Exception("Erro ao tentar-se cadastrar o horário na base de dados.");
        }

        $horarioCadastrar->setHorarioId($this->bancoDados->lastInsertId());
    }

    // buscar os horários pelo ano/mes/dia/horário de/horário até
    public function buscarHorariosPeloAnoMesDia($ano, $mes, $dia, $usuarioSalaoId, $horarioDe, $horarioAte) {
        $query = "SELECT * FROM tb_horarios 
        WHERE ano = :ano
        AND mes = :mes
        AND dia = :dia
        AND usuario_salao_id = :usuario_salao_id
        AND horario_de = :horario_de
        AND horario_ate = :horario_ate";

        $stmt = $this->bancoDados->prepare($query);
        $stmt->bindValue(":ano", $ano);
        $stmt->bindValue(":mes", $mes);
        $stmt->bindValue(":dia", $dia);
        $stmt->bindValue(":usuario_salao_id", $usuarioSalaoId);
        $stmt->bindValue(":horario_de", $horarioDe);
        $stmt->bindValue(":horario_ate", $horarioAte);

        $stmt->execute();
        $horarioArray = $stmt->fetch(PDO::FETCH_ASSOC);

        return $horarioArray;
    }

}