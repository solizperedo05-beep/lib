<?php
include("../../bd.php");


if(isset($_GET['txtID'])){
    $txtID = (isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia = $conexion->prepare("DELETE FROM orders WHERE order_id = :id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

    $mensaje="Registro eliminado";
    header("Location:index.php?mensaje=".$mensaje);
    exit;
}


$allowedPer = [5,10,25,50,100];
$perPage = isset($_GET['perpage']) && in_array((int)$_GET['perpage'],$allowedPer) ? (int)$_GET['perpage'] : 5;
$page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;

$countStmt = $conexion->prepare("SELECT COUNT(*) FROM orders");
$countStmt->execute();
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRows / $perPage));
$offset = ($page - 1) * $perPage;


$sql = "SELECT * FROM orders LIMIT :lim OFFSET :off";
$sentencia = $conexion->prepare($sql);
$sentencia->bindValue(':lim', (int)$perPage, PDO::PARAM_INT);
$sentencia->bindValue(':off', (int)$offset, PDO::PARAM_INT);
$sentencia->execute();
$lista_pedidos = $sentencia->fetchAll(PDO::FETCH_ASSOC);


$hasUsers = false;
try {
    $res = $conexion->query("SHOW TABLES LIKE 'users'");
    $rows = $res ? $res->fetchAll(PDO::FETCH_NUM) : [];
    if (count($rows) > 0) {
        $hasUsers = true;
    }
} catch (PDOException $e) {
    $hasUsers = false;
}


$userTable = null;
$possibleUserTables = ['users','user','usuarios','users_tbl','administradores','admins'];
foreach ($possibleUserTables as $t) {
    try {
        $r = $conexion->query("SHOW TABLES LIKE '".$t."'");
        if ($r && $r->fetch()) { $userTable = $t; break; }
    } catch (PDOException $e) { /* ignorar */ }
}
?>
<?php include("../../templates/header.php") ?>
<br>
<div class="card">
    <div class="card-header">
        <a class="btn btn-outline-primary" href="crear1.php" role="button">Nuevo</a>
        
    </div>
    
    <div class="card-body">
        <form method="get" class="row g-2 mb-3 align-items-center">
  <div class="col-auto">
    <label class="form-label mb-0">Mostrar</label>
    <select name="perpage" class="form-select" onchange="this.form.submit()">
      <?php foreach($allowedPer as $opt): ?>
        <option value="<?php echo $opt;?>" <?php if($opt==$perPage) echo 'selected'; ?>><?php echo $opt; ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <input type="hidden" name="page" value="1">
</form>
        <?php if(isset($_GET['mensaje'])) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo ($_GET['mensaje']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>
        <div class="table-responsive-sm">
            <table class="table table-bordered ">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">Pedido ID</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Fecha de pedido</th>
                        <th scope="col">Usuario</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Total</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($lista_pedidos as $registro) {
                        $id = $registro['order_id'] ?? '';

                 
                        $custId = $registro['customers_id'] ?? $registro['customer_id'] ?? null;
                        $customerName = '';
                        if($custId){
                            $stmtC = $conexion->prepare("SELECT first_name, last_name FROM customers WHERE customers_id = :id");
                            $stmtC->bindParam(":id",$custId);
                            $stmtC->execute();
                            $c = $stmtC->fetch(PDO::FETCH_ASSOC);
                            if($c){
                                $customerName = $c['first_name'].' '.$c['last_name'];
                            } else {
                                $customerName = $custId; 
                            }
                        }

                    
                        $userId = $registro['user_id'] ?? $registro['users_id'] ?? null;
                        $userName = '';
                        if($userId){
                            $stmtU = $conexion->prepare("SELECT usuario FROM usuarios WHERE user_id = :id");
                            $stmtU->bindParam(":id",$userId);
                            $stmtU->execute();
                            $u = $stmtU->fetch(PDO::FETCH_ASSOC);
                            if($u){
                                $userName = $u['usuario'];
                            } else {
                                $userName = $userId; 
                            }
                        }

                        ?>
                    <tr>
                        <td scope="row"><?php echo htmlspecialchars($id); ?></td>
                        <td><?php echo htmlspecialchars($customerName); ?></td>
                        <td><?php echo htmlspecialchars($registro['order_date']); ?></td>
                        <td><?php echo htmlspecialchars($userName); ?></td>
                        <td><?php echo htmlspecialchars($registro['estado']); ?></td>
                        <td><?php echo htmlspecialchars($registro['total_amount'] ); ?></td>
                        <td>
                            <a class="btn btn-outline-primary" href="editar1.php?txtID=<?php echo $registro['order_id']; ?>" role="button"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
  <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
  <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
</svg></a>
                            <a class="btn btn-outline-danger" href="index.php?txtID=<?php echo $registro['order_id']; ?>" onclick="return confirm('¿Eliminar este pedido?');" role="button"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
  <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
</svg></a>
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
        
<nav aria-label="Paginación" class="mt-3">
  <ul class="pagination">
    <li class="page-item <?php if($page<=1) echo 'disabled'; ?>">
      <a class="page-link" href="?page=<?php echo max(1,$page-1); ?>&perpage=<?php echo $perPage; ?>">«</a>
    </li>
    <?php
    $start = max(1, $page - 3);
    $end = min($totalPages, $page + 3);
    for($p=$start;$p<=$end;$p++): ?>
      <li class="page-item <?php if($p==$page) echo 'active'; ?>">
        <a class="page-link" href="?page=<?php echo $p; ?>&perpage=<?php echo $perPage; ?>"><?php echo $p; ?></a>
      </li>
    <?php endfor; ?>
    <li class="page-item <?php if($page>=$totalPages) echo 'disabled'; ?>">
      <a class="page-link" href="?page=<?php echo min($totalPages,$page+1); ?>&perpage=<?php echo $perPage; ?>">»</a>
    </li>
  </ul>
  
</nav>
    </div>
    <div class="card-footer text-muted">Footer</div>
</div>

<?php include("../../templates/footer.php") ?>
