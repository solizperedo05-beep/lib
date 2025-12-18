<?php include("../../bd.php"); 
if(isset($_GET["txtID"])){
    $txtID = (isset($_GET["txtID"])) ? $_GET["txtID"] : "";
    $sentencia = $conexion->prepare("SELECT * FROM customers WHERE customers_id = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $registro = $sentencia->fetch(PDO::FETCH_LAZY);

    if($registro){
        $first_name = $registro["first_name"];
        $last_name = $registro["last_name"];
        $imagen = $registro["imagen"];
        $phone = $registro["phone"];
        $email = $registro["email"];
        $street = $registro["street"];
        $city = $registro["city"];
        $state = $registro["state"];
    }
}

if($_POST){
    $txtID = (isset($_POST["customers_id"])) ? $_POST["customers_id"] : "";
    $first_name = (isset($_POST["first_name"])) ? $_POST["first_name"] : "";
    $last_name = (isset($_POST["last_name"])) ? $_POST["last_name"] : "";
    $imagen = (isset($_FILES["imagen"]["name"])) ? $_FILES["imagen"]["name"] : "";
    $phone = (isset($_POST["phone"])) ? $_POST["phone"] : "";
    $email = (isset($_POST["email"])) ? $_POST["email"] : "";
    $street = (isset($_POST["street"])) ? $_POST["street"] : "";
    $city = (isset($_POST["city"])) ? $_POST["city"] : "";
    $state = (isset($_POST["state"])) ? $_POST["state"] : "";

    $sentencia = $conexion->prepare("UPDATE customers SET
        first_name=:first_name,
        last_name=:last_name,
        phone=:phone,
        email=:email,
        street=:street,
        city=:city,
        state=:state
        WHERE customers_id=:id");
    $sentencia->bindParam(":first_name",$first_name);
    $sentencia->bindParam(":last_name",$last_name);
    $sentencia->bindParam(":phone",$phone);
    $sentencia->bindParam(":email",$email);
    $sentencia->bindParam(":street",$street);
    $sentencia->bindParam(":city",$city);
    $sentencia->bindParam(":state",$state);
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

    $imagen=isset($_FILES["imagen"]["name"])?$_FILES["imagen"]["name"]:"";
    $ffecha_=new DateTime();
    $nombreArchivo_imagen=($imagen!="")?$ffecha_->getTimestamp()."_".$_FILES["imagen"]["name"]:"";
    $tmp_imagen=$_FILES["imagen"]['tmp_name'];
    if($tmp_imagen!=""){
        move_uploaded_file($tmp_imagen,"./imagen/".$nombreArchivo_imagen);
        $sentencia=$conexion->prepare("SELECT imagen FROM customers WHERE customers_id=:id");
        $sentencia->bindParam(":id",$txtID);
        $sentencia->execute();
        $registro_recuperado = $sentencia->fetch(PDO::FETCH_LAZY);
        if(isset($registro_recuperado["imagen"]) && $registro_recuperado["imagen"]!=""){
            if(file_exists("./imagen/".$registro_recuperado["imagen"])){
                unlink("./imagen/".$registro_recuperado["imagen"]);
            }
        }
        $sentencia=$conexion->prepare("UPDATE customers SET imagen=:imagen WHERE customers_id=:id");
        $sentencia->bindParam(":imagen",$nombreArchivo_imagen);
        $sentencia->bindParam(":id",$txtID);
        $sentencia->execute();
    }

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
                <i class="bi bi-pencil-square"></i> Editar Cliente
            </h2>
            
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle"></i> Datos del cliente
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="" method="post" enctype="multipart/form-data">
                        
                        <input type="hidden" name="customers_id" value="<?php echo htmlspecialchars($txtID ?? ''); ?>"/>

                        <div class="mb-4">
                            <label for="customers_id" class="form-label fw-bold">
                                <i class="bi bi-hash"></i> ID:
                            </label>
                            <input type="text" value="<?php echo htmlspecialchars($txtID ?? ''); ?>" class="form-control form-control-lg bg-light" 
                                   name="customers_id_display" id="customers_id" disabled />
                        </div>

                        <div class="mb-4">
                            <label for="first_name" class="form-label fw-bold">
                                <i class="bi bi-person"></i> Nombre:
                            </label>
                            <input type="text" value="<?php echo htmlspecialchars($first_name ?? ''); ?>" class="form-control form-control-lg border-2" 
                                   name="first_name" id="first_name" placeholder="Nombre" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese el nombre</small>
                        </div>

                        <div class="mb-4">
                            <label for="last_name" class="form-label fw-bold">
                                <i class="bi bi-person"></i> Apellido:
                            </label>
                            <input type="text" value="<?php echo htmlspecialchars($last_name ?? ''); ?>" class="form-control form-control-lg border-2" 
                                   name="last_name" id="last_name" placeholder="Apellido" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese el apellido</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="imagen" class="form-label">Imagen:</label>
                            

                            <div id="imagePreview" class="text-center mb-3">
                                <?php if(!empty($imagen)) { ?>
                                    <img src="./imagen/<?php echo $imagen; ?>" 
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

                            <br>
                            <input type="file" class="form-control" name="imagen" id="imagen" aria-describedby="helpId" 
                                   placeholder="Imagen del cliente" onchange="previewImage(this)">
                            <small id="helpId" class="form-text text-muted">Seleccione la imagen del cliente (JPG, PNG, GIF)</small>
                        </div>
                        
                      


                        <div class="mb-4">
                            <label for="phone" class="form-label fw-bold">
                                <i class="bi bi-telephone"></i> Teléfono:
                            </label>
                            <input type="text" value="<?php echo htmlspecialchars($phone ?? ''); ?>" class="form-control form-control-lg border-2" 
                                   name="phone" id="phone" placeholder="Teléfono" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese el teléfono</small>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">
                                <i class="bi bi-envelope"></i> Email:
                            </label>
                            <input type="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" class="form-control form-control-lg border-2" 
                                   name="email" id="email" placeholder="Correo electrónico" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese el correo electrónico</small>
                        </div>

                        <div class="mb-4">
                            <label for="street" class="form-label fw-bold">
                                <i class="bi bi-geo-alt"></i> Dirección:
                            </label>
                            <input type="text" value="<?php echo htmlspecialchars($street ?? ''); ?>" class="form-control form-control-lg border-2" 
                                   name="street" id="street" placeholder="Dirección" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese la dirección</small>
                        </div>

                        <div class="mb-4">
                            <label for="city" class="form-label fw-bold">
                                <i class="bi bi-building"></i> Ciudad:
                            </label>
                            <input type="text" value="<?php echo htmlspecialchars($city ?? ''); ?>" class="form-control form-control-lg border-2" 
                                   name="city" id="city" placeholder="Ciudad" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese la ciudad</small>
                        </div>

                        <div class="mb-4">
                            <label for="state" class="form-label fw-bold">
                                <i class="bi bi-map"></i> Estado:
                            </label>
                            <input type="text" value="<?php echo htmlspecialchars($state ?? ''); ?>" class="form-control form-control-lg border-2" 
                                   name="state" id="state" placeholder="Estado" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese el estado</small>
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

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = 'auto';
            img.style.border = '2px solid #0d6efd';
            img.style.borderRadius = '5px';
            preview.appendChild(img);
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include("../../templates/footer.php") ?>