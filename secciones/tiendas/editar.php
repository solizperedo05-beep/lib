<?php
include("../../bd.php"); 
if(isset($_GET["txtID"])){
    $txtID = (isset($_GET["txtID"])) ? $_GET["txtID"] : "";
    $sentencia = $conexion->prepare("SELECT * FROM stores WHERE store_id = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $registro = $sentencia->fetch(PDO::FETCH_LAZY);

    if($registro){
        $store_name = $registro["store_name"];
        $phone = $registro["phone"];
        $email = $registro["email"];
        $street = $registro["street"];
        $city = $registro["city"];
        $state = $registro["state"];
        $estado = $registro["estado"];
    }
  }

if($_POST){
    $txtID = (isset($_POST["store_id"])) ? $_POST["store_id"] : "";
    $store_name = (isset($_POST["store_name"])) ? $_POST["store_name"] : "";
    $phone = (isset($_POST["phone"])) ? $_POST["phone"] : "";
    $email = (isset($_POST["email"])) ? $_POST["email"] : "";
    $street = (isset($_POST["street"])) ? $_POST["street"] : "";
    $city = (isset($_POST["city"])) ? $_POST["city"] : "";
    $state = (isset($_POST["state"])) ? $_POST["state"] : "";
    $estado = (isset($_POST["estado"])) ? $_POST["estado"] : "";

    $sentencia=$conexion->prepare("UPDATE stores SET store_name=:store_name,phone=:phone,email=:email,street=:street,city=:city,
    state=:state,estado=:estado WHERE store_id=:id");
    $sentencia->bindParam(":store_name",$store_name);
    $sentencia->bindParam(":phone",$phone);
    $sentencia->bindParam(":email",$email);
    $sentencia->bindParam(":street",$street);
    $sentencia->bindParam(":city",$city);
    $sentencia->bindParam(":state",$state);
    $sentencia->bindParam(":estado",$estado);
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

    $mensaje="Registro actualizado";
    header("Location:index.php?mensaje=".$mensaje);
    exit;
  }
?>
<?php include("../../templates/header.php") ?>
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
          <h2 class="mb-4 text-primary"><i class="bi bi-pencil-square"></i> Editar Tienda</h2>            
            <div class="card shadow-lg border-0">
              <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Datos de la tienda</h5>
              </div>
              <div class="card-body p-4">
                <form action="" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="store_id" value="<?php echo htmlspecialchars($txtID ?? ''); ?>"/>
                        <div class="mb-4">
                            <label for="store_id" class="form-label fw-bold"><i class="bi bi-hash"></i> ID:
                            </label>
                            <input type="text" value="<?php echo $txtID; ?>" class="form-control form-control-lg bg-light" name="store_id_display" id="store_id" disabled />
                        </div>
                        <div class="mb-4">
                            <label for="store_name" class="form-label fw-bold">
                                <i class="bi bi-shop"></i> Nombre de la tienda:
                            </label>
                            <input type="text" value="<?php echo htmlspecialchars($store_name ?? ''); ?>" class="form-control form-control-lg border-2" 
                                   name="store_name" id="store_name" placeholder="Nombre de la tienda" required/>

                        </div>
                        <div class="mb-4">
                            <label for="phone" class="form-label fw-bold">
                                <i class="bi bi-telephone"></i> Teléfono:
                            </label>
                            <input type="text" value="<?php echo htmlspecialchars($phone ?? ''); ?>" class="form-control form-control-lg border-2" 
                                   name="phone" id="phone" placeholder="Teléfono"/>
                        
                        </div>
                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">
                                <i class="bi bi-envelope"></i> Email:
                            </label>
                            <input type="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" class="form-control form-control-lg border-2" 
                                   name="email" id="email" placeholder="Email"/>
                       
                        </div>
                        <div class="mb-4">
                            <label for="street" class="form-label fw-bold">
                                <i class="bi bi-geo-alt"></i> Calle / Dirección:
                            </label>
                            <input type="text" value="<?php echo htmlspecialchars($street ?? ''); ?>" class="form-control form-control-lg border-2" 
                                   name="street" id="street" placeholder="Dirección"/>
                           
                        </div>
                        <div class="mb-4">
                            <label for="city" class="form-label fw-bold">
                                <i class="bi bi-building"></i> Ciudad:
                            </label>
                            <input type="text" value="<?php echo htmlspecialchars($city ?? ''); ?>" class="form-control form-control-lg border-2" 
                                   name="city" id="city" placeholder="Ciudad"/>
                        
                        </div>
                        <div class="mb-4">
                            <label for="state" class="form-label fw-bold">
                                <i class="bi bi-flag"></i> State:
                            </label>
                            <input type="text" value="<?php echo htmlspecialchars($state ?? ''); ?>" class="form-control form-control-lg border-2" 
                                   name="state" id="state" placeholder="State"/>
                            
                        </div>
                        <div class="mb-4">
                            <label for="estado" class="form-label fw-bold">
                                <i class="bi bi-toggle-on"></i> Estado:
                            </label>
                            <select class="form-select form-select-lg border-2" name="estado" id="estado">
                                <option value="activo" <?php echo (isset($estado) && $estado=='activo')?'selected':''; ?>>activo</option>
                                <option value="inactivo" <?php echo (isset($estado) && $estado=='inactivo')?'selected':''; ?>>inactivo</option>
                            </select>

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