<?php

require_once "../src/Config/Conexao.php";
require_once "../src/Model/pessoa.php";
require_once "../src/DAO/pessoaDAO.php";

use App\Model\Pessoa;
use App\DAO\PessoaDAO;

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: pessoa-cadastrar.php");
    exit;
}

// Recebe os dados
$nome = $_POST["nome"] ?? "";
$telefone = $_POST["telefone"] ?? "";
$cpf = $_POST["cpf"] ?? "";
$endereco = $_POST["endereco"] ?? "";

// Cria o objeto Pessoa
$pessoa = new Pessoa();

$pessoa->setNome($nome);
$pessoa->setTelefone($telefone);
$pessoa->setCpf($cpf);
$pessoa->setEndereco($endereco);

// Cria o DAO
$pessoaDAO = new PessoaDAO();

// Cadastra
if ($pessoaDAO->cadastrar($pessoa)) {

    echo "
        <script>
            alert('Pessoa cadastrada com sucesso!');
            window.location.href = 'listar.php';
        </script>
    ";

} else {

    echo "
        <script>
            alert('Erro: este CPF já está cadastrado!');
            window.location.href = 'pessoa-cadastrar.php';
        </script>
    ";
}