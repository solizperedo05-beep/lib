<?php include("../../bd.php");

if(isset($_GET["txtID"])){
    $txtID = (isset($_GET["txtID"])) ? $_GET["txtID"] : "";
    $sentencia = $conexion->prepare("SELECT * FROM order_items WHERE order_items_id = :id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();
    $registro = $sentencia->fetch(PDO::FETCH_LAZY);

    if($registro){
        $order_items_id = $registro["order_items_id"];
        $order_id = $registro["order_id"];
        $product_id = $registro["product_id"];
        $quantity = $registro["quantity"];
        $price = $registro["price"];
        $discount = $registro["discount"];
    }
}

if($_POST){
    $txtID = (isset($_POST["order_items_id"])) ? $_POST["order_items_id"] : "";
    $order_id = (isset($_POST["order_id"])) ? $_POST["order_id"] : "";
    $product_id = (isset($_POST["product_id"])) ? $_POST["product_id"] : "";
    $quantity = (isset($_POST["quantity"])) ? $_POST["quantity"] : 0;
    $price = (isset($_POST["price"])) ? $_POST["price"] : 0;
    $discount = (isset($_POST["discount"])) ? $_POST["discount"] : 0;

    $sentencia = $conexion->prepare("UPDATE order_items SET
        order_id = :order_id,
        product_id = :product_id,
        quantity = :quantity,
        price = :price,
        discount = :discount
        WHERE order_items_id = :id");
    $sentencia->bindParam(":order_id",$order_id);
    $sentencia->bindParam(":product_id",$product_id);
    $sentencia->bindParam(":quantity",$quantity);
    $sentencia->bindParam(":price",$price);
    $sentencia->bindParam(":discount",$discount);
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
                <i class="bi bi-pencil-square"></i> Editar artículo de pedido
            </h2>

            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle"></i> Datos del articulo del pedido
                    </h5>
                </div>

            <div class="card">
                <div class="card-body">
                    <form action="" method="post">
                        <input type="hidden" name="order_items_id" value="<?php echo isset($order_items_id) ? htmlspecialchars($order_items_id) : ''; ?>"/>

                        <div class="mb-3">
                            <label for="order_id" class="form-label fw-bold" >ID Pedido</label>
                            <input type="text" class="form-control" name="order_id" id="order_id"
                                   value="<?php echo isset($order_id) ? htmlspecialchars($order_id) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="product_id" class="form-label fw-bold">ID Producto:</label>
                            <input type="text" class="form-control" name="product_id" id="product_id"
                                   value="<?php echo isset($product_id) ? htmlspecialchars($product_id) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label fw-bold">Cantidad:</label>
                            <input type="number" class="form-control" name="quantity" id="quantity" step="1" min="0"
                                   value="<?php echo isset($quantity) ? htmlspecialchars($quantity) : '0'; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label fw-bold">Precio:</label>
                            <input type="number" class="form-control" name="price" id="price" step="0.01" min="0"
                                   value="<?php echo isset($price) ? htmlspecialchars($price) : '0.00'; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="discount" class="form-label fw-bold">Descuento:</label>
                            <input type="number" class="form-control" name="discount" id="discount" step="0.01" min="0"
                                   value="<?php echo isset($discount) ? htmlspecialchars($discount) : '0.00'; ?>">
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="index.php" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include("../../templates/footer.php") ?>