<?php include("../../bd.php");

if(isset($_GET['txtID'])){
    $txtID = isset($_GET['txtID']) ? (int)$_GET['txtID'] : 0;

    $sentencia = $conexion->prepare("SELECT foto FROM products WHERE product_id = :id");
    $sentencia->bindParam(":id", $txtID, PDO::PARAM_INT);
    $sentencia->execute();
    $registro = $sentencia->fetch(PDO::FETCH_ASSOC);

    if (!empty($registro['foto']) && file_exists(__DIR__ . "/imagen/" . $registro['foto'])) {
        unlink(__DIR__ . "/imagen/" . $registro['foto']);
    }
    $delItems = $conexion->prepare("DELETE FROM order_items WHERE product_id = :id");
    $delItems->bindParam(":id", $txtID, PDO::PARAM_INT);
    $delItems->execute();

    $delProd = $conexion->prepare("DELETE FROM products WHERE product_id = :id");
    $delProd->bindParam(":id", $txtID, PDO::PARAM_INT);
    $delProd->execute();

    $mensaje = "Registro eliminado";
    header("Location:index.php?mensaje=" . urlencode($mensaje));
    exit;
}

$sentencia=$conexion->prepare("SELECT *,
(select category_name from categories where categories.category_id=products.category_id limit 1) as categoria
 FROM products");
$sentencia->execute();
$lista_productos = $sentencia->fetchAll(PDO::FETCH_ASSOC); 

?>
<?php include("../../templates/header.php") ?>
<br>
<div class="card">
    <div class="card-header">
        <a name="" id="" class="btn btn-outline-primary" href="crear.php" role="button">Nuevo</a>
    </div>
    <div class="card-body">
        <div class="table-responsive-sm">
            <table class="table table-bordered ">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Producto</th>
                        <th scope="col">Foto</th>
                        <th scope="col">Modelo año</th>
                        <th scope="col">Precio</th>
                        <th scope="col">Categoria</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php foreach($lista_productos as $registro) { ?>
                    <tr class="">
                        <td scope="row"><?php echo $registro['product_id']; ?></td>
                        <td><?php echo $registro['product_name']; ?></td>
                        <td><img width="50" src="./imagen/<?php echo $registro['foto']; ?>"
                            class="img-fluid rounded" alt="Foto del producto"/>
                        </td>
                        <td><?php echo $registro['model_year'] ?></td>
                        <td>
                            <span style="color: green; font-weight: bold; font-size: 1.1rem;">
                                $<?php echo number_format($registro['price'], 2); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($registro['categoria'] ?? $registro['category_id']); ?></td>
                        <td>
                            <a class="btn btn-outline-primary" href="editar.php?txtID=<?php echo $registro['product_id']; ?>" role="button"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
  <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
  <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
</svg></a>
                            <a class="btn btn-outline-danger" href="index.php?txtID=<?php echo $registro['product_id']; ?>" role="button"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
  <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
</svg></a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
    </div>
    <div class="card-footer text-muted">Footer</div>
</div>


<?php include("../../templates/footer.php") ?>
