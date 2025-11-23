<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'autentica.php';
require 'config.php';
require '../back/DAO/usuarioDAO.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    setNotificacaoErro(array("Acesso inválido."));
    header('Location: ../pages/editarperfil.php');
    exit;
}

verificar_autenticacao();

$usuario_id = $_SESSION['user_id'] ?? 0;

if ($usuario_id <= 0) {
    header('Location: processaLogout.php'); // Deu um belo de b.o
    exit;
}

$dados_para_update = [];
$errors = [];

$email = trim($_POST['email'] ?? '');
$nome = trim($_POST['nome'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmar_senha = $_POST['confirmarSenha'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "O e-mail fornecido não é válido.";
} else {
    $dados_para_update['email'] = $email;
}

if (empty($nome)) {
    $errors[] = "O nome completo é obrigatório.";
} else {
    $dados_para_update['nome_completo'] = $nome;
}

$dados_para_update['telefone'] = $telefone;

if (!empty($senha)) {
    if (strlen($senha) < min_senha) {
        $errors[] = "A nova senha deve ter no mínimo " . min_senha . " caracteres.";
    } elseif ($senha !== $confirmar_senha) {
        $errors[] = "A nova senha e a confirmação não são iguais.";
    } else {
        $dados_para_update['senha'] = password_hash($senha, PASSWORD_DEFAULT);
    }
}

if (!empty($errors)) {
    setNotificacaoErro($errors, [
        'email' => $email,
        'nome_completo' => $nome,
        'telefone' => $telefone
    ]);

    header('Location: ../pages/editarperfil.php');
    exit;
}

if (!empty($dados_para_update)) {
    try {
        $usuarioDAO = new UsuarioDAO();
        $usuarioDAO->atualizarUsuario($usuario_id, $dados_para_update);
        setNotificacaoSucesso("Perfil atualizado com sucesso!");
    } catch (Exception $e) {
        setNotificacaoErro(array("Erro ao atualizar o perfil. Erro: " . $e->getMessage()));

        header('Location: ../pages/editarperfil.php');
        exit;
    }
} else {
    setNotificacao('aviso', ['texto' => "Nenhuma alteração para realizar."]);
}

header('Location: ../pages/editarperfil.php');
exit;