<?php
$content = ob_get_clean();
?>

<?php

$content = '
<div class="container mt-4">

    <h2>Cadastro de Pessoa</h2>

    <form action="pessoa-cadastrar.php" method="POST">

        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input 
                type="text" 
                class="form-control" 
                id="nome" 
                name="nome" 
                maxlength="100" 
                required>
        </div>

        <div class="mb-3">
            <label for="telefone" class="form-label">Telefone</label>
            <input 
                type="text" 
                class="form-control" 
                id="telefone" 
                name="telefone" 
                maxlength="15">
        </div>

        <div class="mb-3">
            <label for="cpf" class="form-label">CPF</label>
            <input 
                type="text" 
                class="form-control" 
                id="cpf" 
                name="cpf" 
                maxlength="11" 
                required>
        </div>

        <div class="mb-3">
            <label for="endereco" class="form-label">Endereço</label>
            <input 
                type="text" 
                class="form-control" 
                id="endereco" 
                name="endereco" 
                maxlength="255">
        </div>

        <button type="submit" class="btn btn-primary">
            Cadastrar
        </button>

    </form>

</div>
';

include "layout.php";
?>
<?php
require "footer.php";

?>