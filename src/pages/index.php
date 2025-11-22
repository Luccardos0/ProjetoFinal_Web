<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memóremon</title>
    <link rel="icon" href="../img/pokebola.png" type="image/png">
    <link rel="stylesheet" href="../css/global2.css">
    <link rel="stylesheet" href="../css/index2.css">
</head>

<body>

    <?php require '../components/header.php'; ?>
    <?php include '../components/notificacaoMensagem.php'; ?>
    
    <main>
        <section class="secao-principal">
            <div class="slides-principal">
                <div class="slide slide-1"></div>
                <div class="slide slide-2"></div>
                <div class="slide slide-3"></div>
                <div class="slide slide-4"></div>
            </div>
            <div class="conteudo-principal">
                <h2>Bem-vindo ao Jogo <img src="./src/img/logo2.png" alt=""
                        style="height: 3em; vertical-align: middle;"></h2>

                <p>Memoremon é um jogo da memória para você se divertir com os seu amigos,
                    ele traz os mostrinhos mais queridos do público para por a prova se você
                    realmente conhece os pokémons e é bom de memória!
                </p>
                <div class="botoes-principal">
                    <a href="../pages/cadastro.php" class="botao-principal">Criar conta</a>
                    <a href="../pages/login.php" class="botao-principal">Entrar</a>
                </div>
            </div>
        </section>
    </main>

    <?php require '../components/footer.php'; ?>

</body>

</html>