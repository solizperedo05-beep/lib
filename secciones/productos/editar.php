<?php include("../../bd.php"); 
if(isset($_GET["txtID"])){
    $txtID = (isset($_GET["txtID"])) ? $_GET["txtID"] : "";
    $sentencia = $conexion->prepare("SELECT * FROM products WHERE product_id = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $registro = $sentencia->fetch(PDO::FETCH_LAZY);

    if($registro){
        $product_name = $registro["product_name"];
        $foto = $registro["foto"];
        $model_year = $registro["model_year"];
        $price = $registro["price"];
        $category_id = $registro["category_id"];
    }
}

if($_POST){
    
    $txtID = (isset($_POST["product_id"])) ? $_POST["product_id"] : "";
    $product_name = (isset($_POST["product_name"])) ? $_POST["product_name"] : "";
    $foto = (isset($_FILES["foto"]["name"])) ? $_FILES["foto"]["name"] : "";
    $model_year = (isset($_POST["model_year"])) ? $_POST["model_year"] : "";
    $price = (isset($_POST["price"])) ? $_POST["price"] : "";
    $category_id = (isset($_POST["category_id"])) ? $_POST["category_id"] : "";

    $sentencia=$conexion->prepare("UPDATE products SET
    product_name=:product_name,
    model_year=:model_year,
    price=:price,
    category_id=:category_id WHERE product_id=:id");

    $sentencia->bindParam(":product_name",$product_name);
    $sentencia->bindParam(":model_year",$model_year);
    $sentencia->bindParam(":price",$price);
    $sentencia->bindParam(":category_id",$category_id);
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

    $foto=isset($_FILES["foto"]["name"])?$_FILES["foto"]["name"]:"";

    $ffecha_=new DateTime();
    $nombreArchivo_foto=($foto!="")?$ffecha_->getTimestamp()."_".$_FILES["foto"]["name"]:"";

    $tmp_foto=$_FILES["foto"]['tmp_name'];
    if($tmp_foto!=""){
        move_uploaded_file($tmp_foto,"./imagen/".$nombreArchivo_foto);
      
        $sentencia=$conexion->prepare("SELECT foto FROM products WHERE product_id=:id");
        $sentencia->bindParam(":id",$txtID);
        $sentencia->execute();
        $registro_recuperado = $sentencia->fetch(PDO::FETCH_LAZY);
       
        if(isset($registro_recuperado["foto"]) && $registro_recuperado["foto"]!=""){
            if(file_exists("./imagen/".$registro_recuperado["foto"])){
                unlink("./imagen/".$registro_recuperado["foto"]);
            }
        }
        $sentencia=$conexion->prepare("UPDATE products SET foto=:foto WHERE product_id=:id");
        $sentencia->bindParam(":foto",$nombreArchivo_foto);
        $sentencia->bindParam(":id",$txtID);
        $sentencia->execute();
    }
    $mensaje="Registro actualizado";
    header("Location:index.php?mensaje=".$mensaje);
}

$sentencia=$conexion->prepare("SELECT * FROM categories ORDER BY category_name ASC");
$sentencia->execute();
$lista_categorias = $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include("../../templates/header.php") ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4 text-primary">
                <i class="bi bi-pencil-square"></i> Editar Producto
            </h2>
            
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle"></i> Datos del producto
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="" method="post" enctype="multipart/form-data">

                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($txtID ?? ''); ?>"/>
           
                        <div class="mb-4">
                            <label for="product_id" class="form-label fw-bold">
                                <i class="bi bi-hash"></i> ID:
                            </label>
                            <input type="text" value="<?php echo $txtID; ?>" class="form-control form-control-lg bg-light" 
                                   name="product_id_display" id="product_id" disabled />
                        </div>
            
                        <div class="mb-4">
                            <label for="product_name" class="form-label fw-bold">
                                <i class="bi bi-tag"></i> Nombre del producto:
                            </label>
                            <input type="text" value="<?php echo $product_name; ?>" class="form-control form-control-lg border-2" 
                                   name="product_name" id="product_name" placeholder="Nombre del producto" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese el nombre del producto</small>
                        </div>
     
                        <div class="mb-4">
                            <label for="foto" class="form-label fw-bold">
                                <i class="bi bi-image"></i> Foto:
                            </label>
                            <div class="text-center mb-3">
                                <?php if(!empty($foto)) { ?>
                                    <img src="./imagen/<?php echo $foto; ?>" 
                                         class="img-thumbnail border-3 shadow-sm" 
                                         style="width: auto; height: auto; object-fit: cover;" 
                                         alt="Foto del producto"/>
                                <?php } else { ?>
                                    <div class="bg-light border-3 border-dashed d-flex align-items-center justify-content-center rounded"
                                         style="width: 200px; height: 200px; margin: 0 auto;">
                                        <i class="bi bi-image" style="font-size: 3rem; color: #ccc;"></i>
                                    </div>
                                <?php } ?>
                            </div>
                            <input type="file" class="form-control form-control-lg border-2" name="foto" id="foto" accept="image/*"/>
                            <small class="form-text text-muted d-block mt-2">Ingrese el archivo tipo imagen del producto (JPG, PNG, GIF)</small>
                        </div>

                        <div class="mb-4">
                            <label for="model_year" class="form-label fw-bold">
                                <i class="bi bi-calendar"></i> Modelo año:
                            </label>
                            <input type="number" value="<?php echo $model_year; ?>" class="form-control form-control-lg border-2" 
                                   name="model_year" id="model_year" placeholder="Año del modelo" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese el modelo del año</small>
                        </div>

                        <div class="mb-4">
                            <label for="price" class="form-label fw-bold">
                                <i class="bi bi-currency-dollar"></i> Precio:
                            </label>
                            <input type="number" step="0.01" value="<?php echo $price; ?>" class="form-control form-control-lg border-2" 
                                   name="price" id="price" placeholder="0.00" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese el precio del producto</small>
                        </div>

                        <div class="mb-4">
                            <label for="category_id" class="form-label fw-bold">
                                <i class="bi bi-list"></i> Categoría:
                            </label>
                            <select class="form-select form-select-lg border-2" name="category_id" id="category_id" required>
                                <option value="">Seleccione una categoría</option>
                                <?php foreach($lista_categorias as $registro) { ?>
                                <option <?php echo ($category_id==$registro['category_id'])?"selected":""; ?> 
                                    value="<?php echo $registro['category_id']; ?>">
                                    <?php echo $registro['category_name']; ?>
                                </option>
                                <?php } ?>
                            </select>
                            <small class="form-text text-muted d-block mt-2">Seleccione la categoría del producto</small>
                        </div>

                        <div class="d-flex gap-3 justify-content-end mt-5">
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


