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

}