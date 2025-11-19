<?php

// Recebendo dados
$nome = htmlspecialchars($_POST['nome'] ?? '');
$cpf = htmlspecialchars($_POST['cpf'] ?? '');
$email = htmlspecialchars($_POST['email'] ?? '');
$usuario = htmlspecialchars($_POST['usuario'] ?? '');
$data_nascimento = htmlspecialchars($_POST['data-nascimento'] ?? '');
$senha = $_POST['senha'] ?? ''; // Não aplicar htmlspecialchars na senha antes de hashear

// Validação básica (ex: se a senha e a confirmação são iguais - omitida por brevidade)

## 2. Abordagens de Persistência de Dados
// Escolha uma das abordagens abaixo:

// ----------------------------------------------------------------------
// ABORDAGEM A: Persistência em Banco de Dados (PDO - Unidade 17) 💾
// ----------------------------------------------------------------------
/*
try {
    // 1. Conectar ao Banco de Dados (substitua os valores)
    $sname = "localhost";
    $uname = "root";
    $pwd = "";
    $dbname = "meu_banco"; // Substitua pelo nome do seu BD
    
    // Crie uma nova instância PDO
    $conn = new PDO("mysql:host=$sname;dbname=$dbname", $uname, $pwd);
    // Define o modo de erro para EXCEPTION
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 2. Preparar e executar a inserção de dados
    // Sempre use instruções preparadas para evitar ataques de SQL Injection!
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, cpf, email, usuario, senha, data_nasc) VALUES (:nome, :cpf, :email, :usuario, :senha_hash, :data_nasc)");
    
    // Hash da senha antes de armazenar
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT); 
    
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':senha_hash', $senha_hash);
    $stmt->bindParam(':data_nasc', $data_nascimento);

    $stmt->execute();
    
    echo "Cadastro realizado com sucesso via Banco de Dados! [cite: 597, 600, 690]";
    
} catch(PDOException $e) {
    echo "Erro de conexão ou inserção: " . $e->getMessage() . " [cite: 601, 606]";
}

$conn = null; // Fechar a conexão [cite: 618]
*/