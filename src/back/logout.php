<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = array();

session_destroy();

$_SESSION['login_mensagem'] = [
    'tipo' => 'sucesso',
    'texto' => "Você saiu da sua conta com sucesso. Esperamos você de volta!"
];

header('Location: ../pages/login.php');
exit;
?>