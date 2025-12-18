<?php include("../../bd.php"); 
if($_POST){

    $usuario=(isset($_POST["usuario"])?$_POST["usuario"]:"");
    $clave=(isset($_POST["clave"])?$_POST["clave"]:"");
    $email=(isset($_POST["email"])?$_POST["email"]:"");
    $role=(isset($_POST["role"])?$_POST["role"]:"");
    

    if($usuario == "" || $clave == "" || $email == "" || $role == "") {
        $error = "Todos los campos son obligatorios";
    } else {
        
        $sentencia=$conexion->prepare("INSERT INTO usuarios(usuario,clave,email,role)
            VALUES (:usuario,:clave,:email,:role)");
        $sentencia->bindParam(":usuario",$usuario);
        $sentencia->bindParam(":clave",$clave);
        $sentencia->bindParam(":email",$email);
        $sentencia->bindParam(":role",$role);
        $sentencia->execute();
        header("Location:index.php");
        exit;
    }
}
?>
<?php include("../../templates/header.php") ?>
<br> <br>
<div class="card">
    <div class="card-header">Datos del usuario</div>
    <div class="card-body">
        <?php if(isset($error)) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i>
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>
        
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario:</label>
                <input type="text" class="form-control form-control-lg" name="usuario" id="usuario" aria-describedby="helpId"
                placeholder="Ingrese el nombre de usuario" required>
                <small id="helpId" class="form-text text-muted"></small>
            </div>

            <div class="mb-3">
                <label for="clave" class="form-label">Contraseña:</label>
                <input type="password" class="form-control form-control-lg" name="clave" id="clave" aria-describedby="helpId" 
                placeholder="Ingrese la contraseña" required>
                <small id="helpId" class="form-text text-muted"></small>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control form-control-lg" name="email" id="email" aria-describedby="helpId"
                placeholder="Ingrese el correo electrónico" required>
                <small id="helpId" class="form-text text-muted"></small>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Rol:</label>
                <select name="role" id="role" class="form-select form-select-lg" required>
                    <option value="USUARIO">USUARIO</option>
                    <option value="ADMINISTRADOR">ADMINISTRADOR</option>
                </select>
                <small id="helpId" class="form-text text-muted"></small>
            </div>

            <button type="submit" class="btn btn-success btn-lg">Guardar</button>
            <a name="" id="" class="btn btn-primary btn-lg" href="index.php" role="button">Cancelar</a>
        </form>
    </div>
</div>

<?php include("../../templates/footer.php") ?>