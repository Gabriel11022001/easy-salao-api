<?php

namespace Utils;

use DateTime;

require_once __DIR__ . "/../../api/constantes.php";

class Metodos {

    /**
     * validar os campos no cadastro do horário do salão
     * @param array $campos
     * @return array
     */
    public static function validarCamposCadastroHorario($campos) {
        $erros = array();

        if (empty($campos["ano"])) {
            $erros["ano"] = "Informe o ano.";
        } elseif ($campos["ano"] <= 0) {
            $erros["ano"] = "Ano inválido.";
        } elseif (self::validarAnoInformadoMenorAnoAtual($campos["ano"])) { 
            $erros["ano"] = "Ano inválido.";
        }

        if (empty($campos["mes"])) {
            $erros["mes"] = "Informe o mês.";
        } elseif (!in_array($campos["mes"], MESES)) {
            $erros["mes"] = "Mês inválido.";
        } elseif (empty($campos["dia"])) {
            $erros["dia"] = "Informe o dia.";
        } elseif (!self::validarDiaMesInvalido($campos["mes"], $campos["dia"])) {
            $erros["dia"] = "Dia inválido";
        }

        if (empty($campos["horario_de"])) {
            $erros["horario_de"] = "Informe o horário inicial.";
        } elseif (empty($campos["horario_ate"])) {
            $erros["horario_ate"] = "Informe o horário final.";
        } elseif (self::validarHorarioDeMaiorIgualHorarioAte(
            $campos["horario_de"],
            $campos["horario_ate"]
        )) {
            $erros["horario_de"] = "Horário inicial deve ser menor que o horário final.";
        }

        if (empty($campos["usuario_salao_id"])) {
            $erros["usuario_salao_id"] = "Informe o id do usuário do salão.";
        }

        return $erros;
    }

    // validar se o dia do mês está correto ou inválido
    private static function validarDiaMesInvalido($mes, $dia) {
        $dataAtual = new DateTime("now");
        $anoAtual = $dataAtual->format("Y");

        // validar se o ano atual é bissexto
        $isBissexto = (date("L", strtotime($anoAtual))) ? true : false; 

        $meses = [
            [
                "mes" => "Janeiro",
                "quantidade_dias" => 31
            ],
            [
                "mes" => "Fevereiro",
                "quantidade_dias" => $isBissexto ? 29 : 28
            ],
            [
                "mes" => "Março",
                "quantidade_dias" => 31
            ],
            [
                "mes" => "Abril",
                "quantidade_dias" => 30
            ],
            [
                "mes" => "Maio",
                "quantidade_dias" => 31
            ],
            [
                "mes" => "Junho",
                "quantidade_dias" => 30
            ],
            [
                "mes" => "Julho",
                "quantidade_dias" => 31
            ],
            [
                "mes" => "Agosto",
                "quantidade_dias" => 31
            ],
            [
                "mes" => "Setembro",
                "quantidade_dias" => 30
            ],
            [
                "mes" => "Outubro",
                "quantidade_dias" => 31
            ],
            [
                "mes" => "Novembro",
                "quantidade_dias" => 30
            ],
            [
                "mes" => "Dezembro",
                "quantidade_dias" => 31
            ]
        ];

        $ok = true;

        foreach ($meses as $mesValidar) {

            if ($mesValidar["mes"] === $mes && ($dia > $mesValidar["quantidade_dias"] || $dia < 1)) {
                $ok = false;
            }

        }

        return $ok;
    }

    // validar se o horário inicial é maior ou igual o horário final
    private static function validarHorarioDeMaiorIgualHorarioAte($horarioDe, $horarioAte) {

        return false;
    }

    // validar se o ano informado é menor que o ano atual
    private static function validarAnoInformadoMenorAnoAtual($anoInformado) {
        $dataAtual = new DateTime("now");
        $anoAtual = $dataAtual->format("Y");

        if ($anoInformado < $anoAtual) {

            return true;
        }

        return false;
    }

}