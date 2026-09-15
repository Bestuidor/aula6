<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Conexao;

$conexao = Conexao::conectar();

$mensagem = "";
$tipoMensagem = "";


/*
|--------------------------------------------------------------------------
| PESQUISA DE PESSOAS
|--------------------------------------------------------------------------
*/

if (isset($_GET['buscar'])) {

    $busca = trim($_GET['buscar']);

    $sql = "SELECT id, nome, cpf
            FROM pessoas
            WHERE nome LIKE :nome
            ORDER BY nome
            LIMIT 10";

    $stmt = $conexao->prepare($sql);

    $stmt->bindValue(
        ':nome',
        '%' . $busca . '%'
    );

    $stmt->execute();

    $pessoas = $stmt->fetchAll();

    header('Content-Type: application/json; charset=utf-8');

    echo json_encode($pessoas);

    exit;
}


/*
|--------------------------------------------------------------------------
| REALIZAR MOVIMENTAÇÃO
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tipo = $_POST['tipo'] ?? '';
    $idPessoa = $_POST['idPessoa'] ?? '';
    $idDestino = $_POST['idDestino'] ?? '';
    $valor = $_POST['valor'] ?? '';
    $observacao = trim($_POST['observacao'] ?? '');


    /*
    |--------------------------------------------------------------------------
    | CONVERTER VALOR
    |--------------------------------------------------------------------------
    |
    | Exemplo:
    | 10,00     -> 10.00
    | 1.500,50  -> 1500.50
    |
    */

    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);
    $valor = (float) $valor;


    /*
    |--------------------------------------------------------------------------
    | VALIDAÇÕES
    |--------------------------------------------------------------------------
    */

    if ($tipo === '') {

        $mensagem = "Selecione o tipo de movimentação.";
        $tipoMensagem = "danger";

    } elseif ($idPessoa === '') {

        $mensagem = "Selecione uma pessoa.";
        $tipoMensagem = "danger";

    } elseif ($valor <= 0) {

        $mensagem = "Digite um valor válido.";
        $tipoMensagem = "danger";

    } elseif ($tipo === 'transferir' && $idDestino === '') {

        $mensagem = "Selecione a pessoa que receberá a transferência.";
        $tipoMensagem = "danger";

    } elseif ($tipo === 'transferir' && $idPessoa == $idDestino) {

        $mensagem = "A pessoa de origem e destino não podem ser iguais.";
        $tipoMensagem = "danger";

    } else {

        try {

            /*
            |--------------------------------------------------------------------------
            | DEPOSITAR
            |--------------------------------------------------------------------------
            */

            if ($tipo === 'depositar') {

                $sql = "INSERT INTO movimentacao
                        (idPessoa, Credito, Debito, Observacao)
                        VALUES
                        (:idPessoa, :credito, 0, :observacao)";

                $stmt = $conexao->prepare($sql);

                $stmt->bindValue(
                    ':idPessoa',
                    $idPessoa,
                    PDO::PARAM_INT
                );

                $stmt->bindValue(
                    ':credito',
                    $valor
                );

                $stmt->bindValue(
                    ':observacao',
                    $observacao
                );

                $stmt->execute();

                $mensagem = "Depósito realizado com sucesso!";
                $tipoMensagem = "success";
            }


            /*
            |--------------------------------------------------------------------------
            | SACAR
            |--------------------------------------------------------------------------
            */

            elseif ($tipo === 'sacar') {

                // Consulta o saldo atual da pessoa
                $sqlSaldo = "SELECT
                                COALESCE(SUM(Credito), 0) -
                                COALESCE(SUM(Debito), 0) AS saldo
                             FROM movimentacao
                             WHERE idPessoa = :idPessoa";

                $stmtSaldo = $conexao->prepare($sqlSaldo);

                $stmtSaldo->bindValue(
                    ':idPessoa',
                    $idPessoa,
                    PDO::PARAM_INT
                );

                $stmtSaldo->execute();

                $resultadoSaldo = $stmtSaldo->fetch();

                $saldo = (float) $resultadoSaldo['saldo'];


                // Verifica se existe saldo suficiente
                if ($valor > $saldo) {

                    $mensagem = "Saldo insuficiente. Saldo disponível: R$ "
                        . number_format(
                            $saldo,
                            2,
                            ',',
                            '.'
                        );

                    $tipoMensagem = "danger";

                } else {

                    $sql = "INSERT INTO movimentacao
                            (idPessoa, Credito, Debito, Observacao)
                            VALUES
                            (:idPessoa, 0, :debito, :observacao)";

                    $stmt = $conexao->prepare($sql);

                    $stmt->bindValue(
                        ':idPessoa',
                        $idPessoa,
                        PDO::PARAM_INT
                    );

                    $stmt->bindValue(
                        ':debito',
                        $valor
                    );

                    $stmt->bindValue(
                        ':observacao',
                        $observacao
                    );

                    $stmt->execute();

                    $mensagem = "Saque realizado com sucesso!";
                    $tipoMensagem = "success";
                }
            }


            /*
            |--------------------------------------------------------------------------
            | TRANSFERÊNCIA
            |--------------------------------------------------------------------------
            */

            elseif ($tipo === 'transferir') {

                $conexao->beginTransaction();


                /*
                |--------------------------------------------------------------------------
                | CONSULTAR SALDO DA PESSOA DE ORIGEM
                |--------------------------------------------------------------------------
                */

                $sqlSaldo = "SELECT
                                COALESCE(SUM(Credito), 0) -
                                COALESCE(SUM(Debito), 0) AS saldo
                             FROM movimentacao
                             WHERE idPessoa = :idPessoa";

                $stmtSaldo = $conexao->prepare($sqlSaldo);

                $stmtSaldo->bindValue(
                    ':idPessoa',
                    $idPessoa,
                    PDO::PARAM_INT
                );

                $stmtSaldo->execute();

                $resultadoSaldo = $stmtSaldo->fetch();

                $saldo = (float) $resultadoSaldo['saldo'];


                /*
                |--------------------------------------------------------------------------
                | VERIFICAR SALDO
                |--------------------------------------------------------------------------
                */

                if ($valor > $saldo) {

                    $conexao->rollBack();

                    $mensagem = "Saldo insuficiente para realizar a transferência. Saldo disponível: R$ "
                        . number_format(
                            $saldo,
                            2,
                            ',',
                            '.'
                        );

                    $tipoMensagem = "danger";

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | DÉBITO DA PESSOA QUE ENVIA
                    |--------------------------------------------------------------------------
                    */

                    $sqlDebito = "INSERT INTO movimentacao
                                  (idPessoa, Credito, Debito, Observacao)
                                  VALUES
                                  (:idPessoa, 0, :debito, :observacao)";

                    $stmtDebito = $conexao->prepare($sqlDebito);

                    $stmtDebito->bindValue(
                        ':idPessoa',
                        $idPessoa,
                        PDO::PARAM_INT
                    );

                    $stmtDebito->bindValue(
                        ':debito',
                        $valor
                    );

                    $observacaoDebito = "Transferência enviada. "
                        . $observacao;

                    $stmtDebito->bindValue(
                        ':observacao',
                        $observacaoDebito
                    );

                    $stmtDebito->execute();


                    /*
                    |--------------------------------------------------------------------------
                    | CRÉDITO DA PESSOA QUE RECEBE
                    |--------------------------------------------------------------------------
                    */

                    $sqlCredito = "INSERT INTO movimentacao
                                   (idPessoa, Credito, Debito, Observacao)
                                   VALUES
                                   (:idPessoa, :credito, 0, :observacao)";

                    $stmtCredito = $conexao->prepare($sqlCredito);

                    $stmtCredito->bindValue(
                        ':idPessoa',
                        $idDestino,
                        PDO::PARAM_INT
                    );

                    $stmtCredito->bindValue(
                        ':credito',
                        $valor
                    );

                    $observacaoCredito = "Transferência recebida. "
                        . $observacao;

                    $stmtCredito->bindValue(
                        ':observacao',
                        $observacaoCredito
                    );

                    $stmtCredito->execute();


                    /*
                    |--------------------------------------------------------------------------
                    | FINALIZAR TRANSFERÊNCIA
                    |--------------------------------------------------------------------------
                    */

                    $conexao->commit();

                    $mensagem = "Transferência realizada com sucesso!";
                    $tipoMensagem = "success";
                }
            }


            /*
            |--------------------------------------------------------------------------
            | TIPO INVÁLIDO
            |--------------------------------------------------------------------------
            */

            else {

                $mensagem = "Tipo de movimentação inválido.";
                $tipoMensagem = "danger";
            }


        } catch (Exception $e) {

            if ($conexao->inTransaction()) {
                $conexao->rollBack();
            }

            $mensagem = "Erro ao realizar movimentação: "
                . $e->getMessage();

            $tipoMensagem = "danger";
        }
    }
}


