<?php

require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

$conexao = Database::conectar();

if (!isset($_GET['id'])) {
    die("ID não informado.");
}

$id = $_GET['id'];

// Buscar pessoa
$sql = "SELECT * FROM pessoas WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->execute([$id]);

$pessoa = $stmt->fetch();

if (!$pessoa) {
    die("Pessoa não encontrada.");
}

// Atualizar
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = $_POST["nome"];
    $telefone = $_POST["telefone"];
    $cpf = $_POST["cpf"];
    $endereco = $_POST["endereco"];

    $sql = "UPDATE pessoas
            SET nome = ?, telefone = ?, cpf = ?, endereco = ?
            WHERE id = ?";

    $stmt = $conexao->prepare($sql);

    $stmt->execute([
        $nome,
        $telefone,
        $cpf,
        $endereco,
        $id
    ]);

    header("Location: listar.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">

    <title>Editar Pessoa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

    <h1>Editar Pessoa</h1>

    <form method="POST">

        <div class="mb-3">
            <label>Nome</label>

            <input
                type="text"
                name="nome"
                class="form-control"
                value="<?= htmlspecialchars($pessoa['nome']) ?>"
                required
            >
        </div>

        <div class="mb-3">
            <label>Telefone</label>

            <input
                type="text"
                name="telefone"
                class="form-control"
                value="<?= htmlspecialchars($pessoa['telefone']) ?>"
            >
        </div>

        <div class="mb-3">
            <label>CPF</label>

            <input
                type="text"
                name="cpf"
                class="form-control"
                value="<?= htmlspecialchars($pessoa['cpf']) ?>"
                required
            >
        </div>

        <div class="mb-3">
            <label>Endereço</label>

            <input
                type="text"
                name="endereco"
                class="form-control"
                value="<?= htmlspecialchars($pessoa['endereco']) ?>"
            >
        </div>

        <button type="submit" class="btn btn-success">
            Salvar Alterações
        </button>

        <a href="listar.php" class="btn btn-secondary">
            Cancelar
        </a>

    </form>

</div>

</body>

</html>