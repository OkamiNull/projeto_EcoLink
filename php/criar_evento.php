<?php
// public/criar_evento.php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'criador') {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Buscar ID do criador
$criador_id = null;
$stmt = $pdo->prepare("SELECT id FROM criadores WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$criador = $stmt->fetch();
if ($criador) {
    $criador_id = $criador['id'];
} else {
    header('Location: dashboard_criador.php?erro=Dados de criador não encontrados');
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_evento = trim($_POST['nome_evento'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $data_evento = $_POST['data_evento'] ?? '';
    $capacidade_maxima = intval($_POST['capacidade_maxima'] ?? 0);
    
    if (empty($nome_evento) || empty($endereco) || empty($data_evento) || $capacidade_maxima <= 0) {
        $erro = ' Preencha todos os campos obrigatórios!';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO eventos (criador_id, nome_evento, descricao, endereco, data_evento, capacidade_maxima) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$criador_id, $nome_evento, $descricao, $endereco, $data_evento, $capacidade_maxima]);
            $sucesso = 'Evento criado com sucesso! <a href="meus_eventos.php">Ver meus eventos</a>';
        } catch (PDOException $e) {
            $erro = ' Erro ao criar evento!';
        }
    }
}

ob_start();
include '../view/criar_evento.html';
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