/*
|--------------------------------------------------------------------------
| INÍCIO DA PÁGINA
|--------------------------------------------------------------------------
*/

ob_start();

?>

<div class="container mt-4">

    <h1 class="mb-4">
        Nova Movimentação
    </h1>


    <?php if ($mensagem): ?>

        <div class="alert alert-<?= $tipoMensagem ?>">
            <?= htmlspecialchars($mensagem) ?>
        </div>

    <?php endif; ?>


    <form method="POST">


        <!-- TIPO -->

        <div class="mb-3">

            <label class="form-label">
                Tipo de movimentação
            </label>

            <select
                name="tipo"
                id="tipo"
                class="form-select"
                required
            >

                <option value="">
                    Selecione
                </option>

                <option value="depositar">
                    Depositar
                </option>

                <option value="sacar">
                    Sacar
                </option>

                <option value="transferir">
                    Transferir
                </option>

            </select>

        </div>


        <!-- PESSOA -->

        <div class="mb-3">

            <label class="form-label">
                Pessoa
            </label>

            <input
                type="text"
                id="pesquisaPessoa"
                class="form-control"
                placeholder="Digite o nome da pessoa..."
                autocomplete="off"
            >

            <input
                type="hidden"
                name="idPessoa"
                id="idPessoa"
            >

            <div
                id="resultadosPessoa"
                class="list-group mt-1"
            ></div>

            <div
                id="pessoaSelecionada"
                class="mt-2"
            ></div>

        </div>


        <!-- DESTINO -->

        <div
            class="mb-3"
            id="campoDestino"
            style="display: none;"
        >

            <label class="form-label">
                Pessoa que receberá
            </label>

            <input
                type="text"
                id="pesquisaDestino"
                class="form-control"
                placeholder="Digite o nome do destinatário..."
                autocomplete="off"
            >

            <input
                type="hidden"
                name="idDestino"
                id="idDestino"
            >

            <div
                id="resultadosDestino"
                class="list-group mt-1"
            ></div>

            <div
                id="destinoSelecionado"
                class="mt-2"
            ></div>

        </div>


        <!-- VALOR -->

        <div class="mb-3">

            <label class="form-label">
                Valor
            </label>

            <input
                type="text"
                name="valor"
                id="valor"
                class="form-control"
                placeholder="0,00"
                inputmode="decimal"
                required
            >

        </div>


        <!-- OBSERVAÇÃO -->

        <div class="mb-3">

            <label class="form-label">
                Observação
            </label>

            <textarea
                name="observacao"
                class="form-control"
                rows="3"
                placeholder="Digite uma observação"
            ></textarea>

        </div>


        <!-- BOTÕES -->

        <button
            type="submit"
            class="btn btn-primary"
        >
            Realizar Movimentação
        </button>

        <a
            href="movimentacao-list.php"
            class="btn btn-secondary"
        >
            Voltar
        </a>

    </form>

