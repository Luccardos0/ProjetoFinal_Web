<?php

class UsuarioDAO
{
    private $conn;

    public function __construct()
    {
        $this->conn = $this->getConnection();
    }
    private function getConnection()
    {
        $configPath = __DIR__ . '/../config.php';

        if (!file_exists($configPath)) {
            throw new Exception("Arquivo de configuração não encontrado: " . $configPath);
        }

        require_once $configPath;

        try {
            $conn = new PDO("mysql:host=" . host . ";dbname=" . name, user, pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            throw new Exception("Erro de conexão com o banco de dados.");
        }
    }
    public function buscarUsuarioId($id)
    {
        try {
            $sql = "SELECT id, username, email, nome_completo, data_nascimento, cpf, telefone 
                    FROM jogadores 
                    WHERE id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            return $usuario ? $usuario : null;
        } catch (PDOException $e) {
            throw new Exception("Erro ao buscar usuário: " . $e->getMessage());
        }
    }
    public function criarUsuario($dados)
    {
        try {
            $sql_check = "SELECT id FROM jogadores WHERE email = :email OR username = :username OR cpf = :cpf_limpo";
            $stmt_check = $this->conn->prepare($sql_check);

            $cpf_limpo = preg_replace('/[^0-9]/', '', $dados['cpf']);

            $stmt_check->bindParam(':email', $dados['email']);
            $stmt_check->bindParam(':username', $dados['username']);
            $stmt_check->bindParam(':cpf_limpo', $cpf_limpo);
            $stmt_check->execute();

            if ($stmt_check->rowCount() > 0) {
                $duplicado = $stmt_check->fetch(PDO::FETCH_ASSOC);
                $errors = array();
                throw new Exception("CPF, E-mail ou Nome de Usuário já está sendo utilizado.");
            }

            $senha_hash = password_hash($dados['senha'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO jogadores (nome_completo, cpf, telefone, email, username, senha, data_nascimento, termos) 
                    VALUES (:nome, :cpf_limpo, :telefone, :email, :username, :senha_hash, :data_nascimento, :termos)";

            $stmt = $this->conn->prepare($sql);

            $termos_int = $dados['termos_aceitos'] ? 1 : 0;
            $stmt->bindParam(':nome', $dados['nome_completo']);
            $stmt->bindParam(':cpf_limpo', $cpf_limpo);
            $stmt->bindParam(':telefone', $dados['telefone']);
            $stmt->bindParam(':email', $dados['email']);
            $stmt->bindParam(':username', $dados['username']);
            $stmt->bindParam(':senha_hash', $senha_hash);
            $stmt->bindParam(':data_nascimento', $dados['data_nascimento']);
            $stmt->bindParam(':termos', $termos_int, PDO::PARAM_INT);

            $stmt->execute();
            return true;

        } catch (PDOException $e) {
            throw new Exception("Erro no banco de dados: " . $e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function logar($username, $senha)
    {
        try {
            $sql = "SELECT id, username, senha FROM jogadores WHERE username = :username LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $jogador = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($jogador && password_verify($senha, $jogador['senha'])) {
                unset($jogador['senha']);
                return $jogador;
            } else {
                return null; // Erro no login (senha ou usuário errado)
            }

        } catch (PDOException $e) {
            throw new Exception("Erro ao tentar logar: " . $e->getMessage());
        }
    }
    public function atualizarUsuario($id, $dados)
    {
        if (!is_numeric($id) || empty($dados)) {
            return false;
        }

        try {
            $campos_sql = [];
            $parametros = ['id_usuario' => $id];

            foreach ($dados as $campo => $valor) {
                $campos_sql[] = "`{$campo}` = :{$campo}";
                $parametros[":{$campo}"] = $valor;
            }

            if (empty($campos_sql)) {
                return true;
            }

            $sql = "UPDATE jogadores SET " . implode(', ', $campos_sql) . " WHERE id = :id_usuario";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($parametros);

            return true;

        } catch (PDOException $e) {
            throw new Exception("Erro ao atualizar o perfil: " . $e->getMessage());
        }
    }

    public function __destruct()
    {
        $this->conn = null;
    }
}