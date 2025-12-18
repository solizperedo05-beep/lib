<?php
include("../../bd.php");

if(isset($_GET['txtID'])){
    $txtID = (isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia = $conexion->prepare("DELETE FROM order_items WHERE order_items_id = :id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

    $mensaje="Registro eliminado";
    header("Location:index.php?mensaje=".$mensaje);
    exit;
}

$sentencia = $conexion->prepare("SELECT * FROM order_items");
$sentencia->execute();
$lista_pedidos = $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include("../../templates/header.php") ?>
<br>
<div class="card">
    <div class="card-header">
        <a class="btn btn-outline-primary" href="<?php echo $url_base;?>secciones/pedidos/" role="button">Atras</a>
        <a class="btn btn-outline-primary" href="crear.php" role="button">Nuevo</a>
    </div>
    <div class="card-body">
        <?php if(isset($_GET['mensaje'])) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo ($_GET['mensaje']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>
        <div class="table-responsive-sm">
            <table class="table table-bordered table-striped ">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">ID Pedido de articulo</th>
                        <th scope="col">ID Pedido</th>
                        <th scope="col">ID Producto</th>
                        <th scope="col">Cantidad</th>
                        <th scope="col">Precio</th>
                        <th scope="col">Descuento</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($lista_pedidos as $registro) {
                        $id = $registro['order_items_id'] ?? $registro['order_id'] ?? '';
                        ?>
                    <tr>
                        <td scope="row"><?php echo ($id); ?></td>
                        <td><?php echo ($registro['order_id']); ?></td>
                        <td><?php echo ($registro['product_id']); ?></td>
                        <td><?php echo ($registro['quantity']); ?></td>
                        <td><?php echo ($registro['price'] ); ?></td>
                        <td><?php echo ($registro['discount'] ); ?></td>
                        <td>
                            <a class="btn btn-outline-primary" href="editar.php?txtID=<?php echo $registro['order_items_id']; ?>" role="button">Editar</a>
                            <a class="btn btn-outline-danger" href="index.php?txtID=<?php echo $registro['order_items_id']; ?>" onclick="return confirm('¿Eliminar este pedido?');" role="button">Eliminar</a>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if(empty($lista_pedidos)) { ?>
                    <tr>
                        <td colspan="7" class="text-center">No hay pedidos</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
    </div>
    <div class="card-footer text-muted">Footer</div>
</div>

<?php include("../../templates/footer.php") ?>
