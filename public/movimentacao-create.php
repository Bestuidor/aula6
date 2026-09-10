<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Conexao;

$conexao = Conexao::conectar();

$mensagem = "";
$tipoMensagem = "";


// BUSCAR TODAS AS PESSOAS
$sql = "SELECT id, nome FROM pessoas ORDER BY nome";

$stmt = $conexao->query($sql);

$pessoas = $stmt->fetchAll();


// VERIFICAR SE O FORMULÁRIO FOI ENVIADO
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $tipo = $_POST["tipo"] ?? "";
    $idPessoa = $_POST["idPessoa"] ?? "";
    $valor = $_POST["valor"] ?? "";
    $observacao = $_POST["observacao"] ?? "";

    $valor = str_replace(",", ".", $valor);
    $valor = (float) $valor;


    // VALIDAR VALOR
    if ($idPessoa == "" || $valor <= 0) {

        $mensagem = "Preencha todos os campos corretamente.";
        $tipoMensagem = "danger";

    } else {


        // ========================================
        // DEPOSITAR
        // ========================================

        if ($tipo === "depositar") {

            $sql = "INSERT INTO movimentacao
                    (idPessoa, Credito, Debito, Observacao)
                    VALUES
                    (:idPessoa, :credito, 0, :observacao)";

            $stmt = $conexao->prepare($sql);

            $stmt->bindValue(":idPessoa", $idPessoa);
            $stmt->bindValue(":credito", $valor);
            $stmt->bindValue(":observacao", $observacao);

            if ($stmt->execute()) {

                $mensagem = "Depósito realizado com sucesso!";
                $tipoMensagem = "success";

            } else {

                $mensagem = "Erro ao realizar depósito.";
                $tipoMensagem = "danger";
            }
        }


        // ========================================
        // SACAR
        // ========================================

        elseif ($tipo === "sacar") {


            // CONSULTAR SALDO
            $sql = "SELECT
                        COALESCE(SUM(Credito), 0) -
                        COALESCE(SUM(Debito), 0) AS saldo
                    FROM movimentacao
                    WHERE idPessoa = :idPessoa";

            $stmt = $conexao->prepare($sql);

            $stmt->bindValue(":idPessoa", $idPessoa);

            $stmt->execute();

            $resultado = $stmt->fetch();

            $saldo = (float) $resultado["saldo"];


            // VERIFICAR SALDO
            if ($valor > $saldo) {

                $mensagem = "Saldo insuficiente! Saldo atual: R$ "
                    . number_format($saldo, 2, ",", ".");

                $tipoMensagem = "danger";

            } else {


                // REALIZAR SAQUE
                $sql = "INSERT INTO movimentacao
                        (idPessoa, Credito, Debito, Observacao)
                        VALUES
                        (:idPessoa, 0, :debito, :observacao)";

                $stmt = $conexao->prepare($sql);

                $stmt->bindValue(":idPessoa", $idPessoa);
                $stmt->bindValue(":debito", $valor);
                $stmt->bindValue(":observacao", $observacao);

                if ($stmt->execute()) {

                    $mensagem = "Saque realizado com sucesso!";
                    $tipoMensagem = "success";

                } else {

                    $mensagem = "Erro ao realizar saque.";
                    $tipoMensagem = "danger";
                }
            }
        }


        // ========================================
        // TRANSFERIR
        // ========================================

        elseif ($tipo === "transferir") {

            $idDestino = $_POST["idDestino"] ?? "";


            if ($idDestino == "") {

                $mensagem = "Selecione a pessoa que receberá a transferência.";
                $tipoMensagem = "danger";

            } elseif ($idPessoa == $idDestino) {

                $mensagem = "A pessoa de origem e destino não podem ser iguais.";
                $tipoMensagem = "danger";

            } else {


                // CONSULTAR SALDO DA PESSOA DE ORIGEM
                $sql = "SELECT
                            COALESCE(SUM(Credito), 0) -
                            COALESCE(SUM(Debito), 0) AS saldo
                        FROM movimentacao
                        WHERE idPessoa = :idPessoa";

                $stmt = $conexao->prepare($sql);

                $stmt->bindValue(":idPessoa", $idPessoa);

                $stmt->execute();

                $resultado = $stmt->fetch();

                $saldo = (float) $resultado["saldo"];


                // VERIFICAR SALDO
                if ($valor > $saldo) {

                    $mensagem = "Saldo insuficiente! Saldo atual: R$ "
                        . number_format($saldo, 2, ",", ".");

                    $tipoMensagem = "danger";

                } else {


                    // INICIAR TRANSAÇÃO
                    try {

                        $conexao->beginTransaction();


                        // DÉBITO DA PESSOA DE ORIGEM
                        $sql = "INSERT INTO movimentacao
                                (idPessoa, Credito, Debito, Observacao)
                                VALUES
                                (:idPessoa, 0, :valor, :observacao)";

                        $stmt = $conexao->prepare($sql);

                        $stmt->bindValue(":idPessoa", $idPessoa);
                        $stmt->bindValue(":valor", $valor);
                        $stmt->bindValue(
                            ":observacao",
                            $observacao . " - Transferência enviada"
                        );

                        $stmt->execute();


                        // CRÉDITO DA PESSOA DE DESTINO
                        $sql = "INSERT INTO movimentacao
                                (idPessoa, Credito, Debito, Observacao)
                                VALUES
                                (:idPessoa, :valor, 0, :observacao)";

                        $stmt = $conexao->prepare($sql);

                        $stmt->bindValue(":idPessoa", $idDestino);
                        $stmt->bindValue(":valor", $valor);
                        $stmt->bindValue(
                            ":observacao",
                            $observacao . " - Transferência recebida"
                        );

                        $stmt->execute();


                        // CONFIRMAR
                        $conexao->commit();


                        $mensagem = "Transferência realizada com sucesso!";
                        $tipoMensagem = "success";

                    } catch (Exception $e) {

                        $conexao->rollBack();

                        $mensagem = "Erro ao realizar transferência.";
                        $tipoMensagem = "danger";
                    }
                }
            }
        }
    }
}


