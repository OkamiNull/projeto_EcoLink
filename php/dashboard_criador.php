<?php
// public/dashboard_criador.php - LÓGICA do dashboard do criador
session_start();
require_once '../config/database.php';

// ============================================
// 1. VERIFICAR SE USUÁRIO ESTÁ LOGADO
// ============================================
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// ============================================
// 2. VERIFICAR SE É CRIADOR
// ============================================
if ($_SESSION['usuario_tipo'] !== 'criador') {
    header('Location: dashboard_usuario.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// ============================================
// 3. BUSCAR DADOS DO CRIADOR NO BANCO
// ============================================
$razao_social = $_SESSION['criador_razao_social'];
$nome_fantasia = $_SESSION['criador_nome_fantasia'];
$cnpj = $_SESSION['criador_cnpj'];
$telefone = $_SESSION['criador_telefone'];
$endereco = $_SESSION['criador_endereco'];
$data_cadastro = $_SESSION['criador_data_cadastro'];
$criador_id = $_SESSION['criador_id'];

// Estatísticas
$total_eventos = 0;
$total_inscritos = 0;
$eventos_ativos = 0;
$capacidade_total = 0;

try {
        // ============================================
        // 4. CALCULAR ESTATÍSTICAS
        // ============================================
        
        // Total de eventos criados
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM eventos WHERE criador_id = ?");
        $stmt->execute([$criador_id]);
        $total_eventos = $stmt->fetch()['total'];
        
        // Total de inscrições (somando todos os eventos)
        $stmt = $pdo->prepare("SELECT SUM(inscritos) as total FROM eventos WHERE criador_id = ?");
        $stmt->execute([$criador_id]);
        $total_inscritos = $stmt->fetch()['total'] ?: 0;
        
        // Eventos ativos (status = 'ativo' E data futura)
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM eventos 
            WHERE criador_id = ? AND status = 'ativo' AND data_evento > NOW()
        ");
        $stmt->execute([$criador_id]);
        $eventos_ativos = $stmt->fetch()['total'];
        
        // Capacidade total (soma de todas as vagas)
        $stmt = $pdo->prepare("SELECT SUM(capacidade_maxima) as total FROM eventos WHERE criador_id = ?");
        $stmt->execute([$criador_id]);
        $capacidade_total = $stmt->fetch()['total'] ?: 0;
        
} catch (PDOException $e) {
    // Em caso de erro, mantém os valores padrão
    $erro_msg = $e->getMessage();
}

// ============================================
// 5. INCLUIR O HTML E SUBSTITUIR OS PLACEHOLDERS
// ============================================

// Iniciar buffer para capturar o HTML
ob_start();

// Incluir o arquivo HTML
include '../view/dashboard_criador.html';

// Pegar todo o conteúdo do buffer
$html = ob_get_clean();

// Substituir os placeholders pelos dados reais
$html = str_replace('<!-- NOME_USUARIO -->', htmlspecialchars($_SESSION['usuario_nome']), $html);
$html = str_replace('<!-- EMAIL_USUARIO -->', htmlspecialchars($_SESSION['usuario_email']), $html);
$html = str_replace('<!-- RAZAO_SOCIAL -->', htmlspecialchars($razao_social), $html);
$html = str_replace('<!-- NOME_FANTASIA -->', htmlspecialchars($nome_fantasia), $html);
$html = str_replace('<!-- CNPJ -->', htmlspecialchars($cnpj), $html);
$html = str_replace('<!-- TELEFONE -->', htmlspecialchars($telefone), $html);
$html = str_replace('<!-- ENDERECO -->', htmlspecialchars($endereco), $html);
$html = str_replace('<!-- DATA_CADASTRO -->', $data_cadastro, $html);
$html = str_replace('<!-- TOTAL_EVENTOS -->', $total_eventos, $html);
$html = str_replace('<!-- TOTAL_INSCRITOS -->', $total_inscritos, $html);
$html = str_replace('<!-- EVENTOS_ATIVOS -->', $eventos_ativos, $html);
$html = str_replace('<!-- CAPACIDADE_TOTAL -->', $capacidade_total, $html);

// Remover placeholder de mensagem (vazio por enquanto)
$html = str_replace('<!-- MENSAGEM_AREA -->', '', $html);

// ============================================
// 6. EXIBIR O HTML FINAL
// ============================================
echo $html;
?>