<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'autentica.php'; 
require './DAO/partidaDAO.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['notificacao'] = [
        'tipo' => 'erro',
        'mensagem' => 'Método de requisição inválido.'
    ];
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
    'resultado' => $_POST['resultado'] ?? 'Desconhecido',
];

$dados_partida = array_merge($dados_recebidos, ['jogador_id' => $jogador_id]);

if ($dados_partida['num_jogadas'] <= 0 || empty($dados_partida['dimensao'])) {
    $_SESSION['notificacao'] = [
        'tipo' => 'erro',
        'mensagem' => 'Dados da partida estão incompletos ou inválidos.'
    ];
    header('Location: ../pages/telajogo.php');
    exit;
}

if (salvarPartida($dados_partida)) {
    $_SESSION['notificacao'] = [
        'tipo' => 'sucesso',
        'mensagem' => 'Partida registrada com sucesso! Seu histórico foi atualizado.'
    ];
    
    header('Location: ../pages/telajogo.php');
    exit;
} else {    
    if (!isset($_SESSION['notificacao'])) {
         $_SESSION['notificacao'] = [
            'tipo' => 'erro',
            'mensagem' => 'Falha interna ao salvar a partida. Tente novamente.'
        ];
    }
    
    header('Location: ../pages/telajogo.php');
    exit;
}

?>