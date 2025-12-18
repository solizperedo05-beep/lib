<?php include("../../bd.php");

if($_POST){
    $order_id = isset($_POST["order_id"]) ? $_POST["order_id"] : "";
    $product_id = isset($_POST["product_id"]) ? $_POST["product_id"] : "";
    $quantity = isset($_POST["quantity"]) ? (int)$_POST["quantity"] : 0;
    $price = isset($_POST["price"]) ? (float)$_POST["price"] : 0.0;
    $discount = isset($_POST["discount"]) ? (float)$_POST["discount"] : 0.0;

    $subtotal = $price * $quantity;
    $descuento = ($discount / 100) * $subtotal;
    $total = $subtotal - $descuento;
    $sentencia = $conexion->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, discount)
        VALUES (:order_id, :product_id, :quantity, :price, :discount)");
    $sentencia->bindParam(":order_id", $order_id);
    $sentencia->bindParam(":product_id", $product_id);
    $sentencia->bindParam(":quantity", $quantity);
    $sentencia->bindParam(":price", $price);
    $sentencia->bindParam(":discount", $discount);
    $sentencia->execute();

    $mensaje = "Artículo de pedido agregado";
    header("Location:index.php?mensaje=".$mensaje);
    exit;
}
$sentencia = $conexion->prepare("SELECT product_id, product_name, price FROM products ORDER BY CAST(product_id AS UNSIGNED) ASC");
$sentencia->execute();
$lista_product = $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include("../../templates/header.php") ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4"><i class="bi bi-plus-circle"></i> Nuevo artículo de pedido</h2>

            <div class="card">
                <div class="card-body">
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="order_id" class="form-label">ID Pedido</label>
                            <input type="number" class="form-control" name="order_id" id="order_id" required>
                        </div>

                        <div class="mb-3">
                            <label for="product_id" class="form-label">Producto:</label>
                            <select class="form-select" name="product_id" id="product_id" required>
                                <option value="">Seleccione una opción</option>
                                <?php foreach($lista_product as $registro) { ?>
                                    <option value="<?php echo htmlspecialchars($registro['product_id']); ?>"
                                            data-price="<?php echo htmlspecialchars($registro['price']); ?>">
                                        <?php echo htmlspecialchars($registro['product_id'] . ' - ' . $registro['product_name']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Cantidad</label>
                            <input type="number" class="form-control" name="quantity" id="quantity" value="1" min="0" step="1" required>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label" >Precio</label>
                            <input type="number" class="form-control" name="price" id="price" step="0.01" value="<?php echo isset($lista_product[0]['price']) ? htmlspecialchars($lista_product[0]['price']) : ''; ?>" readonly required>
                        </div>

                        <div class="mb-3">
                            <label for="discount" class="form-label">Descuento</label>
                            <input type="number" class="form-control" name="discount" id="discount" value="0.00" min="0" step="0.01">
                        </div>

                        <div class="mb-3">
                            <label for="total" class="form-label">Total</label>
                            <input type="number" class="form-control" name="total" id="total" step="0.01" value="0.00" readonly>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-success">Guardar</button>    
                            <a href="index.php" class="btn btn-primary">Cancelar</a>                            
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include("../../templates/footer.php") ?>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const select = document.getElementById('product_id');
    const priceInput = document.getElementById('price');
    const qtyInput = document.getElementById('quantity');
    const discountInput = document.getElementById('discount');
    const totalInput = document.getElementById('total');

    function updatePrice(){
        const opt = select.options[select.selectedIndex];
        const p = opt ? parseFloat(opt.getAttribute('data-price')) : 0;
        priceInput.value = isNaN(p) ? '' : p.toFixed(2);
        updateTotal();
    }

    function updateTotal(){
        const p = parseFloat(priceInput.value) || 0;
        const q = parseInt(qtyInput.value) || 0;
        const d = parseFloat(discountInput.value) || 0;
        const subtotal = p * q;
        const descuento = (d / 100) * subtotal;
        const total = subtotal - descuento;
        totalInput.value = total.toFixed(2);
    }

    if(select){
        select.addEventListener('change', updatePrice);
        updatePrice();
    }
    if(qtyInput) qtyInput.addEventListener('input', updateTotal);
    if(discountInput) discountInput.addEventListener('input', updateTotal);
});
</script>