<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\DAO\MovimentacaoDAO;

$dao = new MovimentacaoDAO();

$movimentacoes = $dao->listar();

ob_start();

?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h1>Lista de Movimentações</h1>

        <a href="movimentacao-create.php" class="btn btn-primary">
            Nova Movimentação
        </a>

    </div>

    <?php if (count($movimentacoes) > 0): ?>

        <div class="table-responsive">

            <table class="table table-bordered table-striped">

                <thead class="table-dark">

                    <tr>
                        <th>Nome</th>
                        <th>Crédito</th>
                        <th>Débito</th>
                        <th>Data</th>
                        <th>Observação</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($movimentacoes as $movimentacao): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($movimentacao['nome']) ?>
                            </td>

                            <td class="text-success">
                                R$
                                <?= number_format(
                                    $movimentacao['Credito'] ?? 0,
                                    2,
                                    ',',
                                    '.'
                                ) ?>
                            </td>

                            <td class="text-danger">
                                R$
                                <?= number_format(
                                    $movimentacao['Debito'] ?? 0,
                                    2,
                                    ',',
                                    '.'
                                ) ?>
                            </td>

                            <td>
                                <?= date(
                                    'd/m/Y H:i',
                                    strtotime($movimentacao['DataOperacao'])
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $movimentacao['Observacao'] ?? ''
                                ) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="alert alert-info">
            Nenhuma movimentação cadastrada.
        </div>

    <?php endif; ?>

</div>

<?php

$content = ob_get_clean();

require __DIR__ . '/layout.php';