<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$logado = isset($_SESSION['logado']) && $_SESSION['logado'] === true;

$username = $_SESSION['username'] ?? 'Jogador';

$avatar_url = $logado
    ? "https://placehold.co/50x50/3498db/ffffff?text=" . substr($username, 0, 1)
    : "https://placehold.co/50x50/cccccc/333333?text=👤";

$link_area_jogador = $logado ? "../pages/telajogo.php" : "../pages/login.php";
?>

<link rel="stylesheet" href="../css/global2.css">

<header>
    <div class="container">
        <div class="logo">
            <img src="../img/logo2.png" alt="Memóremon">
        </div>
        <nav>
            <ul>
                <li><a href="../pages/index.php">Início</a></li>
                <li><a href="../pages/telajogo.php">Jogo</a></li>
                <li><a href="../pages/ranking.php">Ranking</a></li>
            </ul>
        </nav>

        <div class="menu-perfil">
            <div class="perfil-icone-container">
                <img src="<?php echo $avatar_url; ?>" alt="Perfil de <?php echo htmlspecialchars($username); ?>"
                    class="avatar-icone">
            </div>

            <?php if ($logado): ?>
                <div class="dropdown-menu">
                    <p class="dropdown-username">Olá, <?php echo htmlspecialchars($username); ?></p>
                    <a href="../pages/editarperfil.php">Editar Perfil</a>
                    <a href="../back/logout.php">Sair (Logout)</a>
                </div>
            <?php else: ?>
                <a href="../pages/login.php" class="botao-login-header">Entrar</a>
            <?php endif; ?>
        </div>
    </div>
</header>