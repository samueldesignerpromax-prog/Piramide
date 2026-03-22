<?php
function verificarLogin() {
    if(!isset($_SESSION['usuario_id'])) {
        header("Location: pages/login.php");
        exit();
    }
}

function verificarAdmin() {
    verificarLogin();
    if($_SESSION['usuario_tipo'] != 'admin') {
        header("Location: index.php");
        exit();
    }
}

function isAdmin() {
    return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] == 'admin';
}
?>
