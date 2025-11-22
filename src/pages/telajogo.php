<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require '../back/autentica.php';
require '../back/DAO/partidaDAO.php';

verificar_autenticacao();

$jogador_id = $_SESSION['user_id'] ?? 0;
$username = $_SESSION['username'] ?? 'Jogador Desconhecido';

$historico_partidas = [];
if ($jogador_id > 0) {
    $historico_partidas = buscarUltimasPartidas($jogador_id, 5);
}

function formatarTempoJogo($segundos)
{
    $total_segundos = floor($segundos);
    $min = floor($total_segundos / 60);
    $sec = $total_segundos % 60;
    $decimal = round($segundos - $total_segundos, 2) * 100;

    if ($decimal > 0) {
        return sprintf('%02d:%02d.%02d', $min, $sec, $decimal);
    }
    return sprintf('%02d:%02d', $min, $sec);
}

function formatarDataHora($data_db)
{
    try {
        $date_time = new DateTime($data_db);
        return $date_time->format('d/m/Y H:i');
    } catch (Exception $e) {
        return 'Data Inválida';
    }
}

?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogo da Memória - Memóremon</title>
    <link rel="icon" href="../img/pokebola.png" type="image/png">
    <link rel="stylesheet" href="../css/global2.css">
    <link rel="stylesheet" href="../css/telajogo.css">
</head>

<body>

    <?php require '../components/header.php'; ?>
    <?php include '../back/notificacaoMensagem.php'; ?>

    <main>
        <div class="container">
            <h1 class="titulo-pagina">Jogo da Memória</h1>

            <div class="selecao-modo">
                <h2>Escolha o modo de jogo:</h2>
                <div class="botoes-modo">
                    <button class="botao-modo ativo">Clássico</button>
                    <button class="botao-modo">Contra o Tempo</button>
                </div>
                <h2>Escolha o tamanho do tabuleiro:</h2>
                <div class="botoes-tamanho">
                    <button class="botao-tamanho" data-size="2">2x2</button>
                    <button class="botao-tamanho ativo" data-size="4">4x4</button>
                    <button class="botao-tamanho" data-size="6">6x6</button>
                    <button class="botao-tamanho" data-size="8">8x8</button>
                </div>
            </div>

            <div class="container-jogo">
                <div class="info-jogo">
                    <div class="card-info">
                        <h3>Informações da Partida</h3>
                        <div class="item-info">
                            <span class="rotulo">Tempo:</span>
                            <span class="valor" id="tempo-valor">00:00</span>
                        </div>
                        <div class="item-info">
                            <span class="rotulo">Jogadas:</span>
                            <span class="valor" id="jogadas-valor">0</span>
                        </div>
                        <div class="item-info">
                            <span class="rotulo">Tabuleiro:</span>
                            <span class="valor" id="tabuleiro-valor">3x3</span>
                        </div>
                        <div class="item-info">
                            <span class="rotulo">Modalidade:</span>
                            <span class="valor" id="modalidade-valor">Normal</span>
                        </div>
                        <div class="item-info">
                            <span class="rotulo">Tempo Restante:</span>
                            <span class="valor">-</span>
                        </div>
                    </div>

                    <div class="modo-trapaca">
                        <h3>Modo Trapaça</h3>
                        <div class="controles-trapaca">
                            <div class="botoes-trapaca">
                                <button class="botao-trapaca botao-ativar">Ativar</button>
                                <button class="botao-trapaca botao-desativar">Desativar</button>
                            </div>
                            <div class="status-trapaca">
                                <span class="texto-status">Desativado</span>
                            </div>
                        </div>
                    </div>

                    <div class="controles-jogo">
                        <button class="botao-controle botao-iniciar">Iniciar Partida</button>
                        <button class="botao-controle botao-reiniciar">Nova Partida</button>
                        <a href="./telajogo.php" class="botao-controle botao-voltar">Desistir</a>
                    </div>
                </div>

                <div class="tabuleiro-jogo">
                    <img src="../img/tabuleiro.jpg" alt="Tabuleiro do Jogo da Memória" class="imagem-tabuleiro">
                    <p class="nota-tabuleiro">Cartas meramente ilustrativas</p>
                </div>
            </div>

            <div class="secao-historico">
                <h2>Histórico de Partidas Recentes</h2>

                <div class="tabela-historico">
                    <div class="cabecalho-tabela">
                        <span>Data/Hora</span>
                        <span>Jogador</span>
                        <span>Tabuleiro</span>
                        <span>Modalidade</span>
                        <span>Tempo</span>
                        <span>Jogadas</span>
                        <span>Resultado</span>
                    </div>

                    <?php if (empty($historico_partidas)): ?>
                        <div class="linha-tabela linha-vazia">
                            <span style="grid-column: 1 / span 7; text-align: center; padding: 10px;">
                                Nenhuma partida recente encontrada. Jogue para registrar seu histórico!
                            </span>
                        </div>
                    <?php else: ?>
                        <?php foreach ($historico_partidas as $partida): ?>
                            <?php
                            $resultado_formatado = htmlspecialchars($partida['resultado']);
                            $classe_resultado = (strtolower($resultado_formatado) === 'vitoria') ? 'vitoria' : 'derrota';
                            ?>
                            <div class="linha-tabela">
                                <span><?= formatarDataHora($partida['data_partida']) ?></span>
                                <span><?= htmlspecialchars($username) ?></span>
                                <span><?= htmlspecialchars($partida['dimensao']) ?></span>
                                <span><?= htmlspecialchars($partida['modalidade']) ?></span>
                                <span><?= formatarTempoJogo($partida['tempo_gasto_seg']) ?></span>
                                <span><?= htmlspecialchars($partida['num_jogadas']) ?></span>
                                <span class="resultado <?= $classe_resultado ?>">
                                    <?= $resultado_formatado ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </main>

    <?php require '../components/footer.php'; ?>

    <script src="../js/telajogo.js"></script>
</body>

</html>