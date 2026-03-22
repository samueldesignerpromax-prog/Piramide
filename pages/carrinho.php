<?php
require_once '../includes/header.php';
require_once '../includes/auth.php';

if(!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Inicializar carrinho se não existir
if(!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Adicionar ao carrinho
if(isset($_GET['add']) && is_numeric($_GET['add'])) {
    $curso_id = $_GET['add'];
    if(!in_array($curso_id, $_SESSION['carrinho'])) {
        $_SESSION['carrinho'][] = $curso_id;
    }
    header("Location: carrinho.php");
    exit();
}

// Remover do carrinho
if(isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $key = array_search($_GET['remove'], $_SESSION['carrinho']);
    if($key !== false) {
        unset($_SESSION['carrinho'][$key]);
        $_SESSION['carrinho'] = array_values($_SESSION['carrinho']);
    }
    header("Location: carrinho.php");
    exit();
}

// Buscar informações dos cursos no carrinho
$cursos_carrinho = [];
$valor_total = 0;

if(count($_SESSION['carrinho']) > 0) {
    $database = new Database();
    $db = $database->getConnection();
    
    $ids = implode(',', $_SESSION['carrinho']);
    $query = "SELECT * FROM cursos WHERE id IN ($ids)";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $cursos_carrinho = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($cursos_carrinho as $curso) {
        $valor_total += $curso['preco'];
    }
}
?>

<div class="row">
    <div class="col-12">
        <h2>Meu Carrinho</h2>
        
        <?php if(count($cursos_carrinho) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Preço</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cursos_carrinho as $curso): ?>
                        <tr>
                            <td><?php echo $curso['titulo']; ?></td>
                            <td>R$ <?php echo number_format($curso['preco'], 2, ',', '.'); ?></td>
                            <td>
                                <a href="?remove=<?php echo $curso['id']; ?>" class="btn btn-danger btn-sm">Remover</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-end"><strong>Total:</strong></td>
                            <td><strong>R$ <?php echo number_format($valor_total, 2, ',', '.'); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="text-end">
                <a href="checkout.php" class="btn btn-success">Finalizar Compra</a>
                <a href="cursos.php" class="btn btn-primary">Continuar Comprando</a>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Seu carrinho está vazio. <a href="cursos.php">Continue comprando</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
