<?php
require_once '../includes/header.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Buscar categorias para filtro
$query_categorias = "SELECT * FROM categorias";
$stmt_cat = $db->prepare($query_categorias);
$stmt_cat->execute();
$categorias = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

// Buscar cursos
$categoria_id = isset($_GET['categoria']) ? $_GET['categoria'] : null;
$busca = isset($_GET['busca']) ? $_GET['busca'] : null;

$query = "SELECT c.*, cat.nome as categoria_nome 
          FROM cursos c 
          LEFT JOIN categorias cat ON c.categoria_id = cat.id 
          WHERE 1=1";

if($categoria_id) {
    $query .= " AND c.categoria_id = :categoria_id";
}
if($busca) {
    $query .= " AND (c.titulo LIKE :busca OR c.descricao LIKE :busca)";
}

$query .= " ORDER BY c.data_criacao DESC";

$stmt = $db->prepare($query);

if($categoria_id) {
    $stmt->bindParam(':categoria_id', $categoria_id);
}
if($busca) {
    $busca_param = "%$busca%";
    $stmt->bindParam(':busca', $busca_param);
}

$stmt->execute();
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Filtros</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="">
                    <div class="mb-3">
                        <label for="busca" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="busca" name="busca" value="<?php echo $busca; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categorias</label>
                        <div class="list-group">
                            <a href="cursos.php" class="list-group-item list-group-item-action <?php echo !$categoria_id ? 'active' : ''; ?>">
                                Todos os Cursos
                            </a>
                            <?php foreach($categorias as $cat): ?>
                            <a href="?categoria=<?php echo $cat['id']; ?>" 
                               class="list-group-item list-group-item-action <?php echo $categoria_id == $cat['id'] ? 'active' : ''; ?>">
                                <?php echo $cat['nome']; ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Aplicar Filtros</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <h2 class="mb-4">Nossos Cursos</h2>
        <div class="row">
            <?php if(count($cursos) > 0): ?>
                <?php foreach($cursos as $curso): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $curso['titulo']; ?></h5>
                            <p class="card-text"><?php echo substr($curso['descricao'], 0, 150); ?>...</p>
                            <p class="card-text">
                                <small class="text-muted">Categoria: <?php echo $curso['categoria_nome']; ?></small><br>
                                <strong>Preço: R$ <?php echo number_format($curso['preco'], 2, ',', '.'); ?></strong>
                            </p>
                            <a href="curso-detalhe.php?id=<?php echo $curso['id']; ?>" class="btn btn-primary">Ver Detalhes</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">Nenhum curso encontrado.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
