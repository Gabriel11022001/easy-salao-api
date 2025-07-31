<?php

namespace Servico;

use Exception;
use Models\Horario;
use Repositorio\HorarioRepositorio;
use Repositorio\UsuarioRepositorio;
use Utils\Log;
use Utils\Metodos;
use Utils\Resposta;

class HorarioServico extends ServicoBase {

    /**
     * @property HorarioRepositorio
     */
    private $horarioRepositorio;
    /**
     * @property UsuarioRepositorio
     */
    private $usuarioRepositorio;

    public function __construct()
    {
        parent::__construct();

        $this->horarioRepositorio = new HorarioRepositorio($this->bancoDados);
        $this->usuarioRepositorio = new UsuarioRepositorio($this->bancoDados);
    }

    // cadastrar horário do salão
    public function cadastrar() {

        try {
            $ano = getParametro("ano");
            $mes = getParametro("mes");
            $dia = getParametro("dia");
            $de = getParametro("horario_de");
            $ate = getParametro("horario_ate");
            $usuarioSalaoId = getParametro("usuario_salao_id");

            $errosCampos = Metodos::validarCamposCadastroHorario([
                "ano" => $ano,
                "mes" => $mes,
                "dia" => $dia,
                "horario_de" => $de,
                "horario_ate" => $ate,
                "usuario_salao_id" => $usuarioSalaoId
            ]);

            if (!empty($errosCampos)) {
                Resposta::response(false, "Erros nos campos.", $errosCampos);
            }
            
            // validar se já existe outro horário cadastrado no mesmo ano/mes/dia
            if (!empty($this->horarioRepositorio->buscarHorariosPeloAnoMesDia(
                $ano,
                $mes,
                $dia,
                $usuarioSalaoId,
                $de,
                $ate
            ))) {
                Resposta::response(false, "Já existe outro horário cadastrado para essa mesma data.");
            }

            // validar se existe um usuário cadastrado com o id informado
            if (empty($this->usuarioRepositorio->buscaPeloTipoUsuarioEId($usuarioSalaoId, "salão"))) {
                Resposta::response(false, "Não existe um usuário cadastrado com o id informado.");
            }
            
            $horario = new Horario();
            $horario->setAno($ano);
            $horario->setMes($mes);
            $horario->setDia($dia);
            $horario->setDe($de);
            $horario->setAte($ate);
            $horario->setUsuarioSalaoId($usuarioSalaoId);

            $this->horarioRepositorio->cadastrar(horarioCadastrar: $horario);
            
            Resposta::response(
                true, 
                "Horário registrado com sucesso.",
                $horario->toArray()
            );
        } catch (Exception $e) {
            Log::erro("Erro ao tentar-se cadastrar o horário: " . $e->getMessage());

            Resposta::response(false, "Erro ao tentar-se cadastrar o horário.");
        }

    }

