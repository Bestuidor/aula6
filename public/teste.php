<?php

require_once "../vendor/autoload.php";

use App\Config\Conexao;

$pdo = Conexao::conectar();

echo "Conectado com sucesso!";