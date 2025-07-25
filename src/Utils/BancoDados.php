<?php

namespace Utils;

use Exception;
use PDO;

require_once __DIR__ . "/../../api/constantes.php";

class BancoDados {

    // obter conexão com o banco de dados
    public static function conectarBancoDados() {
        
        try {
            $usuario = "root";
            $senha = "root";
            $bancoDados = "easy_salao_db_local";
            $host = "postgreSQL_db";
            $pdo = new PDO("pgsql:host=$host;dbname=$bancoDados", $usuario, $senha);
    
            return $pdo;
        } catch (Exception $e) {
            echo "Erro ao tentar-se conectar no banco de dados: " . $e->getMessage() . "<br>";

            throw new $e;
        }

    }

}