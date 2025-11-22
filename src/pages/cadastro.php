<?php
session_start();

$dados_anteriores = array();
$termos_aceitos = false;

if (isset($_SESSION['notificacao']) && ($_SESSION['notificacao']['tipo'] === 'erro')) {
    $dados_anteriores = $_SESSION['notificacao']['dados_anteriores'] ?? array();
    $termos_aceitos = isset($dados_anteriores['termos']);
}

$nome = $dados_anteriores['nome'] ?? '';
$cpf = $dados_anteriores['cpf'] ?? '';
$telefone = $dados_anteriores['telefone'] ?? '';
$email = $dados_anteriores['email'] ?? '';
$username = $dados_anteriores['username'] ?? '';
$data_nascimento = $dados_anteriores['data-nascimento'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Memóremon</title>
    <link rel="icon" href="../img/pokebola.png" type="image/png">
    <link rel="stylesheet" href="../css/global2.css">
    <link rel="stylesheet" href="../css/cadastro.css">
</head>

<body>

    <?php require '../components/header.php'; ?>
    <?php include '../components/notificacaoMensagem.php'; ?>

    <main>
        <section class="secao-cadastro">
            <div class="container">
                <div class="container-formulario">
                    <div class="cabecalho-formulario">
                        <h2>Criar Nova Conta</h2>
                        <p>Junte-se ao Memóremon e comece a jogar!</p>
                    </div>

                    <form class="formulario-cadastro" action="../back/processaCadastro.php" method="POST">
                        <div class="grupo-formulario">
                            <label for="nome">Nome Completo</label>
                            <input type="text" id="nome" name="nome" required
                                value="<?php echo htmlspecialchars($nome); ?>">
                        </div>

                        <div class="grupo-formulario">
                            <label for="cpf">CPF</label>
                            <input type="text" id="cpf" name="cpf" required
                                value="<?php echo htmlspecialchars($cpf); ?>">
                        </div>

                        <div class="grupo-formulario">
                            <label for="telefone">Telefone</label>
                            <input type="text" id="telefone" name="telefone" required
                                value="<?php echo htmlspecialchars($telefone); ?>">
                        </div>

                        <div class="grupo-formulario">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" required
                                value="<?php echo htmlspecialchars($email); ?>">
                        </div>

                        <div class="grupo-formulario">
                            <label for="username">Nome de Usuário</label>
                            <input type="text" id="username" name="username" required
                                value="<?php echo htmlspecialchars($username); ?>">
                        </div>

                        <div class="grupo-formulario">
                            <label for="senha">Senha</label>
                            <input type="password" id="senha" name="senha" required>
                        </div>

                        <div class="grupo-formulario">
                            <label for="confirmar-senha">Confirmar Senha</label>
                            <input type="password" id="confirmar-senha" name="confirmar-senha" required>
                        </div>

                        <div class="grupo-formulario">
                            <label for="data-nascimento">Data de Nascimento</label>
                            <input type="date" id="data-nascimento" name="data-nascimento" required
                                value="<?php echo htmlspecialchars($data_nascimento); ?>">
                        </div>

                        <div class="grupo-formulario grupo-checkbox">
                            <input type="checkbox" id="termos" name="termos" <?php echo $termos_aceitos ? 'checked' : ''; ?> required>
                            <label for="termos">Aceito os <a href="#">termos e condições</a></label>
                        </div>

                        <button type="submit" class="botao-cadastrar">Criar Conta</button>

                        <div class="link-login">
                            <p>Já tem uma conta? <a href="./login.php">Faça login aqui</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <?php require '../components/footer.php'; ?>

</body>

</html>