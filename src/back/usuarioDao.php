<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'config.php';

function buscarUsuarioId($id)
{
    if (!is_numeric($id) || $id <= 0) {
        return [];
    }

    try {
        if (!isset($pdo)) {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        $sql = "SELECT id, username, email, nome_completo, data_nascimento, cpf, telefone 
                FROM jogadores 
                WHERE id = :id_usuario";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_usuario', $id, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(mode: PDO::FETCH_ASSOC);

        return $resultado ? $resultado : [];

    } catch (PDOException $e) {

        // Deu um belo de b.o 
        $_SESSION['login_mensagem'] = [
            'tipo' => 'erro',
            'erros' => ["Problema grave, por favor tente logar novamente."],
            'dados_anteriores' => []
        ];

        header('Location: processaLogout.php');
        exit;
    }
}
function salvarPartida($dados_partida)
{
    if (!isset($dados_partida['jogador_id'], $dados_partida['dimensao'], $dados_partida['tempo_gasto_seg'])) {
        error_log("Dados da partida incompletos.");
        return false;
    }

    $tempo_seg = floatval($dados_partida['tempo_gasto_seg']);

    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO partidas (jogador_id, dimensao, modalidade, num_jogadas, tempo_gasto_seg, resultado) 
                VALUES (:jogador_id, :dimensao, :modalidade, :num_jogadas, :tempo_gasto_seg, :resultado)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':jogador_id', $dados_partida['jogador_id'], PDO::PARAM_INT);
        $stmt->bindValue(':dimensao', $dados_partida['dimensao']);
        $stmt->bindValue(':modalidade', $dados_partida['modalidade']);
        $stmt->bindValue(':num_jogadas', $dados_partida['num_jogadas'], PDO::PARAM_INT);
        $stmt->bindValue(':tempo_gasto_seg', (string) $tempo_seg);
        $stmt->bindValue(':resultado', $dados_partida['resultado']);

        $stmt->execute();
        return true;

    } catch (PDOException $e) {
        error_log("Erro ao salvar partida para jogador {$dados_partida['jogador_id']}: " . $e->getMessage());
        return false;
    }
}

?>