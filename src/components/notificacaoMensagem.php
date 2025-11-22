<?php

// Falta esse

$mensagem_para_exibir = $_SESSION['notificacao'] ?? null;

if ($mensagem_para_exibir) {
    $tipo = htmlspecialchars($mensagem_para_exibir['tipo']);
    $erros = $mensagem_para_exibir['erros'] ?? [];
    $texto_sucesso = $mensagem_para_exibir['texto'] ?? '';
    ?>
    
    <div class="modal-overlay" id="modalNotificacao">
        <div class="modal-content mensagem-<?php echo $tipo; ?>">
            <button class="modal-close">&times;</button>
            
            <div class="modal-header">
                <?php if ($tipo === 'sucesso'): ?>
                    <h3>🎉 Sucesso!</h3>
                <?php elseif ($tipo === 'erro'): ?>
                    <h3>⚠️ Atenção</h3>
                <?php endif; ?>
            </div>
            
            <div class="modal-body">
                <?php if ($tipo === 'sucesso'): ?>
                    <p><?php echo htmlspecialchars($texto_sucesso); ?></p>
                <?php elseif ($tipo === 'erro'): ?>
                    <ul>
                        <?php foreach ($erros as $erro): ?>
                            <li><?php echo htmlspecialchars($erro); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            
            <div class="modal-footer">
                <button class="btn-modal">Entendido</button>
            </div>
        </div>
    </div>

    <script src="../js/notificacao.js"></script>
    <?php
    unset($_SESSION['notificacao']);
}

?>