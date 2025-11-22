<?php

require "../back/setNotificacao.php";

function verificar_autenticacao()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {

        setNotificacaoErro("Você precisa estar logado para acessar a área de jogo ou ranking do Memóremon. Faça login ou cadastre-se.");

        header('Location: ../pages/login.php');
        exit;
    }
}

?>