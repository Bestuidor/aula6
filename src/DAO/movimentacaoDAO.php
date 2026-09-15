<?php

namespace App\DAO;

use App\Config\Conexao;
use App\Model\Movimentacao;

class MovimentacaoDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    // DEPOSITAR
    public function depositar(Movimentacao $movimentacao)
    {
        $sql = "INSERT INTO movimentacao
                (idPessoa, Credito, Debito, Observacao)
                VALUES
                (:idPessoa, :credito, 0, :observacao)";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(":idPessoa", $movimentacao->getIdPessoa());
        $stmt->bindValue(":credito", $movimentacao->getCredito());
        $stmt->bindValue(":observacao", $movimentacao->getObservacao());

        return $stmt->execute();
    }


    // SACAR
    public function sacar(Movimentacao $movimentacao)
    {
        $saldo = $this->consultarSaldo(
            $movimentacao->getIdPessoa()
        );

        if ($movimentacao->getDebito() > $saldo) {
            return false;
        }

        $sql = "INSERT INTO movimentacao
                (idPessoa, Credito, Debito, Observacao)
                VALUES
                (:idPessoa, 0, :debito, :observacao)";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(":idPessoa", $movimentacao->getIdPessoa());
        $stmt->bindValue(":debito", $movimentacao->getDebito());
        $stmt->bindValue(":observacao", $movimentacao->getObservacao());

        return $stmt->execute();
    }


    // CONSULTAR SALDO
    public function consultarSaldo($idPessoa)
    {
        $sql = "SELECT
                    COALESCE(SUM(Credito), 0) -
                    COALESCE(SUM(Debito), 0) AS saldo
                FROM movimentacao
                WHERE idPessoa = :idPessoa";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(":idPessoa", $idPessoa);

        $stmt->execute();

        $resultado = $stmt->fetch();

        return $resultado['saldo'];
    }


    // LISTAR MOVIMENTAÇÕES
  public function listar()
{
    $sql = "SELECT 
                m.id,
                m.idPessoa,
                p.nome,
                m.Credito,
                m.Debito,
                m.DataOperacao,
                m.Observacao,

                (
                    SELECT 
                        COALESCE(SUM(m2.Credito), 0) -
                        COALESCE(SUM(m2.Debito), 0)
                    FROM movimentacao m2
                    WHERE m2.idPessoa = m.idPessoa
                ) AS saldo

            FROM movimentacao m

            INNER JOIN pessoas p
                ON p.id = m.idPessoa

            ORDER BY m.DataOperacao DESC";

    $stmt = $this->conexao->prepare($sql);

    $stmt->execute();

    return $stmt->fetchAll();
}
}