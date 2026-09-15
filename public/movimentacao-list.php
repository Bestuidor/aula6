<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\DAO\MovimentacaoDAO;

$dao = new MovimentacaoDAO();

$movimentacoes = $dao->listar();

$totalCredito = 0;
$totalDebito = 0;

/*
|--------------------------------------------------------------------------
| CALCULAR SALDO INDIVIDUAL DE CADA PESSOA
|--------------------------------------------------------------------------
*/

$saldosPessoas = [];

foreach ($movimentacoes as $movimentacao) {

    $idPessoa = $movimentacao['idPessoa'] ?? null;

    $credito = (float) ($movimentacao['Credito'] ?? 0);
    $debito = (float) ($movimentacao['Debito'] ?? 0);

    // Totais gerais
    $totalCredito += $credito;
    $totalDebito += $debito;

    // Calcula o saldo de cada pessoa
    if ($idPessoa !== null) {

        if (!isset($saldosPessoas[$idPessoa])) {
            $saldosPessoas[$idPessoa] = 0;
        }

        $saldosPessoas[$idPessoa] += $credito;
        $saldosPessoas[$idPessoa] -= $debito;
    }
}


// Saldo geral
$saldo = $totalCredito - $totalDebito;

ob_start();

?>

<div class="container mt-4">

    <!-- CABEÇALHO -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="fw-bold">
                Movimentações
            </h1>

            <p class="text-muted mb-0">
                Controle de créditos e débitos das pessoas
            </p>

        </div>

        <a
            href="movimentacao-create.php"
            class="btn btn-primary"
        >
            + Nova Movimentação
        </a>

    </div>


    <!-- CARDS DE RESUMO -->

    <div class="row g-3 mb-4">


        <!-- TOTAL DE CRÉDITOS -->

        <div class="col-md-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <p class="text-muted mb-1">
                        Total de Créditos
                    </p>

                    <h3 class="text-success fw-bold">

                        R$ <?= number_format(
                            $totalCredito,
                            2,
                            ',',
                            '.'
                        ) ?>

                    </h3>

                    <small class="text-muted">
                        Dinheiro que entrou
                    </small>

                </div>

            </div>

        </div>


        <!-- TOTAL DE DÉBITOS -->

        <div class="col-md-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <p class="text-muted mb-1">
                        Total de Débitos
                    </p>

                    <h3 class="text-danger fw-bold">

                        R$ <?= number_format(
                            $totalDebito,
                            2,
                            ',',
                            '.'
                        ) ?>

                    </h3>

                    <small class="text-muted">
                        Dinheiro que saiu
                    </small>

                </div>

            </div>

        </div>


        <!-- SALDO GERAL -->

        <div class="col-md-4">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <p class="text-muted mb-1">
                        Saldo Geral
                    </p>

                    <h3
                        class="<?= $saldo >= 0
                            ? 'text-primary'
                            : 'text-danger' ?> fw-bold"
                    >

                        R$ <?= number_format(
                            $saldo,
                            2,
                            ',',
                            '.'
                        ) ?>

                    </h3>

                    <small class="text-muted">
                        Créditos - Débitos
                    </small>

                </div>

            </div>

        </div>

    </div>


    <!-- HISTÓRICO -->

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h5 class="fw-bold mb-0">
                    Histórico de Movimentações
                </h5>

                <span class="badge bg-secondary">

                    <?= count($movimentacoes) ?>

                    <?= count($movimentacoes) == 1
                        ? 'movimentação'
                        : 'movimentações' ?>

                </span>

            </div>


            <?php if (count($movimentacoes) > 0): ?>

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead class="table-dark">

                            <tr>

                                <th>Pessoa</th>

                                <th>Tipo</th>

                                <th>Valor</th>

                                <th>Saldo da Pessoa</th>

                                <th>Data</th>

                                <th>Observação</th>

                                <th>Ações</th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach ($movimentacoes as $movimentacao): ?>

                                <?php

                                $credito = (float) (
                                    $movimentacao['Credito'] ?? 0
                                );

                                $debito = (float) (
                                    $movimentacao['Debito'] ?? 0
                                );

                                $idPessoa = $movimentacao['idPessoa'] ?? null;

                                // Saldo individual
                                $saldoPessoa = 0;

                                if ($idPessoa !== null) {

                                    $saldoPessoa =
                                        $saldosPessoas[$idPessoa] ?? 0;
                                }

                                ?>


                                <tr>


                                    <!-- PESSOA -->

                                    <td>

                                        <strong>

                                            <?= htmlspecialchars(
                                                $movimentacao['nome']
                                                    ?? 'Não informado'
                                            ) ?>

                                        </strong>

                                    </td>


                                    <!-- TIPO -->

                                    <td>

                                        <?php if ($credito > 0): ?>

                                            <span class="badge bg-success">
                                                Crédito
                                            </span>

                                        <?php elseif ($debito > 0): ?>

                                            <span class="badge bg-danger">
                                                Débito
                                            </span>

                                        <?php else: ?>

                                            <span class="badge bg-secondary">
                                                Sem valor
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- VALOR -->

                                    <td>

                                        <?php if ($credito > 0): ?>

                                            <span class="text-success fw-bold">

                                                + R$
                                                <?= number_format(
                                                    $credito,
                                                    2,
                                                    ',',
                                                    '.'
                                                ) ?>

                                            </span>

                                        <?php elseif ($debito > 0): ?>

                                            <span class="text-danger fw-bold">

                                                - R$
                                                <?= number_format(
                                                    $debito,
                                                    2,
                                                    ',',
                                                    '.'
                                                ) ?>

                                            </span>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                R$ 0,00
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- SALDO INDIVIDUAL -->

                                    <td>

                                        <span
                                            class="fw-bold <?= $saldoPessoa >= 0
                                                ? 'text-primary'
                                                : 'text-danger' ?>"
                                        >

                                            R$
                                            <?= number_format(
                                                $saldoPessoa,
                                                2,
                                                ',',
                                                '.'
                                            ) ?>

                                        </span>

                                    </td>


                                    <!-- DATA -->

                                    <td>

                                        <?php

                                        if (!empty(
                                            $movimentacao['DataOperacao']
                                        )) {

                                            echo date(
                                                'd/m/Y H:i',
                                                strtotime(
                                                    $movimentacao['DataOperacao']
                                                )
                                            );

                                        } else {

                                            echo '-';

                                        }

                                        ?>

                                    </td>


                                    <!-- OBSERVAÇÃO -->

                                    <td>

                                        <?php

                                        $observacao =
                                            $movimentacao['Observacao']
                                            ?? '';

                                        if ($observacao !== ''):

                                        ?>

                                            <?= htmlspecialchars(
                                                $observacao
                                            ) ?>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                Sem observação
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- AÇÕES -->

                                    <td>

                                        <?php if (
                                            isset($movimentacao['id'])
                                        ): ?>

                                            <div class="d-flex gap-2">

                                                <a
                                                    href="movimentacao-edit.php?id=<?= $movimentacao['id'] ?>"
                                                    class="btn btn-sm btn-warning"
                                                >
                                                    Editar
                                                </a>


                                                <a
                                                    href="movimentacao-delete.php?id=<?= $movimentacao['id'] ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Tem certeza que deseja excluir esta movimentação?')"
                                                >
                                                    Excluir
                                                </a>

                                            </div>

                                        <?php endif; ?>

                                    </td>


                                </tr>


                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>


            <?php else: ?>


                <!-- SEM MOVIMENTAÇÕES -->

                <div class="text-center py-5">

                    <h4 class="text-muted">
                        Nenhuma movimentação encontrada
                    </h4>

                    <p class="text-muted">
                        Comece cadastrando uma nova movimentação.
                    </p>

                    <a
                        href="movimentacao-create.php"
                        class="btn btn-primary"
                    >
                        + Cadastrar Movimentação
                    </a>

                </div>


            <?php endif; ?>

        </div>

    </div>

</div>


<?php

$content = ob_get_clean();

require __DIR__ . '/layout.php';

?>