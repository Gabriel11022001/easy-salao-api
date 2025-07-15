<?php

namespace Utils;

use stdClass;

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

        http_response_code(200);

        if (empty($resp["dados"]) && !is_array($resp["dados"])) {
            $resp["dados"] = new stdClass();
        }

        echo json_encode($resp);
        exit();
    }

}