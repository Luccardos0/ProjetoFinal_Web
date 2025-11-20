<?php

if (isset($_SESSION['cadastro_mensagem'])) {
    $msg = $_SESSION['cadastro_mensagem'];
    $tipo = htmlspecialchars($msg['tipo']);
    $erros = $msg['erros'] ?? [];

    echo "<div class='mensagem-alerta mensagem-{$tipo}'>";
    
    if ($tipo === 'sucesso') {
        echo $msg['texto'];
    } elseif ($tipo === 'erro') {
        echo "<h4>Atenção! Por favor, corrija os seguintes problemas:</h4>";
        echo "<ul>";
        foreach ($erros as $erro) {
            echo "<li>" . htmlspecialchars($erro) . "</li>";
        }
        echo "</ul>";
    }
    
    echo "</div>";

    // Limpa a mensagem da sessão depois que a gente mostra
    unset($_SESSION['cadastro_mensagem']);
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