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

    // buscar os horários do salão
    public function buscarHorariosSalao($usuarioSalaoId, $ano, $mes, $dia, $reservado) {
        $query = "SELECT * FROM tb_horarios WHERE usuario_salao_id = :usuario_salao_id ";

        if (!empty($ano)) {
            $query .= "AND ano = :ano ";
        }

        if (!empty($mes)) {
            $query .= "AND mes = :mes ";
        }

        if (!empty($dia)) {
            $query .= "AND dia = :dia ";
        }

        if ($reservado === true) {
            $query .= " AND reservado = true ";
        } else if ($reservado === false) {
            $query .= " AND reservado = false ";
        }

        $query .= " ORDER BY horario_id DESC";

        $stmt = $this->bancoDados->prepare($query);

        $stmt->bindValue(":usuario_salao_id", $usuarioSalaoId);

        if (!empty($ano)) {
            $stmt->bindValue(":ano", $ano);
        }

        if (!empty($mes)) {
            $stmt->bindValue(":mes", $mes);
        }

        if (!empty($dia)) {
            $stmt->bindValue(":dia", $dia);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // buscar horário pelo id
    public function buscarPeloId($horarioId) {
        $stmt = $this->bancoDados->prepare("SELECT * FROM tb_horarios WHERE horario_id = :horario_id");
        $stmt->bindValue(":horario_id", $horarioId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // alterar o status do horário
    public function alterarStatusHorario($horarioId, $reservado) {
        $stmt = $this->bancoDados->prepare("UPDATE tb_horarios SET reservado = :reservado WHERE horario_id = :horario_id");
        $stmt->bindValue(":horario_id", $horarioId);
        $stmt->bindValue(":reservado", $reservado, PDO::PARAM_BOOL);

        if ($stmt->execute()) {

            return;
        }

        throw new Exception("Erro ao tentar-se alterar o status do horário.");
    }

    // buscar reserva_id relacionada ao horário em questão
    public function buscarIdReservaRelacionadaHorario($horarioId) {
        $stmt = $this->bancoDados->prepare("SELECT reserva_id FROM tb_reservas WHERE horario_salao_id = :horario_id");
        $stmt->bindValue(":horario_id", $horarioId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // deletar horário
    public function deletar($horarioId) {
        $stmt = $this->bancoDados->prepare("DELETE FROM tb_horarios WHERE horario_id = :horario_id");
        $stmt->bindValue(":horario_id", $horarioId);

        if (!$stmt->execute()) {

            throw new Exception("Erro ao tentar-se deletar o horário.");
        }

    }

}