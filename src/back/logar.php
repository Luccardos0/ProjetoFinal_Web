<?php

session_start();

require './config.php';

$errors = [];

// Se a req nn for post ele nn faz nada
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('Location: ../pages/login.php');
    exit;
}

$usuario = trim($_POST['usuario'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($usuario) || empty($senha)) {
    $errors[] = "Nome de usuário e senha são obrigatórios.";
}

if(empty($errors)) {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT id, nome_usuario, senha_hash FROM jogadores WHERE nome_usuario = :usuario LIMIT 1";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();

        $jogador = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($jogador && password_verify($senha, $jogador['senha_hash'])) {
            
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $jogador['id'];
            $_SESSION['username'] = $jogador['nome_usuario'];
            
            header('Location: ../pages/telajogo.php');
            exit;
        } else {
            $errors[] = "O nome de usuário e/ou a senha podem não estar corretos.";
        }

    } catch (PDOException $e) {
        $errors[] = "Problema ao logar -> (Detalhe: " . $e->getMessage() . ")";
    }
}

if (!empty($errors)) {
    $_SESSION['login_mensagem'] = [
        'tipo' => 'erro',
        'erros' => $errors,
        'dados_anteriores' => ['usuario' => $usuario] 
    ];
}

//Voltando para tela de login se der pau
header('Location: ../pages/login.php');
exit;

?>