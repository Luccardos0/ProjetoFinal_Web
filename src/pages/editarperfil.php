<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require '../back/autentica.php';
require '../back/DAO/usuarioDAO.php';

verificar_autenticacao();
$usuario_id = $_SESSION['user_id'] ?? 0;

$dados_usuario = [];
if ($usuario_id > 0) {
    $dados_usuario = buscarUsuarioId($usuario_id);
}

$mensagem_sessao = $_SESSION['login_mensagem'] ?? [];
$dados_anteriores = $mensagem_sessao['dados_anteriores'] ?? [];

if (empty($dados_usuario)) {
    $dados_usuario = [
        'username' => '',
        'email' => '',
        'nome_completo' => '',
        'data_nascimento' => '',
        'cpf' => '',
        'telefone' => ''
    ];
}

if (!empty($dados_anteriores)) {
    $dados_usuario = array_merge($dados_usuario, $dados_anteriores);
}

if (!empty($mensagem_sessao)) {
    unset($_SESSION['login_mensagem']);
}

function formataValor($key, $default = '', $dados)
{
    if ($key === 'data_nascimento' && isset($dados[$key])) {
        return date('Y-m-d', strtotime($dados[$key]));
    }
    return htmlspecialchars($dados[$key] ?? $default);
}

?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Memóremon</title>
    <link rel="icon" href="../img/pokebola.png" type="image/png">
    <link rel="stylesheet" href="../css/global2.css">
    <link rel="stylesheet" href="../css/editarperfil.css">
</head>

<body>

    <?php require '../components/header.php'; ?>
    <?php include '../components/notificacaoMensagem.php'; ?>

    <main>
        <section class="secao-editar">
            <div class="container">
                <div class="container-formulario">
                    <div class="cabecalho-formulario">
                        <h2>Editar Informações Pessoais</h2>
                        <p>Atualize suas informações abaixo. Campos marcados com * são obrigatórios.</p>
                    </div>

                    <form class="formulario-editar" action="../back/updatePerfil.php" method="POST">
                        <div class="linha-formulario">
                            <div class="grupo-formulario">
                                <label for="username">Username *</label>
                                <input type="text" id="username" name="username" class="campo-somente-leitura"
                                    value="<?= formataValor('username', '', $dados_usuario) ?>" readonly>
                            </div>
                            <div class="grupo-formulario">
                                <label for="email">E-mail *</label>
                                <input type="email" id="email" name="email"
                                    value="<?= formataValor('email', '', $dados_usuario) ?>" required>
                            </div>
                        </div>

                        <div class="linha-formulario">
                            <div class="grupo-formulario">
                                <label for="nome">Nome Completo *</label>
                                <input type="text" id="nome" name="nome"
                                    value="<?= formataValor('nome_completo', '', $dados_usuario) ?>" required>
                            </div>
                            <div class="grupo-formulario">
                                <label for="dataNascimento">Data de Nascimento *</label>
                                <input type="date" id="dataNascimento" name="dataNascimento"
                                    class="campo-somente-leitura"
                                    value="<?= formataValor('data_nascimento', '', $dados_usuario) ?>" readonly>
                            </div>
                        </div>

                        <div class="linha-formulario">
                            <div class="grupo-formulario">
                                <label for="cpf">CPF *</label>
                                <input type="text" id="cpf" name="cpf" class="campo-somente-leitura"
                                    value="<?= formataValor('cpf', '', $dados_usuario) ?>" readonly>
                            </div>
                            <div class="grupo-formulario">
                                <label for="telefone">Telefone</label>
                                <input type="tel" id="telefone" name="telefone"
                                    value="<?= formataValor('telefone', '', $dados_usuario) ?>">
                            </div>
                        </div>

                        <div class="grupo-formulario largura-completa">
                            <label for="senha">Nova Senha (deixe em branco para manter a atual)</label>
                            <input type="password" id="senha" name="senha" placeholder="Digite uma nova senha">
                        </div>

                        <div class="grupo-formulario largura-completa">
                            <label for="confirmarSenha">Confirmar Nova Senha</label>
                            <input type="password" id="confirmarSenha" name="confirmarSenha"
                                placeholder="Confirme a nova senha">
                        </div>

                        <div class="acoes-formulario">
                            <button type="submit" class="botao-primario">Salvar Alterações</button>
                            <a href="index2.html" class="botao-secundario">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <?php require '../components/footer.php'; ?>
</body>

</html>