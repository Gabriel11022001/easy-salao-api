<?php

namespace Models;

use DateTime;
use PDO;
use Utils\BancoDados;

class FiltroReservas extends Filtro {

    private $ano;
    private $mes;
    private $dia;
    private $usuarioSalaoId;
    private $nomeCliente;
    /**
     * @property PDO
     */
    private $bancoDados;

    private $meses = [
        [
            "mes" => "Janeiro",
            "dias" => 31
        ],
        [
            "mes" => "Fevereiro",
            "dias" => 28 // pode ser 29 em anos bissextos
        ],
        [
            "mes" => "Março",
            "dias" => 31
        ],
        [
            "mes" => "Abril",
            "dias" => 30
        ],
        [
            "mes" => "Maio",
            "dias" => 31
        ],
        [
            "mes" => "Junho",
            "dias" => 30
        ],
        [
            "mes" => "Julho",
            "dias" => 31
        ],
        [
            "mes" => "Agosto",
            "dias" => 31
        ],
        [
            "mes" => "Setembro",
            "dias" => 30
        ],
        [
            "mes" => "Outubro",
            "dias" => 31
        ],
        [
            "mes" => "Novembro",
            "dias" => 30
        ],
        [
            "mes" => "Dezembro",
            "dias" => 31
        ]
    ];

    private $reservas = [];

    public function __construct()
    {
        $this->bancoDados = BancoDados::conectarBancoDados();

        $this->ano = getParametro("ano");
        $this->mes = getParametro("mes");
        $this->dia = getParametro("dia");
        $this->usuarioSalaoId = getParametro("usuario_salao_id");
        $this->nomeCliente = getParametro("nome_cliente");

        // validar se ano é bissexto
        $dataAtual = new DateTime("now");

        if ($dataAtual->format("L") == 1) {
            $this->meses[1]["dias"] = 29;
        }

    }

    // validar se o filtro está com dados válidos
    public function validarFiltro() {
        $erros = [];

        if (empty($this->usuarioSalaoId)) {
            $erros["usuario_salao_id"] = "Informe o id do usuário do salão.";
        }

        // validar se o ano é válido
        if (!empty($this->ano) && $this->ano < 0) {
            $erros["ano"] = "Ano inválido.";
        }

        // validar se o mês é válido
        if (!empty($this->mes)) {
            $mesesNomes = array_map(function ($mes) {

                return $mes["mes"];
            }, $this->meses);

            if (!in_array($this->mes, $mesesNomes)) {
                $erros["mes"] = "Mês inválido.";
            }

        }

        // validar o dia
        if (!empty($this->dia)) {

            foreach ($this->meses as $mesArray) {
                
                if ($mesArray["mes"] === $this->mes && ($this->dia < 1 || $this->dia > $mesArray["dias"])) {
                    $erros["dia"] = "Dia inválido.";
                }

            }

        }

        return $erros;
    }

    // aplicar o filtro
    public function filtrar() {
        $query = "SELECT r.reserva_id, r.valor_total, usuario_id AS id_usuario_cliente,
        h.ano, h.mes, h.dia, h.horario_de, h.horario_ate, h.reservado AS horario_reservado,
        s.servico_salao_id, s.nome_servico, 
        sr.preco_servico_momento_reserva AS preco_servico
        FROM tb_reservas AS r,
        tb_horarios AS h,
        tb_servicos_salao AS s,
        tb_reserva_servico AS sr
        WHERE r.reserva_id = sr.reserva_id
        AND s.servico_salao_id = sr.servico_id
        AND r.horario_salao_id = h.horario_id
        AND r.usuario_salao_id = :usuario_salao_id ";

        if (!empty($this->ano)) {
            $query .= " AND h.ano = :ano_filtro ";
        }

        if (!empty($this->mes)) {
            $query .= " AND h.mes = :mes_filtro ";
        }

        if (!empty($this->dia)) {
            $query .= " AND h.dia = :dia_filtro ";
        }

        $stmt = $this->bancoDados->prepare($query);
    
        $stmt->bindValue(":usuario_salao_id", $this->usuarioSalaoId);

        if (!empty($this->ano)) {
            $stmt->bindValue(":ano_filtro", $this->ano);
        }

        if (!empty($this->mes)) {
            $stmt->bindValue(":mes_filtro", $this->mes);
        }

        if (!empty($this->dia)) {
            $stmt->bindValue(":dia_filtro", $this->dia);
        }

        $stmt->execute();

        $reservasArray = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $idsReservasAtuais = array_map(function ($reserva) {

            return $reserva["reserva_id"];
        }, $this->reservas);

        for ($i = 0; $i < count($reservasArray); $i++) {

            if (in_array($reservasArray[$i]["reserva_id"], $idsReservasAtuais)) {
                
                continue;
            } else {
                $idsReservasAtuais[] = $reservasArray[$i]["reserva_id"];

                $this->reservas[] = [
                    "reserva_id" => $reservasArray[$i]["reserva_id"],
                    "valor_total" => $reservasArray[$i]["valor_total"],
                    "ano" => $reservasArray[$i]["ano"],
                    "mes" => $reservasArray[$i]["mes"],
                    "dia" => $reservasArray[$i]["dia"],
                    "horario_de" => $reservasArray[$i]["horario_de"],
                    "horario_ate" => $reservasArray[$i]["horario_ate"],
                    "reservado" => $reservasArray[$i]["horario_reservado"],
                    "id_usuario_cliente" => $reservasArray[$i]["id_usuario_cliente"]
                ];
            }

        }

        // mapear os serviços
        foreach ($this->reservas as $indice => $reserva) {
            
            foreach ($reservasArray as $reservaArray) {

                if ($reservaArray["reserva_id"] === $reserva["reserva_id"]) {
                    $this->reservas[$indice]["servicos"][] = [
                        "servico_id" => $reservaArray["servico_salao_id"],
                        "servico_nome" => $reservaArray["nome_servico"]
                    ];
                }

            }

        }

        // obter os dados do cliente de cada reserva
        foreach ($this->reservas as $indice => $reserva) {
            $stmt = $this->bancoDados->prepare("SELECT nome_completo AS cliente, email FROM
            tb_usuarios WHERE usuario_id = :id_usuario_cliente");
            $stmt->bindValue(":id_usuario_cliente", $reserva["id_usuario_cliente"]);
            $stmt->execute();

            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->reservas[$indice]["cliente"] = $cliente["cliente"];
            $this->reservas[$indice]["email_cliente"] = $cliente["email"];
        }

        // filtrar pelo nome
        if (!empty($this->nomeCliente)) {
            $nomeClienteValidar = $this->nomeCliente;

            $this->reservas = array_filter($this->reservas, function ($reservaValidar) use($nomeClienteValidar) {
                
                if (str_contains(strtolower($reservaValidar["cliente"]), strtolower($nomeClienteValidar))) {

                    return $reservaValidar;
                }

            });
        }

    }

    public function getDados() {

        return $this->reservas;
    }

}