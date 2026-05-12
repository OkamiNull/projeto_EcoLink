<?php

session_start();
require_once '../config/database.php';

if (!isset($_SESSION['usuario_id'])){
    header('Location: login.php');
    exit;
};

$usuario_id = $_SESSION['usuario_id'];

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['retirar_ins'])) {
    $evento_id = $_POST['inscricao_id'] ?? 0;

    try {
        $pdo -> beginTransaction();
        
        $stmt = $pdo -> prepare("
            UPDATE eventos 
            SET inscritos = inscritos - 1
            WHERE eventos.id = ?
        ");
        $stmt -> execute([$evento_id]);


        $stmt = $pdo -> prepare("
            DELETE 
            FROM inscricoes 
            WHERE evento_id = ? AND usuario_id = ?
        ");
        $stmt -> execute([$evento_id, $usuario_id]);

        $pdo->commit();
        
        $_SESSION['sucesso_inscricao'] = 'Remoção realizada';
        header('Location: dashboard_usuario.php');
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['erro_inscricao'] = 'Erro ao retirar inscrição. Tente novamente.';
        header('Location: dashboard_usuario.php');
        exit;
    }
};

header('Location: dashboard_usuario.php');
exit;

?>