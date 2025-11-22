<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require '../back/config.php';
require '../back/DAO/usuarioDAO.php';
require '../back/setNotificacao.php';

$errors = array();

function validarCpf($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) != 11)
        return false;
    if (preg_match('/(\d)\1{10}/', $cpf))
        return false;
    return true;
}

// Precisa ser POST para funfar
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST['nome'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirma_senha = $_POST['confirmar-senha'] ?? '';
    $data_nascimento = $_POST['data-nascimento'] ?? '';
    $termos_aceitos = isset($_POST['termos']);

    // Validações básicas
    if (empty($nome))
        $errors[] = "O campo Nome Completo é obrigatório.";
    if (empty($cpf))
        $errors[] = "O campo CPF é obrigatório.";
    if (empty($telefone))
        $errors[] = "O campo Telefone é obrigatório.";
    if (empty($email))
        $errors[] = "O campo E-mail é obrigatório.";
    if (empty($username))
        $errors[] = "O campo Nome de Usuário é obrigatório.";
    if (empty($senha))
        $errors[] = "O campo Senha é obrigatório.";
    if (empty($confirma_senha))
        $errors[] = "O campo Confirmar Senha é obrigatório.";
    if (empty($data_nascimento))
        $errors[] = "O campo Data de Nascimento é obrigatório.";
    if (!$termos_aceitos)
        $errors[] = "Você deve aceitar os termos e condições.";

    // Filtro de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "O E-mail fornecido é inválido.";
    }

    // Validar CPF
    if (!validarCpf($cpf)) {
        $errors[] = "O CPF fornecido é inválido.";
    }

    // Validar senha
    if ($senha !== $confirma_senha) {
        $errors[] = "As senhas não coincidem.";
    }

    if (strlen($senha) < min_senha) {
        $errors[] = "A senha deve ter no mínimo " . min_senha . " caracteres.";
    }

    // Se não há erros, tentar cadastrar
    if (empty($errors)) {
        try {
            $usuarioDAO = new UsuarioDAO();
            
            $dadosUsuario = array(
                'nome_completo' => $nome,
                'cpf' => $cpf,
                'telefone' => $telefone,
                'email' => $email,
                'username' => $username,
                'senha' => $senha,
                'data_nascimento' => $data_nascimento,
                'termos_aceitos' => $termos_aceitos
            );

            $resultado = $usuarioDAO->criarUsuario($dadosUsuario);

            // Deu bom
            setNotificacaoSucesso("Bem-vindo(a) ao Memóremon, {$nome}! Seu cadastro foi concluído. Para acessar o jogo é necessário fazer o login.");

            header('Location: ../pages/login.php');
            exit;

        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
} else {
    // Acesso inválido - o método não é POST
    setNotificacaoErro(array("Acesso inválido."));
    header('Location: ../pages/cadastro.php');
    exit;
}

// Se chegou aqui, deu algum b.o
if (!empty($errors)) {
    setNotificacaoErro($errors, $_POST);
}

header('Location: ../pages/cadastro.php');
exit;