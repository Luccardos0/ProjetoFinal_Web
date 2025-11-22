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
?>

