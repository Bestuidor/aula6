<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">

    <title>Cadastrar Pessoa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

    <h1 class="mb-4">Cadastrar Pessoa</h1>

    <form action="pessoa-create.php" method="POST">

        <div class="mb-3">
            <label for="nome" class="form-label">
                Nome
            </label>

            <input
                type="text"
                name="nome"
                id="nome"
                class="form-control"
                required
            >
        </div>


        <div class="mb-3">
            <label for="telefone" class="form-label">
                Telefone
            </label>

            <input
                type="text"
                name="telefone"
                id="telefone"
                class="form-control"
            >
        </div>


        <div class="mb-3">
            <label for="cpf" class="form-label">
                CPF
            </label>

            <input
                type="text"
                name="cpf"
                id="cpf"
                class="form-control"
                maxlength="11"
                required
            >
        </div>


        <div class="mb-3">
            <label for="endereco" class="form-label">
                Endereço
            </label>

            <input
                type="text"
                name="endereco"
                id="endereco"
                class="form-control"
            >
        </div>


        <button type="submit" class="btn btn-success">
            Cadastrar
        </button>

        <a href="listar.php" class="btn btn-secondary">
            Voltar
        </a>

    </form>

</div>

</body>

</html>