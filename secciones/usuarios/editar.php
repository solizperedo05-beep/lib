<?php include("../../bd.php"); 
if(isset($_GET["txtID"])){
    $txtID = (isset($_GET["txtID"])) ? $_GET["txtID"] : "";
    $sentencia = $conexion->prepare("SELECT * FROM usuarios WHERE user_id = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $registro = $sentencia->fetch(PDO::FETCH_LAZY);

    if($registro){
        $usuario = $registro["usuario"];
        $clave = $registro["clave"];
        $email = $registro["email"];
        $role = $registro["role"];
    }
}

if($_POST){
    $txtID = (isset($_POST["user_id"])) ? $_POST["user_id"] : "";
    $usuario = (isset($_POST["usuario"])) ? $_POST["usuario"] : "";
    $clave = (isset($_POST["clave"])) ? $_POST["clave"] : "";
    $email = (isset($_POST["email"])) ? $_POST["email"] : "";
    $role = (isset($_POST["role"])) ? $_POST["role"] : "";

    $sentencia = $conexion->prepare("UPDATE usuarios SET
        usuario=:usuario,
        clave=:clave,
        email=:email,
        role=:role
        WHERE user_id=:id");
    $sentencia->bindParam(":usuario",$usuario);
    $sentencia->bindParam(":clave",$clave);
    $sentencia->bindParam(":email",$email);
    $sentencia->bindParam(":role",$role);
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
                <i class="bi bi-pencil-square"></i> Editar Usuario
            </h2>
            
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle"></i> Datos del usuario
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="" method="post" enctype="multipart/form-data">
                        
                     
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($txtID ?? ''); ?>"/>

                
                        <div class="mb-4">
                            <label for="user_id" class="form-label fw-bold">
                                <i class="bi bi-hash"></i> ID:
                            </label>
                            <input type="text" value="<?php echo $txtID; ?>" class="form-control form-control-lg bg-light" 
                                   name="user_id_display" id="user_id" disabled />
                        </div>

                        <div class="mb-4">
                            <label for="usuario" class="form-label fw-bold">
                                <i class="bi bi-person"></i> Usuario:
                            </label>
                            <input type="text" value="<?php echo $usuario ?? ''; ?>" class="form-control form-control-lg border-2" 
                                   name="usuario" id="usuario" placeholder="Nombre de usuario" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese el nombre de usuario</small>
                        </div>
                        
                   
                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">
                                <i class="bi bi-lock"></i> Contraseña:
                            </label>
                            <input type="password" value="<?php echo $clave ?? ''; ?>" class="form-control form-control-lg border-2" 
                                   name="clave" id="clave" placeholder="clave" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese la contraseña</small>
                        </div>
                        
               
                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">
                                <i class="bi bi-envelope"></i> Email:
                            </label>
                            <input type="email" value="<?php echo $email ?? ''; ?>" class="form-control form-control-lg border-2" 
                                   name="email" id="email" placeholder="Correo electrónico" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese el correo electrónico</small>
                        </div>
                        
                     
                        <div class="mb-4">
                            <label for="role" class="form-label fw-bold">
                                <i class="bi bi-briefcase"></i> Rol:
                            </label>
                            <select name="role" id="role" class="form-select form-select-lg" required>
                                <option value="USUARIO" <?php if(isset($role) && $role==='USUARIO') echo 'selected'; ?>>USUARIO</option>
                                <option value="ADMINISTRADOR" <?php if(isset($role) && $role==='ADMINISTRADOR') echo 'selected'; ?>>ADMINISTRADOR</option>
                            </select>
                            <small class="form-text text-muted d-block mt-2">Ingrese el rol</small>
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