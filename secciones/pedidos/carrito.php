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

        $customerId = null;
        if (!empty($_POST['customer_id'])) {
            $customerId = (int)$_POST['customer_id'];
        } else {
            $fn = trim($_POST['first_name'] ?? '');
            $ln = trim($_POST['last_name'] ?? '');

            $stc = $conexion->prepare("INSERT INTO customers (first_name, last_name) VALUES (?, ?)");
            $stc->execute([$fn, $ln]);
            $customerId = $conexion->lastInsertId();
        }

     
        $smtOrder = $conexion->prepare("INSERT INTO orders (customer_id, order_date, user_id, estado, total_amount) 
        VALUES (?,?,?,?,?)");
        $smtOrder->execute([
            $customerId,
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

        $pm = urlencode($_POST['payment_method'] ?? '');
        $tel = urlencode($_POST['telefono'] ?? '');
        $dir = urlencode($_POST['direccion'] ?? '');
        header("Location: detalle.php?order_id={$orderId}&payment_method={$pm}&telefono={$tel}&direccion={$dir}");
        exit;
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
    <h5 class="mb-0"><i class="bi bi-info-circle"></i>Datos de la compra</h5>
  </div>
    <div class="card-body">
        <div>
            <?php if(!empty ($mensaje)): ?>
                <p><strong><?= htmlspecialchars($mensaje) ?></strong></p>
            <?php endif; ?>
        </div>
        <form id="orderForm" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="first_name" class="form-label">Nombre:</label>
                <input type="text" name="first_name" id="first_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Apellido:</label>
                <input type="text" name="last_name" id="last_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="order_date" class="form-label">Fecha del pedido:</label>
                <input type="date" class="form-control" name="order_date" id="order_date" required />

            </div>

            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id'] ?? ''); ?>">

            <input type="hidden" name="estado" id="estado" value="Pendiente">

            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono:</label>
                <input type="text" class="form-control" name="telefono" id="telefono" placeholder="Número de teléfono">
            </div>
            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección:</label>
                <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Dirección de entrega">
            </div>

            <div class="mb-3">
                <label for="payment_method" class="form-label">Método de pago:</label>
                <select name="payment_method" id="payment_method" class="form-select" required>
                    <option value="">Seleccione un método de pago</option>
                    <option value="QR">QR</option>
                    <option value="TARJETA">Tarjeta</option>
                </select>
            </div>

            <div id="payment_qr" class="mb-3 text-center" style="display:none;">
                <div class="p-2 border rounded d-inline-block">
            
                    <svg xmlns="http://www.w3.org/2000/svg" width="150" height="150" fill="currentColor" class="bi bi-qr-code" viewBox="0 0 16 16">
                      <path d="M2 2h2v2H2z"/>
                      <path d="M6 0v6H0V0zM5 1H1v4h4zM4 12H2v2h2z"/>
                      <path d="M6 10v6H0v-6zm-5 1v4h4v-4zm11-9h2v2h-2z"/>
                      <path d="M10 0v6h6V0zm5 1v4h-4V1zM8 1V0h1v2H8v2H7V1zm0 5V4h1v2zM6 8V7h1V6h1v2h1V7h5v1h-4v1H7V8zm0 0v1H2V8H1v1H0V7h3v1zm10 1h-1V7h1zm-1 0h-1v2h2v-1h-1zm-4 0h2v1h-1v1h-1zm2 3v-1h-1v1h-1v1H9v1h3v-2zm0 0h3v1h-2v1h-1zm-4-1v1h1v-2H7v1z"/>
                      <path d="M7 12h1v3h4v1H7zm9 2v2h-3v-1h2v-1z"/>
                    </svg>
                </div>
                
            </div>

            <div id="payment_card" style="display:none;">
                <div class="mb-3">
                    <label for="card_number" class="form-label">Número de tarjeta:</label>
                    <input type="text" name="card_number" id="card_number" class="form-control" maxlength="19" ">
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label for="card_expiry" class="form-label">Expiración (MM/AA):</label>
                        <input type="text" name="card_expiry" id="card_expiry" class="form-control" maxlength="5" placeholder="MM/AA">
                    </div>
                    <div class="col-6">
                        <label for="card_cvc" class="form-label">CVC:</label>
                        <input type="text" name="card_cvc" id="card_cvc" class="form-control" maxlength="4" >
                    </div>
                </div>
              
            </div>

            <h3>Detalles del pedido</h3>
            <div class="table-responsive-sm">
                <table class="table table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">Producto</th>
                            <th scope="col">Cantidad</th>
                            <th scope="col">Precio Unidad</th>

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
                            <td><input type="number" step="0.01" name="items[0][price]" min="0" required class="form-control" oninput="calculateTotal()" readonly></td>

                            <input type="hidden" name="items[0][discount]" value="0">
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
            <button type="submit" class="btn btn-outline-success">Comprar</button>
            <a name="" id="" class="btn btn-primary" href="http://localhost/bike_store/index.php" role="button">Cancelar</a>
        </form>
    </div>
</div>
<?php include("../../templates/footer.php") ?>
<script>
    let itemIndex = 1;
    const productPrices = <?= json_encode($productPrices) ?>;

    const addProductId = <?= json_encode($_GET['add_product_id'] ?? '') ?>;

    function addRow(prefillProductId) {
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
            <td><input type="number" step="0.01" name="items[${itemIndex}][price]" min="0" required class="form-control" oninput="calculateTotal()" readonly></td>
            <input type="hidden" name="items[${itemIndex}][discount]" value="0">
            <td class="subtotal-cell">0.00</td>
            <td><button type="button" class="remove_row btn btn-sm btn-danger" onclick="removeRow(this)">Eliminar</button></td>
        `;
        tableBody.appendChild(newRow);
  
        if(prefillProductId) {
            const sel = newRow.querySelector('select');
            sel.value = prefillProductId;
            setPrice(sel);
            newRow.querySelector('input[name$="[quantity]"]').value = 1;
        }
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
      
        if(addProductId) {
            addRow(addProductId);
        }
        calculateTotal();


        const pm = document.getElementById('payment_method');
        const qrBlock = document.getElementById('payment_qr');
        const cardBlock = document.getElementById('payment_card');

        function togglePayment() {
            if (!pm) return;
            if (pm.value === 'QR') {
                qrBlock.style.display = '';
                cardBlock.style.display = 'none';
            } else if (pm.value === 'TARJETA') {
                qrBlock.style.display = 'none';
                cardBlock.style.display = '';
            } else {
                qrBlock.style.display = 'none';
                cardBlock.style.display = 'none';
            }
        }

        if (pm) {
            pm.addEventListener('change', togglePayment);
            togglePayment();
        }
    });
</script>