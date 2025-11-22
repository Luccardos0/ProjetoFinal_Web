<?php
function setNotificacao($tipo, $dados = array())
{
    $_SESSION['notificacao'] = array(
        'tipo' => $tipo,
        'texto' => $dados['texto'] ?? '',
        'erros' => $dados['erros'] ?? array(),
        'dados_anteriores' => $dados['dados_anteriores'] ?? array()
    );
}

function setNotificacaoSucesso($texto)
{
    setNotificacao('sucesso', array('texto' => $texto));
}

function setNotificacaoErro($erros, $dados_anteriores = array())
{
    setNotificacao('erro', array(
        'erros' => is_array($erros) ? $erros : array($erros),
        'dados_anteriores' => $dados_anteriores
    ));
}

?>