<?php
session_start();

$dados_anteriores = [];
if (isset($_SESSION['cadastro_mensagem']) && ($_SESSION['cadastro_mensagem']['tipo'] === 'erro')) {
    $dados_anteriores = $_SESSION['cadastro_mensagem']['dados_anteriores'] ?? [];
} elseif (isset($_SESSION['login_mensagem']) && ($_SESSION['login_mensagem']['tipo'] === 'erro')) {
    $dados_anteriores = $_SESSION['login_mensagem']['dados_anteriores'] ?? [];
}

$usuario = $dados_anteriores['usuario'] ?? '';

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Memóremon</title>
    <link rel="icon" href="../img/pokebola.png" type="image/png">
    <link rel="stylesheet" href="../css/global2.css">
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>

    <?php require '../components/header.php'; ?>
    <?php include '../back/notificacaoMensagem.php'; ?>

    <main>
        <section class="secao-login">
            <div class="container">
                <div class="conteiner-formulario">
                    <div class="cabecalho-formulario">
                        <h2>Entrar no Memóremon</h2>
                        <p>Digite suas credenciais para acessar sua conta</p>
                    </div>

                    <form class="formulario-login" action="../back/logar.php" method="POST">
                        <div class="grupo-formulario">
                            <label for="usuario">Nome de Usuário</label>
                            <input type="text" id="usuario" name="usuario" required value="<?php echo htmlspecialchars($usuario); ?>">
                        </div>

                        <div class="grupo-formulario">
                            <label for="senha">Senha</label>
                            <input type="password" id="senha" name="senha" required>
                        </div>

                        <div class="grupo-formulario grupo-checkbox">
                            <input type="checkbox" id="lembrar" name="lembrar">
                            <label for="lembrar">Lembrar-me</label>
                        </div>

                        <button type="submit" class="botao-entrar">Entrar</button>

                        <div class="link-cadastro">
                            <p>Não tem uma conta? <a href="../pages/cadastro.php">Cadastre-se aqui</a></p>
                        </div>

                        <div class="esqueci-senha">
                            <a href="#">Esqueci minha senha</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="conteudo-rodape">
                &copy; Faculdade de Tecnologia da Unicamp - Programação Web - 2025
            </div>
        </div>
    </footer>
</body>

</html>