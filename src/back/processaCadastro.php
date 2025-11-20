<?php

session_start();

require 'config.php';

// Array para armazenar mensagens de erro
$errors = [];

function validarCpf($cpf)
{
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
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirma_senha = $_POST['confirmar-senha'] ?? '';
    $data_nascimento = $_POST['data-nascimento'] ?? '';
    $termos_aceitos = isset($_POST['termos']);

    // Todos os dados foram preenchidos corretamente?
    if (empty($nome))
        $errors[] = "O campo Nome Completo é obrigatório.";
    if (empty($cpf))
        $errors[] = "O campo CPF é obrigatório.";
    if (empty($telefone))
        $errors[] = "O campo Telefone é obrigatório.";
    if (empty($email))
        $errors[] = "O campo E-mail é obrigatório.";
    if (empty($usuario))
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

    // Validando e *Salvando cpf apenas com números
    if(!validarCpf($cpf)){
        $errors[] = "O CPF fornecido é inválido.";
    }

    // Validar senha
    if ($senha !== $confirma_senha) {
        $errors[] = "As Senhas não são as mesmas.";
    }

    if (strlen($senha) < min_senha) {
        $errors[] = "A senha deve ter no mínimo " . min_senha . " caracteres.";
    }

    // Inserindo no Banco se estiver tudo certinho
    if (empty($errors)) {

        try {
            $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Fazendo o  hash da senha por causa da segurança
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            //Salvando só os n do cpf
            $cpf_limpo = preg_replace('/[^0-9]/', '', $cpf);

            //inserindo no banco
            $sql = "INSERT INTO jogadores (nome_completo, cpf, telefone, email, nome_usuario, senha_hash, data_nascimento, termos_aceitos) 
                    VALUES (:nome, :cpf, :telefone, :email, :usuario, :senha_hash, :data_nascimento, :termos_aceitos)";

            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':cpf', $cpf_limpo);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':senha_hash', $senha_hash);
            $stmt->bindParam(':data_nascimento', $data_nascimento);
            $stmt->bindParam(':termos_aceitos', $termos_aceitos, PDO::PARAM_INT); // PDO::PARAM_INT para booleans/inteiros

            $stmt->execute();

            $_SESSION['cadastro_mensagem'] = [
                'tipo' => 'sucesso',
                'texto' => "✅ Bem-vindo(a) ao Memóremon, **{$nome}**! Seu cadastro foi concluído. Para acessar o jogo é necessário fazer o login."
            ];

            header('Location: ../pages/login.php');
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = "Aconteceu um problema no seu cadastro: CPF, E-mail ou Nome de Usuário já está sendo utilizado. Por favor, verifique os dados.";
            } else {
                $errors[] = "Erro no Servidor -> (Detalhe: " . $e->getMessage() . ")";
            }
        }
    }
} else {
    header('Location: ../pages/cadastro.php');
    $errors[] = "Acesso inválido.";
    exit;
}

if (!empty($errors)) {
    $_SESSION['cadastro_mensagem'] = [
        'tipo' => 'erro',
        'erros' => $errors,
        'dados_anteriores' => $_POST // salvo os dados preenchidos antes
    ];
}

header('Location: ../pages/cadastro.php');
exit;

?>