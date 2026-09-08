<?php

namespace App\DAO;

use App\Config\Conexao;
use App\Model\Pessoa;

class PessoaDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    public function cadastrar(Pessoa $pessoa)
    {
        // Verifica se o CPF já existe
        $sql = "SELECT id FROM pessoas WHERE cpf = :cpf";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":cpf", $pessoa->getCpf());
        $stmt->execute();

        if ($stmt->fetch()) {
            return false;
        }

        // Cadastra a pessoa
        $sql = "INSERT INTO pessoas
                (nome, telefone, cpf, endereco)
                VALUES
                (:nome, :telefone, :cpf, :endereco)";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(":nome", $pessoa->getNome());
        $stmt->bindValue(":telefone", $pessoa->getTelefone());
        $stmt->bindValue(":cpf", $pessoa->getCpf());
        $stmt->bindValue(":endereco", $pessoa->getEndereco());

        return $stmt->execute();
    }
}