ob_start();

?>

<div class="container mt-4">

    <h1 class="mb-4">Nova Movimentação</h1>


    <?php if ($mensagem != ""): ?>

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
                onchange="mostrarTransferencia()"
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


        <!-- PESSOA DE ORIGEM -->

        <div class="mb-3">

            <label class="form-label">
                Pessoa
            </label>

            <select
                name="idPessoa"
                class="form-select"
                required
            >

                <option value="">
                    Selecione uma pessoa
                </option>

                <?php foreach ($pessoas as $pessoa): ?>

                    <option value="<?= $pessoa['id'] ?>">

                        <?= htmlspecialchars($pessoa['nome']) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <!-- PESSOA DESTINO -->

        <div
            class="mb-3"
            id="campoDestino"
            style="display: none;"
        >

            <label class="form-label">
                Pessoa que receberá
            </label>

            <select
                name="idDestino"
                class="form-select"
            >

                <option value="">
                    Selecione uma pessoa
                </option>

                <?php foreach ($pessoas as $pessoa): ?>

                    <option value="<?= $pessoa['id'] ?>">

                        <?= htmlspecialchars($pessoa['nome']) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <!-- VALOR -->

        <div class="mb-3">

            <label class="form-label">
                Valor
            </label>

            <input
                type="number"
                name="valor"
                class="form-control"
                step="0.01"
                min="0.01"
                placeholder="0,00"
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
                maxlength="255"
                placeholder="Digite uma observação"
            ></textarea>

        </div>


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

function mostrarTransferencia() {

    let tipo = document.getElementById("tipo").value;

    let campoDestino = document.getElementById("campoDestino");

    if (tipo === "transferir") {

        campoDestino.style.display = "block";

    } else {

        campoDestino.style.display = "none";

    }

}

</script>

<?php

$content = ob_get_clean();

require __DIR__ . '/layout.php';