</div>


<script>

/*
|--------------------------------------------------------------------------
| PESQUISAR PESSOA
|--------------------------------------------------------------------------
*/

function pesquisarPessoa(
    campoPesquisa,
    campoId,
    resultados,
    selecionado
) {

    const input = document.getElementById(campoPesquisa);

    const idInput = document.getElementById(campoId);

    const resultadoDiv = document.getElementById(resultados);

    const selecionadoDiv = document.getElementById(selecionado);


    input.addEventListener('input', function () {

        const busca = this.value.trim();


        idInput.value = "";

        selecionadoDiv.innerHTML = "";

        resultadoDiv.innerHTML = "";


        if (busca.length < 2) {
            return;
        }


        fetch(
            'movimentacao-create.php?buscar='
            + encodeURIComponent(busca)
        )

        .then(response => {

            if (!response.ok) {
                throw new Error('Erro na pesquisa');
            }

            return response.json();

        })

        .then(pessoas => {

            resultadoDiv.innerHTML = "";


            if (pessoas.length === 0) {

                resultadoDiv.innerHTML = `
                    <div class="list-group-item">
                        Nenhuma pessoa encontrada.
                    </div>
                `;

                return;
            }


            pessoas.forEach(pessoa => {

                const item = document.createElement('button');

                item.type = 'button';

                item.className =
                    'list-group-item list-group-item-action';


                item.innerHTML = `
                    <strong>${escapeHtml(pessoa.nome)}</strong>
                    <br>
                    <small>CPF: ${escapeHtml(pessoa.cpf)}</small>
                `;


                item.addEventListener('click', function () {

                    input.value = pessoa.nome;

                    idInput.value = pessoa.id;

                    resultadoDiv.innerHTML = "";


                    selecionadoDiv.innerHTML = `
                        <div class="alert alert-success py-2">
                            <strong>Pessoa selecionada:</strong>
                            ${escapeHtml(pessoa.nome)}
                        </div>
                    `;

                });


                resultadoDiv.appendChild(item);

            });

        })

        .catch(error => {

            console.error(error);

            resultadoDiv.innerHTML = `
                <div class="list-group-item text-danger">
                    Erro ao pesquisar pessoa.
                </div>
            `;

        });

    });

}


