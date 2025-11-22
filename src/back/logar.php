<?php

session_start();

require './config.php';
require './setNotificacao.php';

$errors = [];

// Se a req nn for post ele nn faz nada
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    setNotificacaoErro(array("Acesso inválido. Use o formulário de login."));
    header('Location: ../pages/login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($username) || empty($senha)) {
    $errors[] = "Nome de usuário e senha são obrigatórios.";
}

if (empty($errors)) {
    try {
        $conn = new PDO("mysql:host=" . host . ";dbname=" . name, user, pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT id, username, senha FROM jogadores WHERE username = :username LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $jogador = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($jogador && password_verify($senha, $jogador['senha'])) {

            $_SESSION['logado'] = true;
            $_SESSION['user_id'] = $jogador['id'];
            $_SESSION['username'] = $jogador['username'];

            setNotificacaoSucesso("Bem-vindo(a) de volta, {$jogador['username']}!");

            header('Location: ../pages/telajogo.php');
            exit;
        } else {
            $errors[] = "O nome de usuário e/ou a senha podem não estar corretos.";
        }

    } catch (PDOException $e) {
        $errors[] = "Problema ao logar -> (Detalhe: Erro de banco de dados)";
    }
}

if (!empty($errors)) {
    setNotificacaoErro($errors, ['username' => $username]);
}

header('Location: ../pages/login.php');
exit;