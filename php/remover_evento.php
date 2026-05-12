<?php

session_start();
require_once '../config/database.php';

if(!isset($_SESSION['usuario_id'])){
    header('Location: login_criador.php');
    exit;
};

$usuario_id = $_SESSION['usuario_id'];
$criador_id = $_SESSION['criador_id'];

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover_eve'])){

    $evento_id = $_SESSION['evento_id'] ?? 0;

    try{
        $stmt = $pdo->prepare("
        DELETE 
        FROM inscricoes, eventos
        WHERE inscricoes.evento_id=? AND eventos.id=?;
        ");
        $stmt->execute($evento_id);

        $_SESSION['sucesso_remocao'] = 'Remoção realizada';
        header('Location: meus_eventos.php');
        exit;
    }
    catch(PDOException $e){
        $pdo -> rollback();
        $_SESSION['erro_remocao'] = "Erro ao remover evento. Tente novamente";
        header('Location: meus_eventos.php');
        exit;
    }
};
header('Location: meus_eventos.php');
exit;

?>