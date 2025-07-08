<?php

namespace Utils;

class Resposta {

    public static function response(
        bool $ok = true,
        string $msg = "",
        mixed $dados = null
    ) {
        $resp = [
            "ok" => $ok,
            "msg" => $msg,
            "dados" => $dados
        ];

        if (!$ok) {
            // se tiver erro retornar o status-code 500
            http_response_code(500);
        } else {
            // se não tiver erro, retornar o status-code 200
            http_response_code(200);
        }

        echo json_encode($resp);
        exit();
    }

}