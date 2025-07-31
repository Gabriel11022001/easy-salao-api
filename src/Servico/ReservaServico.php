<?php

namespace Servico;

use Exception;
use Models\FiltroReservas;
use Models\Reserva;
use Repositorio\HorarioRepositorio;
use Repositorio\ReservaRepositorio;
use Repositorio\ServicoSalaoRepositorio;
use Repositorio\UsuarioRepositorio;
use stdClass;
use Utils\Log;
use Utils\Resposta;

class ReservaServico extends ServicoBase {

    /**
     * @property ReservaRepositorio
     */
    private $reservaRepositorio;
    /**
     * @property ServicoSalaoRepositorio
     */
    private $servicoRepositorio;
    /**
     * @property UsuarioRepositorio
     */
    private $usuarioRepositorio;
    /**
     * @property HorarioRepositorio
     */
    private $horarioRepositorio;

    public function __construct() {
        parent::__construct();

        $this->reservaRepositorio = new ReservaRepositorio($this->bancoDados);
        $this->servicoRepositorio = new ServicoSalaoRepositorio($this->bancoDados);
        $this->usuarioRepositorio = new UsuarioRepositorio($this->bancoDados);
        $this->horarioRepositorio = new HorarioRepositorio($this->bancoDados);
    }

    // realizar reserva
    public function realizarReserva() {
        $this->bancoDados->beginTransaction();

        try {
            $usuarioId = getParametro("usuario_id");
            $usuarioSalaoId = getParametro("usuario_salao_id");
            $horarioId = getParametro("horario_id");
            $servicos = getParametro("servicos");

            if (empty($usuarioId)) {
                Resposta::response(false, "Informe o id do usuário/cliente.");
            }

            if (empty($usuarioSalaoId)) {
                Resposta::response(false, "Informe o id do usuário/salão.");
            }

            if (empty($horarioId)) {
                Resposta::response(false, "Informe o id do horário que você deseja reservar.");
            }

            if (empty($servicos)) {
                Resposta::response(false, "Informe os serviços que você deseja.");
            }

            $servicos = $this->buscarServicosRealizarReserva($servicos);

            $servicos = array_map(function ($servico) {
                $servicoObj = new stdClass();

                $servicoObj->servico_id = $servico["servico_salao_id"];
                $servicoObj->preco = $servico["preco_servico"];

                return $servicoObj;
            }, $servicos);

            // validar se existe um usuário/cliente cadastrado com o id informado
            if (empty($this->usuarioRepositorio->buscaPeloTipoUsuarioEId($usuarioId, "cliente"))) {
                Resposta::response(false, "Não existe um cliente cadastrado com o id informado.");
            }

            // validar se existe um usuário/salão cadastrado com o id informado
            if (empty($this->usuarioRepositorio->buscaPeloTipoUsuarioEId($usuarioSalaoId, "salão"))) {
                Resposta::response(false, "Não existe um salão cadastrado com o id informado.");
            }

            // validar se existe um horário cadastrado com o id informado
            $horario = $this->horarioRepositorio->buscarPeloId($horarioId);

            if (empty($horario)) {
                Resposta::response(false, "Não existe um horário cadastrado com o id informado.");
            }

            // validar se o horário já foi reservado
            if ($horario["reservado"]) {
                Resposta::response(false, "Esse horário já foi reservado.");
            }

            // calcular o valor total da reserva
            $valorTotalReserva = $this->calcularValorTotalReserva($servicos);

            $reserva = new Reserva();
            $reserva->setValorTotal($valorTotalReserva);
            $reserva->setUsuarioId($usuarioId);
            $reserva->setUsuarioSalaoId($usuarioSalaoId);
            $reserva->setHorarioSalaoId($horarioId);
            $reserva->setServicos($servicos);

            $this->reservaRepositorio->cadastrar($reserva);

            // alterar o status do horário para reservado
            $this->horarioRepositorio->alterarStatusHorario($horarioId, true);

            $this->bancoDados->commit();

            Resposta::response(true, "Reserva realizada com sucesso.", $reserva->toArray());
        } catch (Exception $e) {
            $this->bancoDados->rollBack();
            Log::erro("Erro ao tentar-se realizar a reserva: " . $e->getMessage(), $reserva->toArray());

            Resposta::response(false, "Erro ao tentar-se realizar a reserva: " . $e->getMessage());
        }

    }

    private function calcularValorTotalReserva($servicos) {
        $valorTotal = 0;

        foreach ($servicos as $servico) {
            $valorTotal += $servico->preco;
        }

        return $valorTotal;
    }

    private function buscarServicosRealizarReserva($servicosId) {
        $servicos = [];

        foreach ($servicosId as $servicoId) {
            $servico = $this->servicoRepositorio->buscarPeloId($servicoId->servico_id);

            if (empty($servico)) {

                throw new Exception("Não existe um serviço cadastrado com o id informado.");
            }

            $servicos[] = $servico->toArray();
        }

        return $servicos;
    }

    // listar as reservas
    public function listar() {

        try {
            $filtroReservas = new FiltroReservas();

            $errosFiltro = $filtroReservas->validarFiltro();

            if (!empty($errosFiltro)) {
                Resposta::response(false, "Erros no filtro.", $errosFiltro);
            }

            $filtroReservas->filtrar();
            $reservas = $filtroReservas->getDados();

            if (empty($reservas)) {
                Resposta::response(true, "Nenhuma reserva encontrada.", []);
            }

            Resposta::response(true, "Reservas listadas com sucesso.", $reservas);
        } catch (Exception $e) {
            Log::erro("Erro ao tentar-se listar as reservas: " . $e->getMessage());

            Resposta::response(false, "Erro ao tentar-se listar as reservas.");
        }

    }

}