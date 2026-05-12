<?php
// public/cadastrar_criador.php - Cadastro de criador
require_once '../config/database.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';
    $razao_social = trim($_POST['razao_social'] ?? '');
    $nome_fantasia = trim($_POST['nome_fantasia'] ?? '');
    $cnpj = trim($_POST['cnpj'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    
    if (empty($nome) || empty($email) || empty($senha) || empty($razao_social) || empty($cnpj)) {
        $erro = 'Campos obrigatórios: Nome, Email, Senha, Razão Social e CNPJ!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Email inválido!';
    } elseif (strlen($senha) < 6) {
        $erro = 'Senha deve ter no mínimo 6 caracteres!';
    } elseif ($senha !== $confirmar) {
        $erro = 'Senhas não coincidem!';
    } else {
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $erro = 'Email já cadastrado!';
                $pdo->rollBack();
            } else {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, 'criador')");
                $stmt->execute([$nome, $email, $senha_hash]);
                $usuario_id = $pdo->lastInsertId();
                
                $stmt = $pdo->prepare("INSERT INTO criadores (usuario_id, razao_social, nome_fantasia, cnpj, telefone, endereco) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$usuario_id, $razao_social, $nome_fantasia, $cnpj, $telefone, $endereco]);
                
                $pdo->commit();
                $sucesso = 'Cadastro de criador realizado! <a href="login.php">Faça login</a>';
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            if ($e->getCode() == 23000) {
                $erro = 'CNPJ já cadastrado!';
            } else {
                $erro = 'Erro no banco de dados!';
            }
        }
    }
}

ob_start();
include '../view/cadastro_criador.html';
$html = ob_get_clean();

if ($erro) {
    $html = str_replace('<!-- MENSAGEM_AREA -->', '<div class="mensagem erro">' . $erro . '</div>', $html);
} elseif ($sucesso) {
    $html = str_replace('<!-- MENSAGEM_AREA -->', '<div class="mensagem sucesso">' . $sucesso . '</div>', $html);
} else {
    $html = str_replace('<!-- MENSAGEM_AREA -->', '', $html);
}

echo $html;
?>