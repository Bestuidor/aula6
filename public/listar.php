<?php

require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

$conexao = Database::conectar();

$sql = "SELECT * FROM pessoas ORDER BY id DESC";

$stmt = $conexao->query($sql);

$pessoas = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Lista de Pessoas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

    <h1>Lista de Pessoas</h1>

    <a href="pessoa-create.php" class="btn btn-primary">
    Cadastrar Pessoa
</a>

    <table class="table table-bordered table-striped">

        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Telefone</th>
                <th>CPF</th>
                <th>Endereço</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach ($pessoas as $pessoa): ?>

            <tr>
                <td><?= $pessoa['id'] ?></td>

                <td><?= htmlspecialchars($pessoa['nome']) ?></td>

                <td><?= htmlspecialchars($pessoa['telefone']) ?></td>

                <td><?= htmlspecialchars($pessoa['cpf']) ?></td>

                <td><?= htmlspecialchars($pessoa['endereco']) ?></td>
        
            <td>

    <a href="editar.php?id=<?= $pessoa['id'] ?>"
       class="btn btn-warning btn-sm">
        Editar
    </a>

    <a href="delete.php?id=<?= $pessoa['id'] ?>"
       class="btn btn-danger btn-sm"
       onclick="return confirm('Deseja realmente excluir?')">
        Excluir
    </a>

</td>
</tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>

</body>

</html>