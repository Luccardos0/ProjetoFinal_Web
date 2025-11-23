<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require './autentica.php'; 
require_once './setNotificacao.php'; 
require './DAO/partidaDAO.php'; 


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    setNotificacaoErro(array("Acesso inválido."), []);
    header('Location: ../pages/telajogo.php');
    exit;
}

verificar_autenticacao();
$jogador_id = $_SESSION['user_id'] ?? 0;

if ($jogador_id <= 0) {
    header('Location: processaLogout.php');
    exit;
}

$dados_recebidos = [
    'dimensao' => $_POST['dimensao'] ?? '',
    'modalidade' => $_POST['modalidade'] ?? 'Clássico',
    'num_jogadas' => intval($_POST['num_jogadas'] ?? 0),
    'tempo_gasto_seg' => $_POST['tempo_gasto_seg'] ?? 0.00,
    'resultado' => $_POST['resultado'] ?? 'B.O',
];

$dados_partida = array_merge($dados_recebidos, ['jogador_id' => $jogador_id]);

if ($dados_partida['num_jogadas'] <= 0 || empty($dados_partida['dimensao'])) {
    setNotificacaoErro(array("Os dados da partida estão incompletos ou inválidos"), []);
    header('Location: ../pages/telajogo.php');
    exit;
}

try {
    $partidaDAO = new PartidaDAO();

    if ($partidaDAO->salvarPartida($dados_partida)) {
        setNotificacaoSucesso("Partida registrada com sucesso! Seu histórico também foi atualizado.");
        header('Location: ../pages/telajogo.php');
        exit;
    } else { 
        if (!isset($_SESSION['notificacao'])) {
             setNotificacaoErro(array("Falha interna ao salvar a partida."), []);
        }
        
        header('Location: ../pages/telajogo.php');
        exit;
    }
} catch (Exception $e) {
    setNotificacaoErro(array("Erro grave do servidor: " . $e->getMessage()), []);
    header('Location: ../pages/telajogo.php');
    exit;
}

?>