<?php

require_once __DIR__ . '/../src/Config/Conexao.php';

use App\Config\Conexao;

echo "1 - Arquivo PHP funcionando<br>";

$conexao = Conexao::conectar();

echo "2 - Conexão funcionando<br>";

$sql = "SELECT * FROM pessoas";

$stmt = $conexao->query($sql);

echo "3 - Consulta funcionando<br>";

$pessoas = $stmt->fetchAll();

echo "<pre>";
print_r($pessoas);
echo "</pre>";