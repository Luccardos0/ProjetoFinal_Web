<?php
// Definir título da página dinamicamente
$pageTitle = isset($pageTitle) ? $pageTitle : "Memóremon";
?>

<header>
    <div class="container">
        <div class="logo">
            <img src="../img/logo2.png" alt="Memóremon">
        </div>
        <nav>
            <ul>
                <li><a href="../pages/index.php">Início</a></li>
                <li><a href="../pages/telajogo.php">Jogo</a></li>
                <li><a href="../pages/login.php">Área do Jogador</a></li>
                <li><a href="../pages/editarperfil.php">Editar Perfil</a></li>
                <li><a href="../pages/ranking.php">Ranking</a></li>
            </ul>
        </nav>
    </div>
</header>