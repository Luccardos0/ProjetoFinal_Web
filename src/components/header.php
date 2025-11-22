<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$logado = isset($_SESSION['logado']) && $_SESSION['logado'] === true;

$username = $_SESSION['username'] ?? 'Jogador';

$avatar_url = $logado
    ? "https://placehold.co/50x50/3498db/ffffff?text=" . substr($username, 0, 1)
    : "";
?>

<link rel="stylesheet" href="../css/global2.css">

<header>
    <div class="container">
        <div class="logo" href="../pages/index.php">
            <a href="<?php echo $logado ? '../pages/telajogo.php' : '../pages/index.php'; ?>">
                <img src="../img/logo2.png" alt="Memóremon">
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="../pages/index.php">Início</a></li>
                <li><a href="../pages/telajogo.php">Jogo</a></li>
                <li><a href="../pages/ranking.php">Ranking</a></li>
            </ul>
        </nav>

        <div class="menu-perfil">
            <?php if ($logado): ?>
                <div class="perfil-icone-container">
                    <img src="<?php echo $avatar_url; ?>" alt="Perfil de <?php echo htmlspecialchars($username); ?>"
                        class="avatar-icone">
                </div>

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