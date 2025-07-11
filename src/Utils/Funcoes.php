<?php

namespace Utils;

class Funcoes {

    // validar e-mail
    public static function validarEmail($email) {
        $email = trim($email);

        if (empty($email)) {

            return false;
        }

        $regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

        return preg_match($regex, $email) === 1;
    }

}