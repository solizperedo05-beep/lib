<?php include("../../bd.php"); 
if(isset($_GET["txtID"])){
    $txtID = (isset($_GET["txtID"])) ? $_GET["txtID"] : "";
    $sentencia = $conexion->prepare("SELECT * FROM orders WHERE order_id = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $registro = $sentencia->fetch(PDO::FETCH_LAZY);

    if($registro){
        $order_id = $registro["order_id"];
        $customer_id = $registro["customer_id"];
        $order_date = $registro["order_date"];
        $user_id = $registro["user_id"];
        $estado = $registro["estado"];
        $total_amount = $registro["total_amount"];
    }
}

if($_POST){
    $txtID = (isset($_POST["order_id"])) ? $_POST["order_id"] : "";
    $customer_id = (isset($_POST["customer_id"])) ? $_POST["customer_id"] : "";
    $order_date = (isset($_POST["order_date"])) ? $_POST["order_date"] : "";
    $user_id = (isset($_POST["user_id"])) ? $_POST["user_id"] : "";
    $estado = (isset($_POST["estado"])) ? $_POST["estado"] : "";
    $total_amount = (isset($_POST["total_amount"])) ? $_POST["total_amount"] : "";

    $sentencia = $conexion->prepare("UPDATE orders SET
        customer_id=:customer_id,
        order_date=:order_date,
        user_id=:user_id,
        estado=:estado,
        total_amount=:total_amount
        WHERE order_id=:id");
    $sentencia->bindParam(":customer_id",$customer_id);
    $sentencia->bindParam(":order_date",$order_date);
    $sentencia->bindParam(":user_id",$user_id);
    $sentencia->bindParam(":estado",$estado);
    $sentencia->bindParam(":total_amount",$total_amount);
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
                <i class="bi bi-pencil-square"></i> Editar Pedido
            </h2>
            
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle"></i> Datos del pedido
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="" method="post" enctype="multipart/form-data">
                        
                        <input type="hidden" name="order_id" value="<?php echo ($txtID ?? ''); ?>"/>

                        <div class="mb-4">
                            <label for="order_id" class="form-label fw-bold">
                                <i class="bi bi-hash"></i> Pedido ID:
                            </label>
                            <input type="text" value="<?php echo ($txtID ?? ''); ?>" class="form-control form-control-lg bg-light" 
                                   name="order_id_display" id="order_id" disabled />
                        </div>

                        <div class="mb-4">
                            <label for="customer_id" class="form-label fw-bold">
                                <i class="bi bi-person"></i> Cliente ID:
                            </label>
                            <input type="text" value="<?php echo ($customer_id ?? ''); ?>" class="form-control form-control-lg border-2" 
                                   name="customer_id" id="customer_id" placeholder="ID del cliente" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese el ID del cliente</small>
                        </div>

                        <div class="mb-4">
                            <label for="order_date" class="form-label fw-bold">
                                <i class="bi bi-calendar"></i> Fecha de pedido:
                            </label>
                            <input type="date" value="<?php echo ($order_date ?? ''); ?>" class="form-control form-control-lg border-2" 
                                   name="order_date" id="order_date" placeholder="Fecha" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese la fecha del pedido</small>
                        </div>

                        <div class="mb-4">
                            <label for="user_id" class="form-label fw-bold">
                                <i class="bi bi-person-circle"></i> Usuario ID:
                            </label>
                            <input type="text" value="<?php echo ($user_id ?? ''); ?>" class="form-control form-control-lg border-2" 
                                   name="user_id" id="user_id" placeholder="ID del usuario" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese el ID del usuario</small>
                        </div>

                        <div class="mb-4">
                            <label for="estado" class="form-label fw-bold">
                                <i class="bi bi-tag"></i> Estado:
                            </label>
                            <input type="text" value="<?php echo ($estado ?? ''); ?>" class="form-control form-control-lg border-2" 
                                   name="estado" id="estado" placeholder="Estado" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese el estado del pedido</small>
                        </div>

                        <div class="mb-4">
                            <label for="total_amount" class="form-label fw-bold">
                                <i class="bi bi-currency-dollar"></i> Total:
                            </label>
                            <input type="number" step="0.01" value="<?php echo ($total_amount ?? ''); ?>" class="form-control form-control-lg border-2" 
                                   name="total_amount" id="total_amount" placeholder="Total" required/>
                            <small class="form-text text-muted d-block mt-2">Ingrese el total del pedido</small>
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