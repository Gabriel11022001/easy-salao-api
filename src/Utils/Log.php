<?php

namespace Utils;

use DateTime;

class Log {

    // registrar log de erro
    public static function erro($mensagemErro) {
        $dataAtual = new DateTime("now");
        $caminho = __DIR__ . "/../../logs/erros/";

        $nomeArquivoLog = "log-erro-" . $dataAtual->format("d-m-Y") . ".log";

        $caminho .= $nomeArquivoLog;

        file_put_contents($caminho, "[ ERRO ] - " . $dataAtual->format("d-m-Y H:i:s") . " -> " . $mensagemErro . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND);
    }

}