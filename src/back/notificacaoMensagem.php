<?php

$session_keys = ['login_mensagem', 'cadastro_mensagem'];

$mensagem_para_exibir = null;
$key_to_clear = null;

foreach ($session_keys as $key) {
    if (isset($_SESSION[$key])) {
        $mensagem_para_exibir = $_SESSION[$key];
        $key_to_clear = $key;
        break;
    }
}

if ($mensagem_para_exibir) {
    $msg = $mensagem_para_exibir;
    $tipo = htmlspecialchars($msg['tipo']);
    $erros = $msg['erros'] ?? [];
    $texto_sucesso = $msg['texto'] ?? '';

    echo "<div class='mensagem-alerta mensagem-{$tipo}'>";

    if ($tipo === 'sucesso') {
        echo "<h4>Sucesso!</h4>";
        echo "<p>{$texto_sucesso}</p>";
    } elseif ($tipo === 'erro') {
        echo "<h4>❌ Atenção!:</h4>";
        echo "<ul>";
        foreach ($erros as $erro) {
            echo "<li>" . htmlspecialchars($erro) . "</li>";
        }
        echo "</ul>";
    }

    echo "</div>";

    unset($_SESSION[$key_to_clear]);
}
?>

<style>
    .mensagem-alerta {
        padding: 15px;
        margin: 15px auto;
        border-radius: 5px;
        max-width: 600px;
        border: 1px solid transparent;
        font-size: 1.1em;
    }

    .mensagem-sucesso {
        background-color: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }

    .mensagem-erro {
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }

    .mensagem-alerta ul {
        margin-top: 10px;
        padding-left: 20px;
    }
</style>