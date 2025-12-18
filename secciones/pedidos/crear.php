<?php include("../../bd.php"); 
if($_POST){

    $customer_id=(isset($_POST["customer_id"])?$_POST["customer_id"]:"");
    $order_date=(isset($_POST["order_date"])?$_POST["order_date"]:"");
    $user_id=(isset($_POST["user_id"])?$_POST["user_id"]:"");
    $estado=(isset($_POST["estado"])?$_POST["estado"]:"");
    $total_amount=(isset($_POST["total_amount"])?$_POST["total_amount"]:"");
    

    $sentencia=$conexion->prepare("INSERT INTO orders (customer_id,order_date,user_id,estado,total_amount)
    VALUES (:customer_id,:order_date,:user_id,:estado,:total_amount)");
    
 
    $sentencia->bindParam(":customer_id",$customer_id);
    $sentencia->bindParam(":order_date",$order_date);
    $sentencia->bindParam(":user_id",$user_id);
    $sentencia->bindParam(":estado",$estado);
    $sentencia->bindParam(":total_amount",$total_amount);
    $sentencia->execute();
    $mensaje="Registro agregado";
    header("Location:index.php?mensaje=".$mensaje);
    exit;
}
?>
<?php include("../../templates/header.php") ?>
<br> <br>
<div class="card">
    <div class="card-header">Datos del pedido</div>
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">
            
            <div class="mb-3">
                <label for="customer_id" class="form-label">Cliente ID:</label>
                <input type="text" class="form-control" name="customer_id" id="customer_id" aria-describedby="helpId" 
                placeholder="ID del cliente" required>
                <small id="helpId" class="form-text text-muted">Ingrese el ID del cliente</small>
            </div>

            <div class="mb-3">
                <label for="order_date" class="form-label">Fecha de pedido:</label>
                <input type="date" class="form-control" name="order_date" id="order_date" aria-describedby="helpId" 
                placeholder="Fecha del pedido" required>
                <small id="helpId" class="form-text text-muted">Ingrese la fecha del pedido</small>
            </div>

            <div class="mb-3">
                <label for="user_id" class="form-label">Usuario ID:</label>
                <input type="text" class="form-control" name="user_id" id="user_id" aria-describedby="helpId"
                placeholder="ID del usuario" required>
                <small id="helpId" class="form-text text-muted">Ingrese el ID del usuario</small>
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label">Estado:</label>
                <input type="text" class="form-control" name="estado" id="estado" aria-describedby="helpId" 
                placeholder="Estado" required>
                <small id="helpId" class="form-text text-muted">Ingrese el estado del pedido</small>
            </div>

            <div class="mb-3">
                <label for="total_amount" class="form-label">Total:</label>
                <input type="number" step="0.01" class="form-control" name="total_amount" id="total_amount" aria-describedby="helpId" 
                placeholder="Total" required>
                <small id="helpId" class="form-text text-muted">Ingrese el total del pedido</small>
            </div>

            <button type="submit" class="btn btn-success">Guardar</button>
            <a class="btn btn-primary" href="index.php" role="button">Cancelar</a>
        </form>
    </div>
</div>

<?php include("../../templates/footer.php") ?>