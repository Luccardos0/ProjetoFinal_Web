<?php
require '../back/autentica.php';

verificar_autenticacao();

?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Memóremon</title>
    <link rel="icon" href="../img/pokebola.png" type="image/png">
    <link rel="stylesheet" href="../css/global2.css">
    <link rel="stylesheet" href="../css/editarperfil.css">
</head>
<body>
    
    <?php require '../components/header.php'; ?>

    <main>
        <section class="secao-editar">
            <div class="container">
                <div class="container-formulario">
                    <div class="cabecalho-formulario">
                        <h2>Editar Informações Pessoais</h2>
                        <p>Atualize suas informações abaixo. Campos marcados com * são obrigatórios.</p>
                    </div>
                    
                    <form class="formulario-editar">
                        <div class="linha-formulario">
                            <div class="grupo-formulario">
                                <label for="username">Username *</label>
                                <input type="text" id="username" name="username" class="campo-somente-leitura" value="AshKetchum" readonly>
                            </div>
                            <div class="grupo-formulario">
                                <label for="email">E-mail *</label>
                                <input type="email" id="email" name="email" value="ash@pokemon.com" required>
                            </div>
                        </div>
                        
                        <div class="linha-formulario">
                            <div class="grupo-formulario">
                                <label for="nome">Nome Completo *</label>
                                <input type="text" id="nome" name="nome" value="Ash Ketchum" required>
                            </div>
                            <div class="grupo-formulario">
                                <label for="dataNascimento">Data de Nascimento *</label>
                                <input type="date" id="dataNascimento" name="dataNascimento" class="campo-somente-leitura" value="1998-05-22" readonly>
                            </div>
                        </div>
                        
                        <div class="linha-formulario">
                            <div class="grupo-formulario">
                                <label for="cpf">CPF *</label>
                                <input type="text" id="cpf" name="cpf" class="campo-somente-leitura" value="123.456.789-00" readonly>
                            </div>
                            <div class="grupo-formulario">
                                <label for="telefone">Telefone</label>
                                <input type="tel" id="telefone" name="telefone" value="(19) 99999-9999">
                            </div>
                        </div>
                        
                        <div class="grupo-formulario largura-completa">
                            <label for="senha">Nova Senha (deixe em branco para manter a atual)</label>
                            <input type="password" id="senha" name="senha" placeholder="Digite uma nova senha">
                        </div>
                        
                        <div class="grupo-formulario largura-completa">
                            <label for="confirmarSenha">Confirmar Nova Senha</label>
                            <input type="password" id="confirmarSenha" name="confirmarSenha" placeholder="Confirme a nova senha">
                        </div>
                        
                        <div class="acoes-formulario">
                            <button type="submit" class="botao-primario">Salvar Alterações</button>
                            <a href="index2.html" class="botao-secundario">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="conteudo-rodape">
                &copy; Faculdade de Tecnologia da Unicamp - Programação Web - 2025
            </div>
        </div>
    </footer>
</body>
</html>