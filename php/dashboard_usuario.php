<?php
// public/dashboard_usuario.php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['usuario_tipo'] !== 'usuario') {
    $_SESSION['aviso'] = 1;
    header('Location: login_criador.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$mensagem = '';

// Buscar DATA DE CADASTRO do usuário
$data_cadastro = '';
$total_eventos = 0;

try {
    $stmt = $pdo->prepare("SELECT data_cadastro FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario_data = $stmt->fetch();
    if ($usuario_data) {
        $data_cadastro = date('d/m/Y', strtotime($usuario_data['data_cadastro']));
    }
} catch (PDOException $e) {
    $data_cadastro = 'Não disponível';
}

// Verificar se tem mensagem de sessão
if (isset($_SESSION['sucesso_inscricao'])) {
    $mensagem = '<div class="mensagem sucesso">' . $_SESSION['sucesso_inscricao'] . '</div>';
    unset($_SESSION['sucesso_inscricao']);
}
if (isset($_SESSION['erro_inscricao'])) {
    $mensagem = '<div class="mensagem erro">' . $_SESSION['erro_inscricao'] . '</div>';
    unset($_SESSION['erro_inscricao']);
}

// Buscar inscrições do usuário
$inscricoes_html = '';
try {
    $stmt = $pdo->prepare("
        SELECT i.id, e.*, i.data_inscricao,i.evento_id , i.status as inscricao_status,
               c.nome_fantasia, c.razao_social
        FROM inscricoes i
        JOIN eventos e ON i.evento_id = e.id
        JOIN criadores c ON e.criador_id = c.id
        WHERE i.usuario_id = ? AND i.status = 'confirmada'
        ORDER BY e.data_evento ASC
    ");
    $stmt->execute([$usuario_id]);
    $inscricoes = $stmt->fetchAll();

    $total_eventos = count($inscricoes);
    
    if ($total_eventos > 0) {
        foreach ($inscricoes as $insc) {

            $botao ='<form method="POST" action="retirar_ins.php" >
                <input type="hidden" name="inscricao_id" value="' . $insc['evento_id'] . '">
                <button type="submit" name="retirar_ins" class="retirar-inscricao">Retirar Inscrição</button>
            </form>'; 

            $data_evento = date('d/m/Y H:i', strtotime($insc['data_evento']));
            $inscricoes_html .= '
                <div class="card-evento">
                    <h4>' . htmlspecialchars($insc['nome_evento']) . '</h4>
                    <p><strong>Realizado por:</strong> ' . htmlspecialchars($insc['nome_fantasia'] ?: $insc['razao_social']) . '</p>
                    <p class="endereco">' . htmlspecialchars($insc['endereco']) . '</p>
                    <p class="data">Data: ' . $data_evento . '</p>
                    <p>Inscrito em: ' . date('d/m/Y', strtotime($insc['data_inscricao'])) . '</p>
                    <span class="badge-inscrito">✓ Inscrito</span>
                    '. $botao .'
                </div>
            ';
        }
    } else {
        $inscricoes_html = '<div class="sem-eventos">Você ainda não se inscreveu em nenhum evento.<br><a href="listar_eventos.php">Ver eventos disponíveis</a></div>';
    }
} catch (PDOException $e) {
    $inscricoes_html = '<div class="mensagem erro">Erro ao carregar inscrições</div>';
}

// Incluir o HTML
ob_start();
include '../view/dashboard_usuario.html';
$html = ob_get_clean();

// Substituir placeholders
$html = str_replace('<!-- NOME_USUARIO -->', htmlspecialchars($_SESSION['usuario_nome']), $html);
$html = str_replace('<!-- EMAIL_USUARIO -->', htmlspecialchars($_SESSION['usuario_email']), $html);
$html = str_replace('<!-- DATA_CADASTRO -->', $data_cadastro, $html);
$html = str_replace('<!-- TOTAL_EVENTOS -->', $total_eventos, $html);
$html = str_replace('<!-- MENSAGEM_AREA -->', $mensagem, $html);
$html = str_replace('<!-- LISTA_INSCRICOES -->', $inscricoes_html, $html);

echo $html;
?>