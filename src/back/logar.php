<?php

session_start();

require './config.php';
require_once './setNotificacao.php';
require './DAO/usuarioDAO.php';

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

        $usuarioDAO = new UsuarioDAO();
        $jogador = $usuarioDAO->logar($username, $senha);

        if ($jogador) {

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