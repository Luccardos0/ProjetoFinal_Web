<?php

// 1º Ter o MySQL e PHP instalados
// 2º Definir as variavéis da sua conexão no config.php
require_once __DIR__ . "/config.php";

$host = host;
$user = user;
$pass = pass;

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Erro de conexão com MySQL: " . $conn->connect_error);
}

// Se não der erro de conexão, ele vai criar o banco e as tabelas automaticamente. 
// Em seguida ele te redireciona para a home
$sql = "
CREATE DATABASE IF NOT EXISTS memoremon_db
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;

USE memoremon_db;

CREATE TABLE IF NOT EXISTS jogadores (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nome_completo VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    username VARCHAR(50) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    data_nascimento DATE NOT NULL,
    termos BOOLEAN NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_cpf (cpf),
    UNIQUE KEY uk_email (email),
    UNIQUE KEY uk_username (username)
);

CREATE TABLE IF NOT EXISTS partidas (
    id INT(11) NOT NULL AUTO_INCREMENT,
    jogador_id INT(11) NOT NULL,
    dimensao VARCHAR(5) NOT NULL,
    modalidade VARCHAR(20) NOT NULL,
    num_jogadas INT(5) NOT NULL,
    tempo_gasto_seg DECIMAL(6, 2) NOT NULL,
    resultado VARCHAR(10) NOT NULL,
    data_partida DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (jogador_id)
        REFERENCES jogadores(id)
        ON DELETE CASCADE
);
";

if ($conn->multi_query($sql)) {
    echo "Banco de dados e tabelas criados com sucesso!";
} else {
    echo "Erro ao criar banco ou tabelas: " . $conn->error;
}

$conn->close();

header("Location: ../pages/index.php");
exit;

?>