/*
|--------------------------------------------------------------------------
| PROTEÇÃO CONTRA HTML
|--------------------------------------------------------------------------
*/

function escapeHtml(text) {

    const div = document.createElement('div');

    div.textContent = text;

    return div.innerHTML;

}


/*
|--------------------------------------------------------------------------
| PESQUISA DA PESSOA DE ORIGEM
|--------------------------------------------------------------------------
*/

pesquisarPessoa(
    'pesquisaPessoa',
    'idPessoa',
    'resultadosPessoa',
    'pessoaSelecionada'
);


/*
|--------------------------------------------------------------------------
| PESQUISA DO DESTINO
|--------------------------------------------------------------------------
*/

pesquisarPessoa(
    'pesquisaDestino',
    'idDestino',
    'resultadosDestino',
    'destinoSelecionado'
);


/*
|--------------------------------------------------------------------------
| MOSTRAR/ESCONDER DESTINO
|--------------------------------------------------------------------------
*/

const tipo = document.getElementById('tipo');

const campoDestino = document.getElementById('campoDestino');


tipo.addEventListener('change', function () {

    if (this.value === 'transferir') {

        campoDestino.style.display = 'block';

    } else {

        campoDestino.style.display = 'none';

        document.getElementById('pesquisaDestino').value = '';

        document.getElementById('idDestino').value = '';

        document.getElementById('resultadosDestino').innerHTML = '';

        document.getElementById('destinoSelecionado').innerHTML = '';

    }

});


/*
|--------------------------------------------------------------------------
| MÁSCARA DE VALOR
|--------------------------------------------------------------------------
*/

const campoValor = document.getElementById('valor');


campoValor.addEventListener('input', function () {

    let valor = this.value.replace(/\D/g, '');


    if (valor === '') {

        this.value = '';

        return;
    }


    valor = (parseInt(valor, 10) / 100).toFixed(2);


    this.value = valor
        .replace('.', ',')
        .replace(/\B(?=(\d{3})+(?!\d))/g, '.');

});

</script>


<?php

$content = ob_get_clean();

require __DIR__ . '/layout.php';

?>