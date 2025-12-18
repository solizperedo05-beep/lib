<?php include("../../bd.php"); 
if($_POST){
    $product_name=(isset($_POST["product_name"])?$_POST["product_name"]:"");
    $foto=(isset($_FILES["foto"]['name'])?$_FILES["foto"]['name']:"");
    $model_year=(isset($_POST["model_year"])?$_POST["model_year"]:"");
    $price=(isset($_POST["price"])?$_POST["price"]:"");
    $category_id=(isset($_POST["category_id"])?$_POST["category_id"]:"");
    $sentencia=$conexion->prepare("INSERT INTO products (product_name,foto,model_year,price,category_id)
    VALUES (:product_name,:foto,:model_year,:price,:category_id)");
    $sentencia->bindParam(":product_name",$product_name);

    $fecha_=new DateTime();
    $nombreArchivo_foto=($foto!="")?$fecha_->getTimestamp()."_".$_FILES["foto"]['name']:"";
    $tmp_foto=$_FILES["foto"]['tmp_name'];
    if($tmp_foto!=""){
        move_uploaded_file($tmp_foto,"./imagen/".$nombreArchivo_foto);
    }
    $sentencia->bindParam(":foto",$nombreArchivo_foto);
    $sentencia->bindParam(":model_year",$model_year);
    $sentencia->bindParam(":price",$price);
    $sentencia->bindParam(":category_id",$category_id);
    $sentencia->execute();
    $memsaje="Registro agregado";
    header("Location:index.php?mensaje=".$memsaje);
}
$sentencia=$conexion->prepare("SELECT * FROM categories ORDER BY category_name ASC");
$sentencia->execute();
$lista_categorias = $sentencia->fetchAll(PDO::FETCH_ASSOC);

?>
<?php include("../../templates/header.php") ?>
<br> <br>
<div class= "card">
    <div class="card-header">Datos del producto</div>
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="product_name" class="form-label">Nombre del producto:</label>
                <input type="text" class="form-control" name="product_name" id="product_name" aria-describedby="helpId" 
                placeholder="Nombre del producto">
                <small id="helpId" class="form-text text-muted">ingrese el nombre del producto</small>
            </div>
            <div class="mb-3">
                <label for="foto" class="form-label">Foto:</label>
                <input type="file" class="form-control" name="foto" id="foto" aria-describedby="helpId" 
                placeholder="Foto del producto">
                <small id="helpId" class="form-text text-muted">Seleccione la foto del producto</small>
            </div>
            <div class="mb-3">
                <label for="model_year" class="form-label">Modelo año:</label>
                <input type="number" class="form-control" name="model_year" id="model_year" aria-describedby="helpId"
                placeholder="Modelo del año">
                <small id="helpId" class="form-text text-muted">Ingrese el modelo del año</small>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Precio:</label>
                <input type="number" step="0.01" class="form-control" name="price" id="price" aria-describedby="helpId" 
                placeholder="Precio">
                <small id="helpId" class="form-text text-muted">Ingrese el precio del producto</small>
            </div>      
            <div class="mb-3">
                <label for="category_id" class="form-label">Categoria:</label>
                <select class="form-text text-muted" name="category_id" id="category_id">
                    <option selected>seleccione una opcion</option>
                    <?php foreach($lista_categorias as $registro) { ?>
                    <option value="<?php echo $registro['category_id']; ?>">
                        <?php echo $registro['category_name']; ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-outline-success">agregar registro</button>
            <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>
        </form>
    </div>
</div>
<?php include("../../templates/footer.php") ?>