    // espelhar horários para os próximos meses do ano
    public function espelharHorarios() {
        $this->bancoDados->beginTransaction();

        try {
            $ano = getParametro("ano");
            $mes = getParametro("mes");
            $dia = getParametro("dia");
            $horarios = getParametro("horarios");
            $usuarioSalaoId = getParametro("usuario_salao_id");
            $idBissexto = Metodos::validarAnoBissexto($ano);

            // validar os campos
            $errosCampos = Metodos::validarCamposEspelharHorario([
                "ano" => $ano,
                "mes" => $mes,
                "dia" => $dia,
                "usuario_salao_id" => $usuarioSalaoId,
                "horarios" => $horarios
            ]);

            if (!empty($errosCampos))
                Resposta::response(false, "Erros nos campos", $errosCampos);

            // validar se existe um usuário cadastrado com id informado
            if (empty($this->usuarioRepositorio->buscaPeloTipoUsuarioEId($usuarioSalaoId, "salão"))) {
                Resposta::response(false, "Não existe um usuário cadastrado com o id informado.");
            }

            // mês de Janeiro
            if ($mes === "Janeiro") {

                // espelhar o horário para os demais dias do mês de Janeiro
                if ($dia < 31) {

                    for ($i = $dia + 1; $i <= 31; $i++) {

                        foreach ($horarios as $horarioEspelhar) {
                            $horario = new Horario();
                            $horario->setMes("Janeiro");
                            $horario->setAno($ano);
                            $horario->setDia($i);
                            $horario->setDe($horarioEspelhar->horario_de);
                            $horario->setAte($horarioEspelhar->horario_ate);
                            $horario->setUsuarioSalaoId($usuarioSalaoId);

                            // validar para não ocorrer duplicação
                            if (empty($this->horarioRepositorio->buscarHorariosPeloAnoMesDia(
                                $ano,
                                "Janeiro",
                                $i,
                                $usuarioSalaoId,
                                $horarioEspelhar->horario_de,
                                $horarioEspelhar->horario_ate
                            ))) {
                                $this->horarioRepositorio->cadastrar($horario);
                            }

                        }

                    }

                }

                // espelhar para Fevereiro
                for ($i = 1; $i <= ($idBissexto ? 29 : 28); $i++) {

                    foreach ($horarios as $horarioEspelhar) {
                        $horario = new Horario();
                        $horario->setAno($ano);
                        $horario->setMes("Fevereiro");
                        $horario->setDia($i);
                        $horario->setDe($horarioEspelhar->horario_de);
                        $horario->setAte($horarioEspelhar->horario_ate);
                        $horario->setUsuarioSalaoId($usuarioSalaoId);

                        // validar para não ocorrer duplicação
                        if (empty($this->horarioRepositorio->buscarHorariosPeloAnoMesDia(
                            $ano,
                            "Fevereiro",
                            $i,
                            $usuarioSalaoId,
                            $horarioEspelhar->horario_de,
                            $horarioEspelhar->horario_ate
                        ))) {
                            $this->horarioRepositorio->cadastrar($horario);
                        }

                    }

                }

                // espelhar para Março
                for ($i = 1; $i <= 31; $i++) {

                    foreach ($horarios as $horarioEspelhar) {
                        $horario = new Horario();
                        $horario->setAno($ano);
                        $horario->setMes("Março");
                        $horario->setDia($i);
                        $horario->setDe($horarioEspelhar->horario_de);
                        $horario->setAte($horarioEspelhar->horario_ate);
                        $horario->setUsuarioSalaoId($usuarioSalaoId);

                        // validar para não ocorrer duplicação
                        if (empty($this->horarioRepositorio->buscarHorariosPeloAnoMesDia(
                            $ano,
                            "Março",
                            $i,
                            $usuarioSalaoId,
                            $horarioEspelhar->horario_de,
                            $horarioEspelhar->horario_ate
                        ))) {
                            $this->horarioRepositorio->cadastrar($horario);
                        }

                    }

                }

                // espelhar para Abril
                for ($i = 1; $i <= 30; $i++) {

                    foreach ($horarios as $horarioEspelhar) {
                        $horario = new Horario();
                        $horario->setAno($ano);
                        $horario->setMes("Abril");
                        $horario->setDia($i);
                        $horario->setDe($horarioEspelhar->horario_de);
                        $horario->setAte($horarioEspelhar->horario_ate);
                        $horario->setUsuarioSalaoId($usuarioSalaoId);

                        // validar para não ocorrer duplicação
                        if (empty($this->horarioRepositorio->buscarHorariosPeloAnoMesDia(
                            $ano,
                            "Abril",
                            $i,
                            $usuarioSalaoId,
                            $horarioEspelhar->horario_de,
                            $horarioEspelhar->horario_ate
                        ))) {
                            $this->horarioRepositorio->cadastrar($horario);
                        }

                    }

                }

                // espelhar para Maio
                for ($i = 1; $i <= 31; $i++) {

                    foreach ($horarios as $horarioEspelhar) {
                        $horario = new Horario();
                        $horario->setAno($ano);
                        $horario->setMes("Maio");
                        $horario->setDia($i);
                        $horario->setDe($horarioEspelhar->horario_de);
                        $horario->setAte($horarioEspelhar->horario_ate);
                        $horario->setUsuarioSalaoId($usuarioSalaoId);

                        // validar para não ocorrer duplicação
                        if (empty($this->horarioRepositorio->buscarHorariosPeloAnoMesDia(
                            $ano,
                            "Maio",
                            $i,
                            $usuarioSalaoId,
                            $horarioEspelhar->horario_de,
                            $horarioEspelhar->horario_ate
                        ))) {
                            $this->horarioRepositorio->cadastrar($horario);
                        }

                    }

                }

                // espelhar para Junho
                for ($i = 1; $i <= 30; $i++) {

                    foreach ($horarios as $horarioEspelhar) {
                        $horario = new Horario();
                        $horario->setAno($ano);
                        $horario->setMes("Junho");
                        $horario->setDia($i);
                        $horario->setDe($horarioEspelhar->horario_de);
                        $horario->setAte($horarioEspelhar->horario_ate);
                        $horario->setUsuarioSalaoId($usuarioSalaoId);

                        // validar para não ocorrer duplicação
                        if (empty($this->horarioRepositorio->buscarHorariosPeloAnoMesDia(
                            $ano,
                            "Junho",
                            $i,
                            $usuarioSalaoId,
                            $horarioEspelhar->horario_de,
                            $horarioEspelhar->horario_ate
                        ))) {
                            $this->horarioRepositorio->cadastrar($horario);
                        }

                    }

                }

                // espelhar para Julho
                for ($i = 1; $i <= 31; $i++) {

                    foreach ($horarios as $horarioEspelhar) {
                        $horario = new Horario();
                        $horario->setAno($ano);
                        $horario->setMes("Julho");
                        $horario->setDia($i);
                        $horario->setDe($horarioEspelhar->horario_de);
                        $horario->setAte($horarioEspelhar->horario_ate);
                        $horario->setUsuarioSalaoId($usuarioSalaoId);

                        // validar para não ocorrer duplicação
                        if (empty($this->horarioRepositorio->buscarHorariosPeloAnoMesDia(
                            $ano,
                            "Julho",
                            $i,
                            $usuarioSalaoId,
                            $horarioEspelhar->horario_de,
                            $horarioEspelhar->horario_ate
                        ))) {
                            $this->horarioRepositorio->cadastrar($horario);
                        }

                    }

                }

                // espelhar para Agosto
                for ($i = 1; $i <= 31; $i++) {

                    foreach ($horarios as $horarioEspelhar) {
                        $horario = new Horario();
                        $horario->setAno($ano);
                        $horario->setMes("Agosto");
                        $horario->setDia($i);
                        $horario->setDe($horarioEspelhar->horario_de);
                        $horario->setAte($horarioEspelhar->horario_ate);
                        $horario->setUsuarioSalaoId($usuarioSalaoId);

                        // validar para não ocorrer duplicação
                        if (empty($this->horarioRepositorio->buscarHorariosPeloAnoMesDia(
                            $ano,
                            "Agosto",
                            $i,
                            $usuarioSalaoId,
                            $horarioEspelhar->horario_de,
                            $horarioEspelhar->horario_ate
                        ))) {
                            $this->horarioRepositorio->cadastrar($horario);
                        }

                    }

                }

                // espelhar para Setembro
                for ($i = 1; $i <= 30; $i++) {

                    foreach ($horarios as $horarioEspelhar) {
                        $horario = new Horario();
                        $horario->setAno($ano);
                        $horario->setMes("Setembro");
                        $horario->setDia($i);
                        $horario->setDe($horarioEspelhar->horario_de);
                        $horario->setAte($horarioEspelhar->horario_ate);
                        $horario->setUsuarioSalaoId($usuarioSalaoId);

                        // validar para não ocorrer duplicação
                        if (empty($this->horarioRepositorio->buscarHorariosPeloAnoMesDia(
                            $ano,
                            "Setembro",
                            $i,
                            $usuarioSalaoId,
                            $horarioEspelhar->horario_de,
                            $horarioEspelhar->horario_ate
                        ))) {
                            $this->horarioRepositorio->cadastrar($horario);
                        }

                    }

                }

                // espelhar para Outubro
                for ($i = 1; $i <= 31; $i++) {

                    foreach ($horarios as $horarioEspelhar) {
                        $horario = new Horario();
                        $horario->setAno($ano);
                        $horario->setMes("Outubro");
                        $horario->setDia($i);
                        $horario->setDe($horarioEspelhar->horario_de);
                        $horario->setAte($horarioEspelhar->horario_ate);
                        $horario->setUsuarioSalaoId($usuarioSalaoId);

                        // validar para não ocorrer duplicação
                        if (empty($this->horarioRepositorio->buscarHorariosPeloAnoMesDia(
                            $ano,
                            "Outubro",
                            $i,
                            $usuarioSalaoId,
                            $horarioEspelhar->horario_de,
                            $horarioEspelhar->horario_ate
                        ))) {
                            $this->horarioRepositorio->cadastrar($horario);
                        }

                    }

                }

                // espelhar para Novembro
                for ($i = 1; $i <= 30; $i++) {

                    foreach ($horarios as $horarioEspelhar) {
                        $horario = new Horario();
                        $horario->setAno($ano);
                        $horario->setMes("Novembro");
                        $horario->setDia($i);
                        $horario->setDe($horarioEspelhar->horario_de);
                        $horario->setAte($horarioEspelhar->horario_ate);
                        $horario->setUsuarioSalaoId($usuarioSalaoId);

                        // validar para não ocorrer duplicação
                        if (empty($this->horarioRepositorio->buscarHorariosPeloAnoMesDia(
                            $ano,
                            "Novembro",
                            $i,
                            $usuarioSalaoId,
                            $horarioEspelhar->horario_de,
                            $horarioEspelhar->horario_ate
                        ))) {
                            $this->horarioRepositorio->cadastrar($horario);
                        }

                    }

                }

                // espelhar para Dezembro
                for ($i = 1; $i <= 31; $i++) {

                    foreach ($horarios as $horarioEspelhar) {
                        $horario = new Horario();
                        $horario->setAno($ano);
                        $horario->setMes("Dezembro");
                        $horario->setDia($i);
                        $horario->setDe($horarioEspelhar->horario_de);
                        $horario->setAte($horarioEspelhar->horario_ate);
                        $horario->setUsuarioSalaoId($usuarioSalaoId);

                        // validar para não ocorrer duplicação
                        if (empty($this->horarioRepositorio->buscarHorariosPeloAnoMesDia(
                            $ano,
                            "Dezembro",
                            $i,
                            $usuarioSalaoId,
                            $horarioEspelhar->horario_de,
                            $horarioEspelhar->horario_ate
                        ))) {
                            $this->horarioRepositorio->cadastrar($horario);
                        }

                    }

                }

            }

            $this->bancoDados->commit();

            Resposta::response(true, "Horários espelhados com sucesso.");
        } catch (Exception $e) {
            $this->bancoDados->rollBack();
            Log::erro("Erro ao tentar-se espelhar os horários: " . $e->getMessage());

            Resposta::response(false, "Erro ao tentar-se espelhar os horários.");
        }

    }

