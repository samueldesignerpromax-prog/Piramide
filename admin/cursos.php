<?php
require_once '../includes/header.php';
require_once '../includes/auth.php';
verificarAdmin();

$database = new Database();
$db = $database->getConnection();

// Processar formulário
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $carga_horaria = $_POST['carga_horaria'];
    $categoria_id = $_POST['categoria_id'];
    $destaque = isset($_POST['destaque']) ? 1 : 0;
    
    if(isset($_POST['id']) && $_POST['id'] > 0) {
        // Atualizar
        $id = $_POST['id'];
        $query = "UPDATE cursos SET titulo=:titulo, descricao=:descricao, preco=:preco, 
                  carga_horaria=:carga_horaria, categoria_id=:categoria_id, destaque=:destaque 
                  WHERE id=:id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
    } else {
        // Inserir
        $query = "INSERT INTO cursos (titulo, descricao, preco, carga_horaria, categoria_id, destaque) 
                  VALUES (:titulo, :descricao, :preco, :carga_horaria, :categoria_id, :destaque)";
        $stmt = $db->prepare($query);
    }
    
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':preco', $preco);
    $stmt->bindParam(':carga_horaria', $carga_horaria);
    $stmt->bindParam(':categoria_id', $categoria_id);
    $stmt->bindParam(':destaque', $destaque);
    
    if($stmt->execute()) {
        $sucesso = "Curso salvo com sucesso!";
    } else {
        $erro = "Erro ao salvar curso!";
    }
}

// Excluir curso
if(isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $query = "DELETE FROM cursos WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    if($stmt->execute()) {
        $sucesso = "Curso excluído com sucesso!";
    }
}

// Buscar curso para edição
$curso_editar = null;
if(isset($_GET['editar'])) {
    $id = $_GET['editar'];
    $query = "SELECT * FROM cursos WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $curso_editar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Buscar categorias
$query_cat = "SELECT * FROM categorias";
$stmt_cat = $db->prepare($query_cat);
$stmt_cat->execute();
$categorias = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

// Listar cursos
$query = "SELECT c.*, cat.nome as categoria_nome 
          FROM cursos c 
          LEFT JOIN categorias cat ON c.categoria_id = cat.id 
          ORDER BY c.data_criacao DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4><?php echo $curso_editar ? 'Editar Curso' : 'Novo Curso'; ?></h4>
            </div>
            <div class="card-body">
                <?php if(isset($sucesso)): ?>
                    <div class="alert alert-success"><?php echo $sucesso; ?></div>
                <?php endif; ?>
                <?php if(isset($erro)): ?>
                    <div class="alert alert-danger"><?php echo $erro; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <?php if($curso_editar): ?>
                        <input type="hidden" name="id" value="<?php echo $curso_editar['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" 
                               value="<?php echo $curso_editar ? $curso_editar['titulo'] : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3" required><?php echo $curso_editar ? $curso_editar['descricao'] : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="preco" class="form-label">Preço (R$)</label>
                        <input type="number" step="0.01" class="form-control" id="preco" name="preco" 
                               value="<?php echo $curso_editar ? $curso_editar['preco'] : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="carga_horaria" class="form-label">Carga Horária (horas)</label>
                        <input type="number" class="form-control" id="carga_horaria" name="carga_horaria" 
                               value="<?php echo $curso_editar ? $curso_editar['carga_horaria'] : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="categoria_id" class="form-label">Categoria</label>
                        <select class="form-control" id="categoria_id" name="categoria_id">
                            <option value="">Selecione...</option>
                            <?php foreach($categorias as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo ($curso_editar && $
