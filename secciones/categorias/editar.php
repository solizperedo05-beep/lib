<?php include("../../bd.php"); 
if(isset($_GET["txtID"])){
    $txtID = (isset($_GET["txtID"])) ? $_GET["txtID"] : "";
    $sentencia = $conexion->prepare("SELECT * FROM categories WHERE category_id = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $registro = $sentencia->fetch(PDO::FETCH_LAZY);

    if($registro){
        $category_name = $registro["category_name"];
    }
}

if($_POST){
    $txtID = (isset($_POST["category_id"])) ? $_POST["category_id"] : "";
    $category_name = (isset($_POST["category_name"])) ? $_POST["category_name"] : "";

    $sentencia = $conexion->prepare("UPDATE categories SET
        category_name=:category_name
        WHERE category_id=:id");
    $sentencia->bindParam(":category_name",$category_name);
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

    $mensaje = "Registro actualizado";
    header("Location:index.php?mensaje=".$mensaje);
    exit;
}
?>
<?php include("../../templates/header.php") ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4">
                <i class="bi bi-pencil-square"></i> Editar Categoría
            </h2>
            
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle"></i> Datos de la categoría
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="" method="post" enctype="multipart/form-data">
                        

                        <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($txtID ?? ''); ?>"/>

                   
                        <div class="mb-4">
                            <label for="category_id" class="form-label fw-bold">
                                <i class="bi bi-hash"></i> ID:
                            </label>
                            <input type="text" value="<?php echo $txtID; ?>" class="form-control form-control-lg bg-light" 
                                   name="category_id_display" id="category_id" disabled />
                        </div>
                        
         
                        <div class="mb-4">
                            <label for="category_name" class="form-label fw-bold">
                                <i class="bi bi-tag"></i> Nombre de la categoría:
                            </label>
                            <input type="text" value="<?php echo $category_name ?? ''; ?>" class="form-control form-control-lg border-2" 
                                   name="category_name" id="category_name" placeholder="Nombre de la categoría" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese el nombre de la categoría</small>
                        </div>
                        
             
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="index.php" class="btn btn-secondary btn-lg">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../../templates/footer.php") ?>