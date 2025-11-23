<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "../back/setNotificacao.php";

$_SESSION = array();
session_destroy();

header('Location: ../pages/login.php');
exit;
?>