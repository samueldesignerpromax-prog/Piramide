<?php
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Buscar cursos em destaque
$query = "SELECT * FROM cursos WHERE destaque = 1 LIMIT 6";
$stmt = $db->prepare($query);
$stmt->execute();
$cursos_destaque = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-12 text-center mb-5">
        <h1>Bem-vindo aos Melhores Cursos Online</h1>
        <p class="lead">Aprenda com os melhores profissionais do mercado</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">Cursos em Destaque</h2>
    </div>
    
    <?php foreach($cursos_destaque as $curso): ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <?php if($curso['imagem']): ?>
                <img src="assets/img/<?php echo $curso['imagem']; ?>" class="card-img-top" alt="<?php echo $curso['titulo']; ?>">
            <?php else: ?>
                <img src="assets/img/curso-placeholder.jpg" class="card-img-top" alt="Curso">
            <?php endif; ?>
            <div class="card-body">
                <h5 class="card-title"><?php echo $curso['titulo']; ?></h5>
                <p class="card-text"><?php echo substr($curso['descricao'], 0, 100); ?>...</p>
                <p class="card-text">
                    <strong>Preço: R$ <?php echo number_format($curso['preco'], 2, ',', '.'); ?></strong>
                </p>
                <a href="pages/curso-detalhe.php?id=<?php echo $curso['id']; ?>" class="btn btn-primary">Ver Detalhes</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
