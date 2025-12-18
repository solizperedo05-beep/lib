<?php include("../../bd.php");

if(isset($_GET['txtID'])){
    $txtID = (isset($_GET['txtID']))?$_GET['txtID']:"";
  
    $sentencia=$conexion->prepare("SELECT * FROM stores WHERE store_id=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();
    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    $registro_recupeardo = $registro;

    $sentencia=$conexion->prepare("DELETE FROM stores WHERE store_id=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();
    $memsaje="Registro eliminado";
    header("Location:index.php?mensaje=".$memsaje);
}

$sentencia=$conexion->prepare("SELECT *,
(select store_name from stores s where s.store_id=st.store_id) as tienda_nombre
FROM stores st");
$sentencia->execute();
$lista_tiendas = $sentencia->fetchAll(PDO::FETCH_ASSOC); 

?>
<?php include("../../templates/header.php") ?>
<br>
<div class="card">
    <div class="card-header">
        <a name="" id="" class="btn btn-outline-primary" href="crear.php" role="button">Nuevo</a>
    </div>
    <div class="card-body">
        <div class="table-responsive-sm">
            <table class="table table-bordered  ">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre</th>
                        
                        <th scope="col">Telefono</th>
                        <th scope="col">email</th>
                        <th scope="col">calle</th>
                        <th scope="col">ciudad</th>
                        <th scope="col">Provincia/Estado</th>
                        <th scope="col">Estado de actividad</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php foreach($lista_tiendas as $registro) { ?>
                    <tr class="">
                        <td scope="row"><?php echo $registro['store_id']; ?></td>
                        <td><?php echo $registro['store_name']; ?></td>
                        
                        <td><?php echo $registro['phone'] ?></td>
                        <td><?php echo($registro['email']); ?>
                        </td>
                        <td><?php echo $registro['street'] ?></td>
                        <td><?php echo $registro["city"] ?> </td>
                        <td><?php echo $registro["state"] ?> </td>
                        <td><?php echo $registro["estado"] ?> </td>
                        <td>
                            <a class="btn btn-outline-primary" href="editar.php?txtID=<?php echo $registro['store_id']; ?>" role="button"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
  <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
  <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
</svg></a>
                            <a class="btn btn-outline-danger" href="index.php?txtID=<?php echo $registro['store_id']; ?>" role="button"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
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
