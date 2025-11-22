<?php
// Certifica-se de que a sessão está iniciada, o que é fundamental para usar $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require '../back/autentica.php';
require '../back/DAO/partidaDAO.php';

verificar_autenticacao();

// 1. CHAMAR A FUNÇÃO PARA OBTER O TOP 10 GLOBAL
$ranking_global = buscarRankingTop10();

$id = $_SESSION['user_id'] ?? 0;
$melhor_posicao_jogador = encontrarMelhorPosicao($id);
// 2. FUNÇÕES AUXILIARES DE FORMATAÇÃO (Para garantir que a página renderize corretamente)

/**
 * Formata segundos (DECIMAL) para o formato MM:SS.
 * Assumimos que o tempo_gasto_seg é um DECIMAL.
 */
function formatarTempoJogo($segundos)
{
    $total_segundos = floor($segundos);
    $min = floor($total_segundos / 60);
    $sec = $total_segundos % 60;

    // Inclui centésimos para maior precisão (opcional)
    $decimal = round($segundos - $total_segundos, 2) * 100;

    if ($decimal > 0) {
        return sprintf('%02d:%02d.%02d', $min, $sec, $decimal);
    }
    return sprintf('%02d:%02d', $min, $sec);
}

/**
 * Formata a data do banco (DATETIME) para DD/MM/AAAA.
 */
function formatarData($data_db)
{
    try {
        $date_time = new DateTime($data_db);
        return $date_time->format('d/m/Y');
    } catch (Exception $e) {
        return 'Data Inválida';
    }
}

/**
 * Adiciona o sufixo 'º' à posição.
 */
function formatarPosicao($posicao)
{
    return $posicao . 'º';
}

// Assumimos que o username do jogador logado está na sessão para o cartão lateral
$username_logado = $_SESSION['username'] ?? 'SeuUsername';

// NOTE: A lógica para 'Sua Melhor Posição' (15º) não foi implementada no DAO, 
// então usaremos dados estáticos/placeholder nessa seção.

?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking Global - Memóremon</title>
    <link rel="icon" href="../img/pokebola.png" type="image/png">
    <link rel="stylesheet" href="../css/global2.css">
    <link rel="stylesheet" href="../css/ranking.css">
</head>

<body>

    <?php require '../components/header.php'; ?>
    <?php include '../components/notificacaoMensagem.php'; ?>

    <main>
        <div class="container">
            <h1 class="titulo-pagina">Ranking Global de Jogadores</h1>

            <div class="info-ranking">
                <p>Top 10 melhores partidas pelos maiores tabuleiros e menor número de jogadas</p>
            </div>

            <div class="conteiner-ranking">
                <div class="tabela-ranking">
                    <div class="cabecalho-tabela">
                        <span>Posição</span>
                        <span>Jogador</span>
                        <span>Tabuleiro</span>
                        <span>Modalidade</span>
                        <span>Tempo</span>
                        <span>Jogadas</span>
                        <span>Data</span>
                    </div>

                    <?php if (empty($ranking_global)): ?>
                        <div class="linha-tabela linha-vazia">
                            <span style="grid-column: 1 / span 7; text-align: center; padding: 10px;">
                                O ranking ainda está vazio! Seja o primeiro a vencer uma partida.
                            </span>
                        </div>
                    <?php else: ?>
                        <?php $posicao = 1; ?>
                        <?php foreach ($ranking_global as $partida): ?>
                            <?php
                            // Classes especiais para pódium
                            $classe_lugar = '';
                            if ($posicao === 1)
                                $classe_lugar = 'primeiro-lugar';
                            elseif ($posicao === 2)
                                $classe_lugar = 'segundo-lugar';
                            elseif ($posicao === 3)
                                $classe_lugar = 'terceiro-lugar';
                            ?>
                            <div class="linha-tabela <?= $classe_lugar ?>">
                                <span class="posicao"><?= formatarPosicao($posicao) ?></span>
                                <span class="jogador"><?= htmlspecialchars($partida['username']) ?></span>
                                <span class="board"><?= htmlspecialchars($partida['dimensao']) ?></span>
                                <span class="mode"><?= htmlspecialchars($partida['modalidade']) ?></span>
                                <span class="time"><?= formatarTempoJogo($partida['tempo_gasto_seg']) ?></span>
                                <span class="moves"><?= htmlspecialchars($partida['num_jogadas']) ?></span>
                                <span class="date"><?= formatarData($partida['data_partida']) ?></span>
                            </div>
                            <?php $posicao++; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>

                <div class="barra-lateral-ranking">
                    <div class="cartao-lateral">
                        <h3>Critérios do Ranking</h3>
                        <ul>
                            <li>Maior tamanho de tabuleiro</li>
                            <li>Menor número de jogadas</li>
                            <li>Apenas partidas vencidas</li>
                            <li>Top 10 melhores performances</li>
                        </ul>
                    </div>

                    <div class="cartao-lateral">
                        <h3>Sua Melhor Posição</h3>
                        <div class="ranking-usuario">
                            <span class="posicao-usuario">
                                <?php
                                // Acessa a chave 'posicao' e formata se não for o placeholder '-'
                                $posicao = $melhor_posicao_jogador['posicao'];
                                echo $posicao !== '-' ? formatarPosicao($posicao) : $posicao;
                                ?>
                            </span>
                            <span class="nome-usuario"><?= htmlspecialchars($username_logado) ?></span>
                            <span class="tabuleiro-usuario">
                                <?php
                                // Acessa a chave 'dimensao'
                                echo htmlspecialchars($melhor_posicao_jogador['dimensao']);
                                ?>
                            </span>
                            <span class="jogadas-usuario">
                                <?php
                                // Acessa a chave 'num_jogadas'
                                $jogadas = $melhor_posicao_jogador['num_jogadas'];
                                echo $jogadas !== '-' ? htmlspecialchars($jogadas) . ' jogadas' : htmlspecialchars($jogadas);
                                ?>
                            </span>
                        </div>
                        <a class="botao-melhorar" href="./telajogo.php">Melhorar Pontuação</a>
                    </div>

                    <div class="cartao-lateral">
                        <h3>Novo Desafio</h3>
                        <p>Complete uma partida para entrar no ranking!</p>
                        <a href="./telajogo.php" class="botao-jogar">Jogar Agora</a>
                    </div>
                </div>
            </div>

            <div class="prompt-nova-partida">
                <h2>Partida Concluída!</h2>
                <p>Deseja iniciar uma nova partida?</p>
                <div class="botoes-prompt">
                    <button class="botao-sim">Sim, Jogar Novamente</button>
                    <button class="botao-nao">Não, Voltar ao Início</button>
                </div>
            </div>
        </div>
    </main>

    <?php require '../components/footer.php'; ?>
</body>

</html>