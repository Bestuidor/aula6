<?php

namespace App\Config;

use PDO;
use PDOException;

class Conexao
{
    private static ?PDO $conexao = null;

    private function __construct()
    {
    }

    public static function conectar(): PDO
    {
        if (self::$conexao === null) {

            $host = "localhost";
            $dbname = "aula6";
            $usuario = "root";
            $senha = "";

            try {

                self::$conexao = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                    $usuario,
                    $senha
                );

                self::$conexao->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );

                self::$conexao->setAttribute(
                    PDO::ATTR_DEFAULT_FETCH_MODE,
                    PDO::FETCH_ASSOC
                );

            } catch (PDOException $e) {

                die("Erro na conexão: " . $e->getMessage());

            }
        }

        return self::$conexao;
    }
}