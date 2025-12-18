<?php
include("../../bd.php");

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;
$payment_method = $_GET['payment_method'] ?? '';
$telefono = $_GET['telefono'] ?? '';
$direccion = $_GET['direccion'] ?? '';

if (!$orderId) {
    include("../../templates/header.php");
    echo "<div class='container mt-4'><div class='alert alert-warning'>Falta el parámetro order_id</div></div>";
    include("../../templates/footer.php");
    exit;
}

$stmt = $conexion->prepare("
    SELECT o.*, c.first_name, c.last_name
    FROM orders o
    LEFT JOIN customers c ON c.customers_id = o.customer_id
    WHERE o.order_id = :id
    LIMIT 1
");
$stmt->execute([':id' => $orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    include("../../templates/header.php");
    echo "<div class='container mt-4'><div class='alert alert-danger'>Pedido no encontrado.</div></div>";
    include("../../templates/footer.php");
    exit;
}

$itStmt = $conexion->prepare("
    SELECT oi.*, p.product_name
    FROM order_items oi
    LEFT JOIN products p ON p.product_id = oi.product_id
    WHERE oi.order_id = :id
");
$itStmt->execute([':id' => $orderId]);
$items = $itStmt->fetchAll(PDO::FETCH_ASSOC);

$nit = isset($_GET['nit']) ? preg_replace('/\D/', '', $_GET['nit']) : str_pad((string)random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);

if (isset($_GET['invoice_number']) && preg_replace('/\D/', '', $_GET['invoice_number']) !== '') {
    $inv = preg_replace('/\D/', '', $_GET['invoice_number']);
    $invoice_number = ltrim($inv, '0');
    if ($invoice_number === '') $invoice_number = '0';
} else {
    $invoice_number = (string)$orderId;
}

include("../../templates/header.php");
?>
<div class="container my-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Factura</h5>
        </div>
        <div class="card-body">
            <H1 class="text-center">Bike Store</H1>
            <br>
            <div class="row mb-3">
                <div class="col-12 mb-2">
                    <strong>NIT:</strong> <?php echo htmlspecialchars($nit); ?><br>
                    <strong>N° Factura:</strong> <?php echo htmlspecialchars($invoice_number); ?>
                </div>
                <div class="col-md-6">
                    <strong>Cliente:</strong><br>
                    <?php echo htmlspecialchars(trim(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')) ?: 'N/A'); ?>
                </div>
                <div class="col-md-3">
                    <strong>Fecha:</strong><br>
                    <?php echo htmlspecialchars($order['order_date'] ?? ''); ?>
                </div>
                
            </div>

            <?php if ($telefono || $direccion || $payment_method): ?>
            <div class="mb-3">
                <?php if ($telefono): ?>
                    <strong>Teléfono:</strong> <?php echo htmlspecialchars($telefono); ?><br>
                <?php endif; ?>
                <?php if ($direccion): ?>
                    <strong>Dirección:</strong> <?php echo htmlspecialchars($direccion); ?><br>
                <?php endif; ?>
                <?php if (!empty($payment_method)): ?>
                    <strong>Método de pago:</strong>
                    <?php
                        $pm = strtoupper($payment_method);
                        echo ($pm === 'QR') ? 'QR' : (($pm === 'TARJETA') ? 'Tarjeta' : htmlspecialchars($payment_method));
                    ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <h6>Detalle</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>Producto</th>
                            <th class="text-end">Cantidad</th>
                            <th class="text-end">Precio</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $calcTotal = 0;
                        foreach ($items as $it):
                            $qty = (float)($it['quantity'] ?? 0);
                            $price = (float)($it['price'] ?? 0);
                            $subtotal = $qty * $price;
                            $calcTotal += $subtotal;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($it['product_name'] ?? ('ID ' . ($it['product_id'] ?? ''))); ?></td>
                            <td class="text-end"><?php echo (int)$qty; ?></td>
                            <td class="text-end"><?php echo number_format($price, 2); ?></td>
                            <td class="text-end"><?php echo number_format($subtotal, 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th class="text-end"><?php echo number_format($order['total_amount'] ?? $calcTotal, 2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
        <div class="card-footer">
            <button id="btnSavePdf" class="btn btn-sm btn-outline-primary">Guardar</button>
             <a href="http://localhost/bike_store/" class="btn btn-sm btn-secondary">Volver</a>
         </div>
     </div>
 </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
<script>
document.getElementById('btnSavePdf').addEventListener('click', function () {
    const elemento = document.querySelector('.card');
    if (!elemento) return;
    const filename = 'factura-<?php echo htmlspecialchars($order['order_id'] ?? 'pedido'); ?>.pdf';
    html2pdf().set({
        margin: 0.5,
        filename: filename,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    }).from(elemento).save();
});
</script>

<?php include("../../templates/footer.php"); ?>