<?php
include("../../bd.php");

$product=$conexion->query("SELECT product_id, product_name, price FROM products ORDER BY product_name ")->fetchAll(PDO::FETCH_ASSOC);

if($_POST){
    $store_name = isset($_POST["store_name"])?$_POST["store_name"]:"";
    $phone= isset($_POST["phone"])?$_POST["phone"]:"";
    $email= isset($_POST["email"])?$_POST["email"]:"";
    $street=isset($_POST["street"])?$_POST["street"]:"";
    $city= isset($_POST["city"])?$_POST["city"]:"";
    $state= isset($_POST["state"])?$_POST["state"]:"";
    $estado= isset($_POST["estado"])?$_POST["estado"]:"activo";

    $sentencia = $conexion->prepare("INSERT INTO stores (store_name,phone,email,street,city,state,estado)VALUES (:store_name,:phone,:email,:street,:city,:state,:estado)");
    $sentencia->bindParam(":store_name",$store_name);
    $sentencia->bindParam(":phone",$phone);
    $sentencia->bindParam(":email",$email);
    $sentencia->bindParam(":street",$street);
    $sentencia->bindParam(":city",$city);
    $sentencia->bindParam(":state",$state);
    $sentencia->bindParam(":estado",$estado);
    $sentencia->execute();

    $storeId = (int)$conexion->lastInsertId();
    if (!empty($_POST['items']) && is_array($_POST['items'])) {
        $ins = $conexion->prepare("INSERT INTO stocks (store_id, product_id, quantity) VALUES (:store_id,:product_id,:quantity)");
        foreach ($_POST['items'] as $it) {
            $pid = isset($it['product_id']) ? (int)$it['product_id'] : 0;
            $qty = isset($it['quantity']) ? (int)$it['quantity'] : 0;
            if ($pid > 0 && $qty > 0) {
                $ins->bindValue(':store_id', $storeId, PDO::PARAM_INT);
                $ins->bindValue(':product_id', $pid, PDO::PARAM_INT);
                $ins->bindValue(':quantity', $qty, PDO::PARAM_INT);
                $ins->execute();
            }
        }
    }

    $memsaje="Registro agregado";
    header("Location:index.php?mensaje=".$memsaje);
    exit;
}
?>
<?php include("../../templates/header.php") ?>
<br> <br>
<div class="card">
    <div class="card-header">Datos de la tienda</div>
    <div class="card-body">
        <form action="" method="post">
            <div class="mb-3">
                <label for="store_name" class="form-label">Nombre de la tienda:</label>
                <input type="text" class="form-control" name="store_name" id="store_name" placeholder="Nombre de la tienda">
             
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Teléfono:</label>
                <input type="text" class="form-control" name="phone" id="phone" placeholder="Teléfono">
           
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Email">
   
            </div>

            <div class="mb-3">
                <label for="street" class="form-label">Calle:</label>
                <input type="text" class="form-control" name="street" id="street" placeholder="Dirección">
             
            </div>

            <div class="mb-3">
                <label for="city" class="form-label">Ciudad:</label>
                <input type="text" class="form-control" name="city" id="city" placeholder="Ciudad">
                
            </div>

            <div class="mb-3">
                <label for="state" class="form-label">State:</label>
                <input type="text" class="form-control" name="state" id="state" placeholder="State">
      
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label">Estado:</label>
                <select class="form-select" name="estado" id="estado">
                    <option value="activo">activo</option>
                    <option value="inactivo">inactivo</option>
                </select>
     
            </div>
            <div class="table-responsive-sm">
                    <table class="table table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th scope="col">Producto</th>
                                <th scope="col">Cantidad</th>
                                
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                             <tr>
                                <td>
                                    <select name="items[0][product_id]" required class="form-select">
                                        <option value="">Seleccione un producto</option>
                                        <?php foreach($product as $prod): ?>
                                            <option value="<?= $prod['product_id'] ?>"><?= htmlspecialchars($prod['product_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="number" name="items[0][quantity]" min="1" required class="form-control"></td>
                                
                                <td><button type="button" class="remove_row btn btn-sm btn-danger" onclick="removeRow(this)">Eliminar</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="add_row btn btn-outline-primary" onclick="addRow()"><i class="bi bi-plus-circle me-2"></i>Agregar item</button>
                <br>
                <br>
            <button type="submit" class="btn btn-outline-success">Guardar</button>
            <a class="btn btn-primary" href="index.php" role="button">Cancelar</a>
        </form>
    </div>
</div>
<?php include("../../templates/footer.php") ?>
<script>
    let itemIndex = 1;

    function addRow() {
        const tableBody = document.querySelector('table tbody');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select name="items[${itemIndex}][product_id]" required class="form-select">
                    <option value="">--Seleccione un producto--</option>
                    <?php foreach($product as $prod): ?>
                        <option value="<?= $prod['product_id'] ?>"><?= htmlspecialchars($prod['product_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><input type="number" name="items[${itemIndex}][quantity]" min="1" required class="form-control"></td>
            <td><button type="button" class="remove_row btn btn-sm btn-danger" onclick="removeRow(this)">Eliminar</button></td>
        `;
        tableBody.appendChild(newRow);
        itemIndex++;
    }
    function removeRow(button) {
        const row = button.closest('tr');
        row.remove();
    }
</script>