<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'autentica.php'; 
require 'config.php';   

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('Location: ../pages/editarperfil.php');
    exit;
}

verificar_autenticacao();
$usuario_id = $_SESSION['user_id'] ?? 0;

if ($usuario_id <= 0) {
    header('Location: processaLogout.php');
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
        $errors[] = "A nova senha e a confirmação de senha não coincidem.";
    } else {
        $dados_para_update['senha'] = password_hash($senha, PASSWORD_DEFAULT);
    }
}

$sucesso = false;
$destino_sucesso = '../pages/editarperfil.php';

if (empty($errors) && !empty($dados_para_update)) {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $campos_sql = [];
        $parametros = ['id_usuario' => $usuario_id];
        
        foreach ($dados_para_update as $campo => $valor) {
            if (!empty($valor) || $campo === 'senha') { 
                $campos_sql[] = "`{$campo}` = :{$campo}";
                $parametros[":{$campo}"] = $valor;
            }
        }
        
        $sql = "UPDATE jogadores SET " . implode(', ', $campos_sql) . " WHERE id = :id_usuario";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($parametros);

        $sucesso = true;

    } catch (PDOException $e) {
        $_SESSION['notificacao'] = [
            'tipo' => 'erro',
            'mensagem' => "Erro no servidor ao salvar as alterações. Tente novamente."
        ];
        $errors[] = "Falha no banco de dados. " . $e->getMessage(); 
        $sucesso = false;
    }
}

if ($sucesso) {
    $_SESSION['notificacao'] = [
        'tipo' => 'sucesso',
        'mensagem' => "Perfil atualizado com sucesso! As alterações já estão visíveis abaixo."
    ];
    header('Location: ' . $destino_sucesso); 
    exit;

} elseif (empty($errors) && empty($dados_para_update)) {
    $_SESSION['notificacao'] = [
        'tipo' => 'aviso',
        'mensagem' => "Nenhuma alteração detectada para salvar."
    ];
    header('Location: ' . $destino_sucesso); 
    exit;

} else {
    if (!empty($errors)) {
        $_SESSION['login_mensagem'] = [ 
            'tipo' => 'erro',
            'erros' => $errors,
            'dados_anteriores' => [
                'email' => $email, 
                'nome_completo' => $nome,
                'telefone' => $telefone
            ] 
        ];
    }

    header('Location: ../pages/editarperfil.php');
    exit;
}
?>