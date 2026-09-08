
<?php

require_once "../src/Config/Conexao.php";

use App\Config\Conexao;

$conexao = Conexao::conectar();

$nome = $_POST["nome"];
$telefone = $_POST["telefone"];
$cpf = $_POST["cpf"];
$endereco = $_POST["endereco"];

// Verifica se o CPF já existe
$sql = "SELECT id FROM pessoas WHERE cpf = :cpf";

$stmt = $conexao->prepare($sql);
$stmt->bindValue(":cpf", $cpf);
$stmt->execute();

if ($stmt->fetch()) {
    echo "Erro: este CPF já está cadastrado!";
    exit;
}

// Cadastra a pessoa
$sql = "INSERT INTO pessoas 
        (nome, telefone, cpf, endereco)
        VALUES 
        (:nome, :telefone, :cpf, :endereco)";

$stmt = $conexao->prepare($sql);

$stmt->bindValue(":nome", $nome);
$stmt->bindValue(":telefone", $telefone);
$stmt->bindValue(":cpf", $cpf);
$stmt->bindValue(":endereco", $endereco);

if ($stmt->execute()) {
    echo "Pessoa cadastrada com sucesso!";
} else {
    echo "Erro ao cadastrar pessoa.";
}