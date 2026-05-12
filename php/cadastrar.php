<?php
// public/cadastrar.php - Cadastro de usuário comum
require_once '../config/database.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';
    
    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = ' Todos os campos são obrigatórios!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = ' Email inválido!';
    } elseif (strlen($senha) < 6) {
        $erro = ' Senha deve ter no mínimo 6 caracteres!';
    } elseif ($senha !== $confirmar) {
        $erro = ' Senhas não coincidem!';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $erro = ' Email já cadastrado!';
            } else {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, 'usuario')");
                
                if ($stmt->execute([$nome, $email, $senha_hash])) {
                    $sucesso = 'Cadastro realizado! <a href="login.php">Faça login</a>';
                } else {
                    $erro = ' Erro ao cadastrar!';
                }
            }
        } catch (PDOException $e) {
            $erro = ' Erro no banco de dados!';
        }
    }
}

// Incluir o HTML
ob_start();
include '../view/cadastro_usuario.html';
$html = ob_get_clean();

// Adicionar mensagens
if ($erro) {
    $html = str_replace('<!-- MENSAGEM_AREA -->', '<div class="mensagem erro">' . $erro . '</div>', $html);
} elseif ($sucesso) {
    $html = str_replace('<!-- MENSAGEM_AREA -->', '<div class="mensagem sucesso">' . $sucesso . '</div>', $html);
} else {
    $html = str_replace('<!-- MENSAGEM_AREA -->', '', $html);
}

echo $html;
?>