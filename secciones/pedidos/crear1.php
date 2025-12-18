<?php include("../../bd.php");

$customers = $conexion->query("SELECT customers_id, CONCAT(first_name,' ',last_name) AS name FROM customers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$product=$conexion->query("SELECT product_id, product_name, price FROM products 
ORDER BY product_name ")->fetchAll(PDO::FETCH_ASSOC);

$productPrices = [];
foreach ($product as $prod) {
    $productPrices[$prod['product_id']] = $prod['price'];
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    try {
        $conexion->beginTransaction();
      
        $smtOrder = $conexion->prepare("INSERT INTO orders (customer_id, order_date, user_id, estado, total_amount) 
        VALUES (?,?,?,?,?)");
        $smtOrder->execute([
            $_POST['customer_id'],
            $_POST['order_date'],
            $_POST['user_id'],
            $_POST['estado'],
            $_POST['total_amount']
        ]);

        $orderId = $conexion->lastInsertId();
       
        $smtItem = $conexion->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, discount) 
        VALUES (?,?,?,?,?)");
        foreach ($_POST['items'] as $item) {
            $smtItem->execute([
                $orderId,
                $item['product_id'],
                $item['quantity'],
                $item['price'],
                $item['discount']
            ]);
        }
        $conexion->commit();
        $mensaje = "Pedido creado exitosamente";
    }
    catch (Exception $e) {
        $conexion->rollBack();
        $mensaje="Se produjo un error al crear el pedido: " . $e->getMessage();
    }
}
?>
<?php include("../../templates/header.php") ?>
<br><br>
<div class="card">
    <div class="card-header bg-primary text-white py-3">
    <h5 class="mb-0"><i class="bi bi-info-circle"></i>Crear nuevo pedido</h5>
  </div>
    
    <div class="card-body">
        <div>
            <?php if(!empty ($mensaje)): ?>
                <p><strong><?= htmlspecialchars($mensaje) ?></strong></p>
            <?php endif; ?>
        </div>
        <form id="orderForm" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="customer_id" class="form-label">Cliente:</label>
                <select name="customer_id" id="customer_id" required class="form-select">
                    <option value="">Seleccione un cliente</option>
                    <?php foreach($customers as $cust): ?>
                        <option value="<?= $cust['customers_id'] ?>"><?= htmlspecialchars($cust['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="order_date" class="form-label">Fecha del pedido:</label>
                <input type="date" class="form-control" name="order_date" id="order_date" required />
                <small class="form-text text-muted">Ingrese la fecha del pedido</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Usuario:</label>
                <div class="form-control"><?php echo htmlspecialchars($_SESSION['usuario'] ?? ''); ?></div>
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="estado" class="form-label">Estado:</label>
                <input type="text" value="Pendiente" class="form-control" name="estado" id="estado" required/>
            </div>

                <h3>Pedido detalle</h3>
                <div class="table-responsive-sm">
                    <table class="table table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th scope="col">Producto</th>
                                <th scope="col">Cantidad</th>
                                <th scope="col">Precio Unidad</th>
                                <th scope="col">Descuento (%)</th> 
                                <th scope="col">Sub Total</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                             <tr>
                                <td>
                                    <select name="items[0][product_id]" onchange="setPrice(this)" required class="form-select">
                                        <option value="">Seleccione un producto</option>
                                        <?php foreach($product as $prod): ?>
                                            <option value="<?= $prod['product_id'] ?>"><?= htmlspecialchars($prod['product_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="number" name="items[0][quantity]" min="1" required class="form-control" oninput="calculateTotal()"></td>
                                <td><input type="number" step="0.01" name="items[0][price]" min="0" required class="form-control" oninput="calculateTotal()"></td>
                                <td><input type="number" step="0.01" name="items[0][discount]" min="0" class="form-control" oninput="calculateTotal()"></td>
                                <td class="subtotal-cell">0.00</td>
                                <td><button type="button" class="remove_row btn btn-sm btn-danger" onclick="removeRow(this)">Eliminar</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                <button type="button" class="add_row btn btn-outline-primary" onclick="addRow()"><i class="bi bi-plus-circle me-2"></i>Agregar item</button>
                <div class="total-box mt-3">
                    Monto total: Bs.<span id="totalAmount">0.00</span>
                </div>
                <input type="hidden" name="total_amount" id="total_amount" value="0.00">
                <br>
                <button type="submit" class="btn btn-outline-success">Guardar orden</button>
                <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>
        </form>
    </div>
</div>
<?php include("../../templates/footer.php") ?>
<script>
    let itemIndex = 1;
    const productPrices = <?= json_encode($productPrices) ?>;

    function addRow() {
        const tableBody = document.querySelector('table tbody');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select name="items[${itemIndex}][product_id]" onchange="setPrice(this)" required class="form-select">
                    <option value="">--Seleccione un producto--</option>
                    <?php foreach($product as $prod): ?>
                        <option value="<?= $prod['product_id'] ?>"><?= htmlspecialchars($prod['product_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><input type="number" name="items[${itemIndex}][quantity]" min="1" required class="form-control" oninput="calculateTotal()"></td>
            <td><input type="number" step="0.01" name="items[${itemIndex}][price]" min="0" required class="form-control" oninput="calculateTotal()"></td>
            <td><input type="number" step="0.01" name="items[${itemIndex}][discount]" min="0" class="form-control" oninput="calculateTotal()"></td>
            <td class="subtotal-cell">0.00</td>
            <td><button type="button" class="remove_row btn btn-sm btn-danger" onclick="removeRow(this)">Eliminar</button></td>
        `;
        tableBody.appendChild(newRow);
        itemIndex++;
    }
    function removeRow(button) {
        const row = button.closest('tr');
        row.remove();
        calculateTotal();
    }
    function setPrice(selectElement) {
        const productId = selectElement.value;
        const priceInput = selectElement.closest('tr').querySelector('input[name$="[price]"]');
        if (productPrices[productId] !== undefined) {
            priceInput.value = parseFloat(productPrices[productId]).toFixed(2);
        } else {
            priceInput.value = '';
        }
        calculateTotal();
    }

    function calculateTotal() {
        const rows = document.querySelectorAll('table tbody tr');
        let grandTotal = 0;
        rows.forEach(row => {
            const qtyInput = row.querySelector('input[name$="[quantity]"]');
            const priceInput = row.querySelector('input[name$="[price]"]');
            const discountInput = row.querySelector('input[name$="[discount]"]');
            const subtotalCell = row.querySelector('.subtotal-cell');

            const q = parseFloat(qtyInput?.value) || 0;
            const p = parseFloat(priceInput?.value) || 0;
            const d = parseFloat(discountInput?.value) || 0; 
            const subtotal = p * q;
            const descuento = (d / 100) * subtotal;
            const subtotalFinal = subtotal - descuento;

            subtotalCell.textContent = subtotalFinal.toFixed(2);
            grandTotal += subtotalFinal;
        });
        document.getElementById('totalAmount').textContent = grandTotal.toFixed(2);
        document.getElementById('total_amount').value = grandTotal.toFixed(2);
    }

 
    document.addEventListener('DOMContentLoaded', function(){
     
        const firstSelect = document.querySelector('table tbody tr select');
        if(firstSelect) {
            setPrice(firstSelect);
        }
        calculateTotal();
    });
</script>