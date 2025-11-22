<?php
session_start();

$dados_anteriores = [];
if (isset($_SESSION['notificacao']) && ($_SESSION['notificacao']['tipo'] === 'erro')) {
    $dados_anteriores = $_SESSION['notificacao']['dados_anteriores'] ?? [];
}

$username = $dados_anteriores['username'] ?? '';

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
    <?php include '../components/notificacaoMensagem.php'; ?>

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
                            <label for="username">Nome de Usuário</label>
                            <input type="text" id="username" name="username" required
                                value="<?php echo htmlspecialchars($username); ?>">
                        </div>

                        <div class="grupo-formulario">
                            <label for="senha">Senha</label>
                            <input type="password" id="senha" name="senha" required>
                        </div>

                        <button type="submit" class="botao-entrar">Entrar</button>

                        <div class="link-cadastro">
                            <p>Não tem uma conta? <a href="../pages/cadastro.php">Cadastre-se aqui</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <?php require '../components/footer.php'; ?>

</body>

</html>