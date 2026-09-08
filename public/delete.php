<?php

require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

if (!isset($_GET['id'])) {
    die("ID da pessoa não informado.");
}

$id = $_GET['id'];

$conexao = Database::conectar();

$sql = "DELETE FROM pessoas WHERE id = ?";

$stmt = $conexao->prepare($sql);

$stmt->execute([$id]);

header("Location: listar.php");
exit;