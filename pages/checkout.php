<?php
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_once '../config/database.php';

if(!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

if(empty($_SESSION['carrinho'])) {
    header("Location: carrinho.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Buscar cursos do carrinho
$ids = implode(',', $_SESSION['carrinho']);
$query = "SELECT * FROM cursos WHERE id IN ($ids)";
$stmt = $db->prepare($query);
$stmt->execute();
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$valor_total = 0;
foreach($cursos as $curso) {
    $valor_total += $curso['preco'];
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db->beginTransaction();
        
        // Criar pedido
        $query = "INSERT INTO pedidos (usuario_id, valor_total, status) VALUES (:usuario_id, :valor_total, 'pago')";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':usuario_id', $_SESSION['usuario_id']);
        $stmt->bindParam(':valor_total', $valor_total);
        $stmt->execute();
        
        $pedido_id = $db->lastInsertId();
        
        // Adicionar itens do pedido e matricular
        foreach($cursos as $curso) {
            // Item do pedido
            $query = "INSERT INTO pedido_itens (pedido_id, curso_id, preco_unitario) VALUES (:pedido_id, :curso_id, :preco)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':pedido_id', $pedido_id);
            $stmt->bindParam(':curso_id', $curso['id']);
            $stmt->bindParam(':preco', $curso['preco']);
            $stmt->execute();
            
            // Matrícula
            $query = "INSERT INTO matriculas (usuario_id, curso_id) VALUES (:usuario_id, :curso_id)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':usuario_id', $_SESSION['usuario_id']);
            $stmt->bindParam(':curso_id', $curso['id']);
            $stmt->execute();
        }
        
        $db->commit();
        
        // Limpar carrinho
        $_SESSION['carrinho'] = [];
        
        $sucesso = "Compra realizada com sucesso! Você já pode acessar seus cursos.";
        
    } catch(Exception $e) {
        $db->rollBack();
        $erro = "Erro ao processar compra: " . $e->getMessage();
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3>Finalizar Compra</h3>
            </div>
            <div class="card-body">
                <?php if(isset($sucesso)): ?>
                    <div class="alert alert-success">
                        <?php echo $sucesso; ?>
                        <br><br>
                        <a href="../index.php" class="btn btn-primary">Voltar para Home</a>
                    </div>
                <?php else: ?>
                    <h4>Resumo do Pedido</h4>
                    <table class="table table-bordered mb-4">
                        <thead>
                            <tr>
                                <th>Curso</th>
                                <th>Preço</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cursos as $curso): ?>
                            <tr>
                                <td><?php echo $curso['titulo']; ?></td>
                                <td>R$ <?php echo number_format($curso['preco'], 2, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-end"><strong>Total:</strong></td>
                                <td><strong>R$ <?php echo number_format($valor_total, 2, ',', '.'); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <form method="POST" action="">
                        <div class="alert alert-info">
                            <p><strong>Forma de Pagamento:</strong> Pagamento Simulado</p>
                            <p>Para fins de demonstração, o pagamento será processado sem integração real.</p>
                        </div>
                        <button type="submit" class="btn btn-success">Confirmar Pagamento</button>
                        <a href="carrinho.php" class="btn btn-secondary">Voltar ao Carrinho</a>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
