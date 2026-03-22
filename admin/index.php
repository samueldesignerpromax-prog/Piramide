<?php
require_once '../includes/header.php';
require_once '../includes/auth.php';
verificarAdmin();

$database = new Database();
$db = $database->getConnection();

// Estatísticas
$query_total_cursos = "SELECT COUNT(*) as total FROM cursos";
$stmt = $db->prepare($query_total_cursos);
$stmt->execute();
$total_cursos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query_total_usuarios = "SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'cliente'";
$stmt = $db->prepare($query_total_usuarios);
$stmt->execute();
$total_usuarios = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query_total_vendas = "SELECT COUNT(*) as total, SUM(valor_total) as total_valor FROM pedidos WHERE status = 'pago'";
$stmt = $db->prepare($query_total_vendas);
$stmt->execute();
$vendas = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-12">
        <h2>Painel Administrativo</h2>
        <hr>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total de Cursos</h5>
                <h2><?php echo $total_cursos; ?></h2>
                <a href="cursos.php" class="text-white">Gerenciar Cursos →</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Total de Alunos</h5>
                <h2><?php echo $total_usuarios; ?></h2>
                <a href="usuarios.php" class="text-white">Gerenciar Usuários →</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Vendas Realizadas</h5>
                <h2><?php echo $vendas['total']; ?></h2>
                <p>Total: R$ <?php echo number_format($vendas['total_valor'], 2, ',', '.'); ?></p>
                <a href="pedidos.php" class="text-white">Ver Pedidos →</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
