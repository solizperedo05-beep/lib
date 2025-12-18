<?php include("../../bd.php"); 
if($_POST){
    $first_name=(isset($_POST["first_name"])?$_POST["first_name"]:"");
    $last_name=(isset($_POST["last_name"])?$_POST["last_name"]:"");
    $imagen=(isset($_FILES["imagen"]['name'])?$_FILES["imagen"]['name']:"");
    $phone=(isset($_POST["phone"])?$_POST["phone"]:"");
    $email=(isset($_POST["email"])?$_POST["email"]:"");
    $street=(isset($_POST["street"])?$_POST["street"]:"");
    $city=(isset($_POST["city"])?$_POST["city"]:"");
    $state=(isset($_POST["state"])?$_POST["state"]:"");
    
    $sentencia=$conexion->prepare("INSERT INTO customers (first_name,last_name,imagen,phone,email,street,city,state)
    VALUES (:first_name,:last_name,:imagen,:phone,:email,:street,:city,:state)");
    $sentencia->bindParam(":first_name",$first_name);
    $sentencia->bindParam(":last_name",$last_name);
    $fecha_=new DateTime();
    $nombreArchivo_imagen=($imagen!="")?$fecha_->getTimestamp()."_".$_FILES["imagen"]['name']:"";
    $tmp_imagen=$_FILES["imagen"]['tmp_name'];
    if($tmp_imagen!=""){
        if(!is_dir("./imagen/")) mkdir("./imagen/", 0755, true);
        move_uploaded_file($tmp_imagen,"./imagen/".$nombreArchivo_imagen);
    }
    $sentencia->bindParam(":imagen",$nombreArchivo_imagen);
    $sentencia->bindParam(":phone",$phone);
    $sentencia->bindParam(":email",$email);
    $sentencia->bindParam(":street",$street);
    $sentencia->bindParam(":city",$city);
    $sentencia->bindParam(":state",$state);
    $sentencia->execute();
    $mensaje="Registro agregado";
    header("Location:index.php?mensaje=".$mensaje);
    exit;
}
?>
<?php include("../../templates/header.php") ?>
<br> <br>
<div class="card">
    <div class="card-header">Datos del cliente</div>
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">
            
            <div class="mb-3">
                <label for="first_name" class="form-label">Nombre:</label>
                <input type="text" class="form-control" name="first_name" id="first_name" aria-describedby="helpId" 
                placeholder="Nombre del cliente" required>
                <small id="helpId" class="form-text text-muted">Ingrese el nombre del cliente</small>
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label">Apellido:</label>
                <input type="text" class="form-control" name="last_name" id="last_name" aria-describedby="helpId" 
                placeholder="Apellido del cliente" required>
                <small id="helpId" class="form-text text-muted">Ingrese el apellido del cliente</small>
            </div>

            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen:</label>
                <div id="imagePreview" class="text-center mt-3"></div>
                <br>
                <input type="file" class="form-control" name="imagen" id="imagen" aria-describedby="helpId" 
                placeholder="Imagen del cliente" onchange="previewImage(this)">
                <small id="helpId" class="form-text text-muted">Seleccione la imagen del cliente (JPG, PNG, GIF)</small>
                
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Teléfono:</label>
                <input type="text" class="form-control" name="phone" id="phone" aria-describedby="helpId"
                placeholder="Teléfono" required>
                <small id="helpId" class="form-text text-muted">Ingrese el teléfono</small>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" name="email" id="email" aria-describedby="helpId" 
                placeholder="Email" required>
                <small id="helpId" class="form-text text-muted">Ingrese el correo electrónico</small>
            </div>

            <div class="mb-3">
                <label for="street" class="form-label">Dirección:</label>
                <input type="text" class="form-control" name="street" id="street" aria-describedby="helpId"
                placeholder="Dirección" required>
                <small id="helpId" class="form-text text-muted">Ingrese la dirección</small>
            </div>

            <div class="mb-3">
                <label for="city" class="form-label">Ciudad:</label>
                <input type="text" class="form-control" name="city" id="city" aria-describedby="helpId" 
                placeholder="Ciudad" required>
                <small id="helpId" class="form-text text-muted">Ingrese la ciudad</small>
            </div>

            <div class="mb-3">
                <label for="state" class="form-label">Estado:</label>
                <input type="text" class="form-control" name="state" id="state" aria-describedby="helpId" 
                placeholder="Estado" required>
                <small id="helpId" class="form-text text-muted">Ingrese el estado</small>
            </div>

            <button type="submit" class="btn btn-success"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bookmarks-fill" viewBox="0 0 16 16">
  <path d="M2 4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v11.5a.5.5 0 0 1-.777.416L7 13.101l-4.223 2.815A.5.5 0 0 1 2 15.5z"/>
  <path d="M4.268 1A2 2 0 0 1 6 0h6a2 2 0 0 1 2 2v11.5a.5.5 0 0 1-.777.416L13 13.768V2a1 1 0 0 0-1-1z"/>
</svg>  Guardar</button>
            <a class="btn btn-primary" href="index.php" role="button">Cancelar</a>
        </form>
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
            img.style.maxWidth = '200px';
            img.style.border = '2px solid #0d6efd';
            img.style.borderRadius = '5px';
            preview.appendChild(img);
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include("../../templates/footer.php") ?>