    // buscar horários do salão
    public function buscarHorariosSalao() {

        try {   
            $usuarioSalaoId = getParametro("usuario_salao_id");
            $ano            = getParametro("ano");
            $mes            = getParametro("mes");
            $dia            = getParametro("dia");
            $reservado      = getParametro("reservado");

            // validar se existe um usuário de salão cadastrado com o id informado
            if (empty($this->usuarioRepositorio->buscaPeloTipoUsuarioEId($usuarioSalaoId, "salão"))) {
                Resposta::response(false, "Não existe um usuário de salão cadastrado com o id informado.");
            }

            $horarios = $this->horarioRepositorio->buscarHorariosSalao(
                usuarioSalaoId: $usuarioSalaoId,
                ano: $ano,
                mes: $mes,
                dia: $dia,
                reservado: $reservado
            );

            if (empty($horarios)) {
                Resposta::response(true, "Não existem horários cadastrados na base de dados.", array());
            }

            Resposta::response(true, "Horários encontrados com sucesso.", $horarios);
        } catch (Exception $e) {
            Log::erro("Erro ao tentar-se listar os horários do salão: " . $e->getMessage());

            Resposta::response(false, "Erro ao tentar-se listar os horários do salão.");
        }

    }

    // deletar horário do salão
    public function deletar() {

        try {
            
            if (!isset($_GET["horario_id"])) {
                Resposta::response(false, "Informe o id do horário na url.");
            }

            $horarioId = $_GET["horario_id"];

            if (empty($horarioId)) {
                Resposta::response(false, "Informe o id do horário na url.");
            }

            // validar se existe um horário cadastrado com o id informado
            if (empty($this->horarioRepositorio->buscarPeloId($horarioId))) {
                Resposta::response(false, "Horário não encontrado na base de dados.");
            }

            /**
             * validar se existe uma reserva relacionada
             * ao horário em questão, se tiver, não deletar o horário
             */
            if (!empty($this->horarioRepositorio->buscarIdReservaRelacionadaHorario($horarioId))) {
                Resposta::response(false, "Existe uma reserva relacionada ao horário em questão, primeiro, cancele a reserva para poder deletar esse horário.");
            }

            $this->horarioRepositorio->deletar($horarioId);

            Resposta::response(true, "Horário deletado com sucesso.");
        } catch (Exception $e) {
            Log::erro("Erro ao tentar-se deletar o horário: " . $e->getMessage());

            Resposta::response(false, "Erro ao tentar-se deletar o horário.");
        }

    }

}
