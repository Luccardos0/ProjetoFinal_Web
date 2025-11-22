<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
function getPDOConnection() {
    if (!defined('host')) {
        require $_SERVER['DOCUMENT_ROOT'] . '/projeto-web/src/back/config.php';
    }
    
    $pdo = new PDO("mysql:host=" . host . ";dbname=" . name, user, pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}
function salvarPartida($dados_partida)
{
    if (!isset($dados_partida['jogador_id'], $dados_partida['dimensao'], $dados_partida['tempo_gasto_seg'])) {
        $_SESSION['notificacao'] = [
            'tipo' => 'erro',
            'mensagem' => 'Dados da partida estão incompletos. Não foi possível salvar.'
        ];
        return false;
    }

    $tempo_seg = floatval($dados_partida['tempo_gasto_seg']);

    try {
        $pdo = getPDOConnection();
        
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
        $_SESSION['notificacao'] = [
            'tipo' => 'erro',
            'mensagem' => 'Falha ao registrar a partida no banco de dados. (' . $e->getMessage() . ')'
        ];
        return false; 
    }
}
function buscarUltimasPartidas($jogador_id, $limite = 5) {
    if (!is_numeric($jogador_id) || $jogador_id <= 0) {
        return [];
    }
    
    try {
        $pdo = getPDOConnection();
        
        $sql = "SELECT dimensao, modalidade, num_jogadas, tempo_gasto_seg, resultado, data_partida 
                FROM partidas 
                WHERE jogador_id = :jogador_id
                ORDER BY data_partida DESC
                LIMIT :limite";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':jogador_id', $jogador_id, PDO::PARAM_INT);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Erro ao buscar histórico do jogador {$jogador_id}: " . $e->getMessage());
        return [];
    }
}
function buscarRankingTop10() {    
    try {
        $pdo = getPDOConnection();
        $sql = "SELECT 
                    p.dimensao, 
                    p.modalidade, 
                    p.num_jogadas, 
                    p.tempo_gasto_seg,
                    p.resultado,
                    p.data_partida,
                    j.username,
                    CAST(SUBSTRING_INDEX(p.dimensao, 'x', 1) AS UNSIGNED) AS tamanho_int
                FROM partidas p
                JOIN jogadores j ON p.jogador_id = j.id
                WHERE p.resultado = 'Vitoria'
                ORDER BY 
                    tamanho_int DESC,
                    p.num_jogadas ASC,      
                    p.tempo_gasto_seg ASC   
                LIMIT 10";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Erro ao buscar ranking: " . $e->getMessage());
        return [];
    }
}
function encontrarMelhorPosicao($jogador_id) {
    if (!is_numeric($jogador_id) || $jogador_id <= 0) {
        return ['posicao' => '-', 'dimensao' => '-', 'num_jogadas' => '-'];
    }

    try {
        $pdo = getPDOConnection();
        
        $sql_melhor_partida = "
            SELECT 
                p.dimensao, 
                p.num_jogadas, 
                CAST(SUBSTRING_INDEX(p.dimensao, 'x', 1) AS UNSIGNED) AS tamanho_int
            FROM partidas p
            WHERE p.jogador_id = :jogador_id AND p.resultado = 'Vitoria'
            ORDER BY 
                tamanho_int DESC, 
                p.num_jogadas ASC,
                p.tempo_gasto_seg ASC
            LIMIT 1
        ";
        $stmt_melhor_partida = $pdo->prepare($sql_melhor_partida);
        $stmt_melhor_partida->bindParam(':jogador_id', $jogador_id, PDO::PARAM_INT);
        $stmt_melhor_partida->execute();
        $melhor_partida = $stmt_melhor_partida->fetch(PDO::FETCH_ASSOC);

        if (!$melhor_partida) {
            return ['posicao' => '-', 'dimensao' => '-', 'num_jogadas' => 'Nenhuma vitória'];
        }

        $sql_contar_melhores = "
            SELECT COUNT(*) + 1 AS posicao
            FROM (
                SELECT 
                    p.dimensao, 
                    p.num_jogadas, 
                    p.tempo_gasto_seg,
                    CAST(SUBSTRING_INDEX(p.dimensao, 'x', 1) AS UNSIGNED) AS tamanho_int
                FROM partidas p
                WHERE p.resultado = 'Vitoria'
            ) AS todas_partidas
            WHERE 
                (tamanho_int > :melhor_tamanho)
                OR 
                (tamanho_int = :melhor_tamanho AND num_jogadas < :melhor_jogadas)
                OR
                (tamanho_int = :melhor_tamanho AND num_jogadas = :melhor_jogadas AND tempo_gasto_seg < :melhor_tempo)
        ";

        $stmt_contar_melhores = $pdo->prepare($sql_contar_melhores);
        $stmt_contar_melhores->bindParam(':melhor_tamanho', $melhor_partida['tamanho_int'], PDO::PARAM_INT);
        $stmt_contar_melhores->bindParam(':melhor_jogadas', $melhor_partida['num_jogadas'], PDO::PARAM_INT);

        $sql_tempo = "SELECT tempo_gasto_seg FROM partidas WHERE jogador_id = :jogador_id AND dimensao = :dimensao AND num_jogadas = :jogadas AND resultado = 'Vitoria' ORDER BY tempo_gasto_seg ASC LIMIT 1";
        $stmt_tempo = $pdo->prepare($sql_tempo);
        $stmt_tempo->bindParam(':jogador_id', $jogador_id, PDO::PARAM_INT);
        $stmt_tempo->bindParam(':dimensao', $melhor_partida['dimensao']);
        $stmt_tempo->bindParam(':jogadas', $melhor_partida['num_jogadas'], PDO::PARAM_INT);
        $stmt_tempo->execute();
        $tempo_melhor = $stmt_tempo->fetchColumn();

        $stmt_contar_melhores->bindParam(':melhor_tempo', $tempo_melhor); // Usar o tempo real
        $stmt_contar_melhores->execute();
        
        $posicao = $stmt_contar_melhores->fetchColumn();

        return [
            'posicao' => $posicao,
            'dimensao' => $melhor_partida['dimensao'],
            'num_jogadas' => $melhor_partida['num_jogadas']
        ];

    } catch (PDOException $e) {
        error_log("Erro ao buscar melhor posição do jogador {$jogador_id}: " . $e->getMessage());
        return ['posicao' => '-', 'dimensao' => '-', 'num_jogadas' => 'Erro DB'];
    }
}