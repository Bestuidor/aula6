<?php

require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

$conexao = Database::conectar();

$pessoas = [];

if (isset($_GET['nome']) && $_GET['nome'] != '') {

    $nome = $_GET['nome'];

    $sql = "SELECT * FROM pessoas WHERE nome LIKE ?";

    $stmt = $conexao->prepare($sql);
    $stmt->execute(["%$nome%"]);

    $pessoas = $stmt->fetchAll();
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Pesquisar Pessoas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

    <h1>Pesquisar Pessoa</h1>

    <form method="GET">

        <div class="input-group mb-4">

            <input
                type="text"
                name="nome"
                class="form-control"
                placeholder="Digite o nome"
                value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>"
            >

            <button class="btn btn-primary" type="submit">
                Pesquisar
            </button>

        </div>

    </form>

    <?php if (isset($_GET['nome'])): ?>

        <?php if (count($pessoas) > 0): ?>

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
                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="alert alert-warning">
                Nenhuma pessoa encontrada.
            </div>

        <?php endif; ?>

    <?php endif; ?>

    <a href="listar.php" class="btn btn-secondary">
        Voltar
    </a>

</div>

</body>

</html>