<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
class PartidaDAO
{
    private $conn;

    public function __construct()
    {
        $this->conn = $this->getConnection();
    }
    private function getConnection()
    {
        if (!defined('DB_HOST')) {
            require $_SERVER['DOCUMENT_ROOT'] . '/projeto-web/src/back/config.php';
        }

        try {
            // Conexão com o BD
            $conn = new PDO("mysql:host=" . host . ";dbname=" . name, user, pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            setNotificacaoErro("Erro na hora de conectar com o banco de dados.");
            throw new Exception("Erro na hora de conectar com o banco de dados.");
        }
    }
    public function salvarPartida($dados_partida)
    {
        if (!isset($dados_partida['jogador_id'], $dados_partida['dimensao'], $dados_partida['tempo_gasto_seg'])) {
            setNotificacaoErro('OS dados da partida estão incompletos. Não foi possível salvar');
            throw new Exception('OS dados da partida estão incompletos. Não foi possível salvar');
        }

        $tempo_seg = floatval($dados_partida['tempo_gasto_seg']);

        try {
            $sql = "INSERT INTO partidas (jogador_id, dimensao, modalidade, num_jogadas, tempo_gasto_seg, resultado) 
                    VALUES (:jogador_id, :dimensao, :modalidade, :num_jogadas, :tempo_gasto_seg, :resultado)";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(':jogador_id', $dados_partida['jogador_id'], PDO::PARAM_INT);
            $stmt->bindValue(':dimensao', $dados_partida['dimensao']);
            $stmt->bindValue(':modalidade', $dados_partida['modalidade']);
            $stmt->bindValue(':num_jogadas', $dados_partida['num_jogadas'], PDO::PARAM_INT);
            $stmt->bindValue(':tempo_gasto_seg', (string) $tempo_seg);
            $stmt->bindValue(':resultado', $dados_partida['resultado']);

            $stmt->execute();
            return true;

        } catch (PDOException $e) {
            setNotificacaoErro("Falha ao registrar a partida no banco de dados.");
            throw new Exception("Falha ao registrar a partida no banco de dados.");
        }
    }

    //Histórico do jogador
    public function buscarUltimasPartidas($jogador_id)
    {
        if (!is_numeric($jogador_id) || $jogador_id <= 0) {
            return [];
        }

        try {
            $sql = "SELECT dimensao, modalidade, num_jogadas, tempo_gasto_seg, resultado, data_partida 
                    FROM partidas 
                    WHERE jogador_id = :jogador_id
                    ORDER BY data_partida DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':jogador_id', $jogador_id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            setNotificacaoErro('Erro ao buscar o seu histórico de partidas: ' . $e->getMessage());
            throw new Exception('Erro ao buscar o seu histórico de partidas: ' . $e->getMessage());
        }
    }

    public function buscarRankingTop10()
    {
        try {
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

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            setNotificacaoErro('Erro ao buscar o ranking global: ' . $e->getMessage());
            throw new Exception('Erro ao buscar o ranking global: ' . $e->getMessage());
        }
    }

    public function encontrarMelhorPosicao($jogador_id)
    {
        if (!is_numeric($jogador_id) || $jogador_id <= 0) {
            return ['posicao' => '-', 'dimensao' => '-', 'num_jogadas' => '-'];
        }

        try {
            $sql_melhor_partida = "
                SELECT 
                    p.dimensao, 
                    p.num_jogadas, 
                    p.tempo_gasto_seg,
                    CAST(SUBSTRING_INDEX(p.dimensao, 'x', 1) AS UNSIGNED) AS tamanho_int
                FROM partidas p
                WHERE p.jogador_id = :jogador_id AND p.resultado = 'Vitoria'
                ORDER BY 
                    tamanho_int DESC, 
                    p.num_jogadas ASC,
                    p.tempo_gasto_seg ASC
                LIMIT 1
            ";
            $stmt_melhor_partida = $this->conn->prepare($sql_melhor_partida);
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

            $stmt_contar_melhores = $this->conn->prepare($sql_contar_melhores);
            $stmt_contar_melhores->bindParam(':melhor_tamanho', $melhor_partida['tamanho_int'], PDO::PARAM_INT);
            $stmt_contar_melhores->bindParam(':melhor_jogadas', $melhor_partida['num_jogadas'], PDO::PARAM_INT);
            $stmt_contar_melhores->bindParam(':melhor_tempo', $melhor_partida['tempo_gasto_seg']);
            $stmt_contar_melhores->execute();

            $posicao = $stmt_contar_melhores->fetchColumn();

            return [
                'posicao' => $posicao,
                'dimensao' => $melhor_partida['dimensao'],
                'num_jogadas' => $melhor_partida['num_jogadas']
            ];

        } catch (PDOException $e) {
            setNotificacaoErro('Erro ao calcular sua melhor posição: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function __destruct()
    {
        $this->conn = null;
    }
}