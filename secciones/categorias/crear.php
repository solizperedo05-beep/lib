<?php include("../../bd.php"); 
if($_POST){

    $category_name=(isset($_POST["category_name"])?$_POST["category_name"]:"");
    

    $sentencia=$conexion->prepare("INSERT INTO categories(category_name)
        VALUES (:category_name)");
    $sentencia->bindParam(":category_name",$category_name);
    $sentencia->execute();
    header("Location:index.php");
}
?>
<?php include("../../templates/header.php") ?>
<br> <br>
<div class="card">
    <div class="card-header">Datos de la categoría</div>
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="category_name" class="form-label">Nombre de la categoría:</label>
                <input type="text" class="form-control" name="category_name" id="category_name" aria-describedby="helpId"
                placeholder="Nombre de la categoría">
                <small id="helpId" class="form-text text-muted">Ingrese el nombre de la categoría</small>
            </div>

            <button type="submit" class="btn btn-success">Guardar</button>
            <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>
        </form>
    </div>
</div>
<?php include("../../templates/footer.php") ?>