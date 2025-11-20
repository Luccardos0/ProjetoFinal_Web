<?php
function verificar_autenticacao()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {

        $_SESSION['login_mensagem'] = [
            'tipo' => 'erro',
            'erros' => ["Você precisa estar logado para acessar a área de jogo do Memóremon. Faça login ou cadastre-se."]
        ];

        header('Location: ../pages/login.php');
        exit;
    }
}

// se passar disso tudo o usuario ta logado

?>