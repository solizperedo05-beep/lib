<?php
include("../../bd.php");


function firstExistingTable($pdo, $c){ foreach($c as $t){ try{ $r=$pdo->query("SHOW TABLES LIKE ".$pdo->quote($t)); if($r && $r->fetch()) return $t; }catch(PDOException $e){} } return null; }

$id = $_GET['txtID'] ?? null; if(!$id){ header("Location:index.php"); exit; }


$pedido = $conexion->prepare("SELECT * FROM orders WHERE order_id = :id LIMIT 1"); $pedido->execute([':id'=>$id]); $pedido = $pedido->fetch(PDO::FETCH_ASSOC);
if(!$pedido){ header("Location:index.php?mensaje=Pedido no encontrado"); exit; }

$customers = $conexion->query("SELECT customers_id, CONCAT(first_name,' ',last_name) AS name FROM customers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$products  = $conexion->query("SELECT product_id, product_name, price FROM products ORDER BY product_name")->fetchAll(PDO::FETCH_ASSOC);
$usuarios  = $conexion->query("SELECT user_id, usuario FROM usuarios ORDER BY usuario")->fetchAll(PDO::FETCH_ASSOC);
$productPrices = array_column($products,'price','product_id');

$itemsTable = firstExistingTable($conexion, ['order_items','order_details','pedido_detalle','pedidos_articulos','orders_products','pedido_items']);
$items = [];
if($itemsTable){
    $items = $conexion->prepare("SELECT * FROM {$itemsTable} WHERE order_id = :id");
    $items->execute([':id'=>$id]); $items = $items->fetchAll(PDO::FETCH_ASSOC);
    $cols = $conexion->query("SHOW COLUMNS FROM {$itemsTable}")->fetchAll(PDO::FETCH_COLUMN,0);
}


$colsO = $conexion->query("SHOW COLUMNS FROM orders")->fetchAll(PDO::FETCH_COLUMN,0);
$colCustomer = in_array('customer_id',$colsO)?'customer_id':(in_array('customers_id',$colsO)?'customers_id':null);
$colUser     = in_array('user_id',$colsO)?'user_id':(in_array('users_id',$colsO)?'users_id':null);


if($_SERVER['REQUEST_METHOD']==='POST'){
    $post = $_POST;
    try{
        $conexion->beginTransaction();
        $sets=[];$params=[':id'=>$id];
        if($colCustomer){ $sets[]="$colCustomer = :customer"; $params[':customer']=$post['customer_id']??null; }
        if($colUser){ $sets[]="$colUser = :user"; $params[':user']=$post['user_id']??null; }
        $sets[]="order_date = :order_date"; $params[':order_date']=$post['order_date']??null;
        $sets[]="estado = :estado"; $params[':estado']=$post['estado']??null;
        $sets[]="total_amount = :total_amount"; $params[':total_amount']=$post['total_amount']??0;
        $sql = "UPDATE orders SET ".implode(", ",$sets)." WHERE order_id = :id";
        $conexion->prepare($sql)->execute($params);

        if($itemsTable){
            $conexion->prepare("DELETE FROM {$itemsTable} WHERE order_id = :id")->execute([':id'=>$id]);
            $icols = $conexion->query("SHOW COLUMNS FROM {$itemsTable}")->fetchAll(PDO::FETCH_COLUMN,0);
            $c_order = in_array('order_id',$icols)?'order_id':(in_array('pedido_id',$icols)?'pedido_id':'order_id');
            $c_prod  = in_array('product_id',$icols)?'product_id':(in_array('producto_id',$icols)?'producto_id':'product_id');
            $c_qty   = in_array('quantity',$icols)?'quantity':(in_array('qty',$icols)?'qty':'quantity');
            $c_price = in_array('price',$icols)?'price':(in_array('unit_price',$icols)?'unit_price':'price');
            $c_disc  = in_array('discount',$icols)?'discount':(in_array('descuento',$icols)?'descuento':'discount');

            $ins = $conexion->prepare("INSERT INTO {$itemsTable} ($c_order,$c_prod,$c_qty,$c_price,$c_disc) VALUES (:o,:p,:q,:pr,:d)");
            for($i=0;$i<count($post['product_id'] ?? []);$i++){
                $p = $post['product_id'][$i] ?? null; if(!$p) continue;
                $ins->execute([':o'=>$id, ':p'=>$p, ':q'=>$post['quantity'][$i]??1, ':pr'=>$post['price'][$i]??0, ':d'=>$post['discount'][$i]??0]);
            }
        }

        $conexion->commit(); header("Location:index.php?mensaje=Pedido actualizado"); exit;
    }catch(PDOException $e){ $conexion->rollBack(); $error = $e->getMessage(); }
}

include("../../templates/header.php");
?>
<br>
<h2 class="mb-4 text-primary"><i class="bi bi-pencil-square"></i> Información del Pedido</h2>
<div class="card">
  <div class="card-header bg-primary text-white py-3">
    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Datos del pedido</h5>
  </div>
  <div class="card-body">
    <?php if(!empty($error)): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
    <form method="post" id="form-pedido">
      <div class="mb-3">
        <label>Cliente</label>
        <select  name="customer_id" class="form-select" required>
          <option value="">--Seleccione--</option>
          <?php foreach($customers as $c): $v=$c['customers_id']; $sel=(($pedido[$colCustomer]??$pedido['customer_id']??'')==$v)?'selected':''; ?>
            <option value="<?=htmlspecialchars($v)?>" <?=$sel?>><?=htmlspecialchars($c['name'])?></option>
          <?php endforeach;?>
        </select>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3"><label>Fecha</label>
        <input  type="date" name="order_date" class="form-control" value="<?=htmlspecialchars($pedido['order_date']??'')?>" required></div>
        <div class="col-md-6 mb-3"><label>Usuario</label>
          <select  name="user_id" class="form-select" required>
            <option value="">Seleccione un usuario</option>
            <?php foreach($usuarios as $u): $v=$u['user_id']; $sel=(($pedido[$colUser]??$pedido['user_id']??'')==$v)?'selected':''; ?>
              <option value="<?=htmlspecialchars($v)?>" <?=$sel?>><?=htmlspecialchars($u['usuario'])?></option>
            <?php endforeach;?>
          </select>
        </div>
      </div>

      <div class="mb-3"><label>Estado</label>
        
        <select class="form-select form-select-lg border-2" name="estado" id="estado">
          <option value="Pendiente" <?php echo (isset($estado) && $estado=='Pendiente')?'selected':''; ?>>Pendiente</option>
          <option value="Anulado" <?php echo (isset($estado) && $estado=='Anulado')?'selected':''; ?>>Anulado</option>
        </select>
      </div>
    </div>
      <div class="card-header bg-primary text-white py-3">
    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Detalles del pedido</h5>
  </div>
      <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="tabla-items">
        <thead class="table-primary">
          <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Descuento (%)</th>
            <th>Sub Total</th>
            <!--
            <th>Acciones</th>
            -->
          </tr></thead>
        <tbody>
          <?php if(!empty($items)): foreach($items as $it):
            $prod = $it['product_id'] ?? $it['producto_id'] ?? $it['product'] ?? '';
            $qty  = $it['quantity'] ?? $it['qty'] ?? 1;
            $pr   = $it['price'] ?? $it['unit_price'] ?? 0;
            $disc = $it['discount'] ?? $it['descuento'] ?? 0;?>
            <tr>
              <td><select  name="product_id[]" class="form-select product-select">
                <option value="">Seleccione</option><?php foreach($products as $p): $sel=($p['product_id']==$prod)?'selected':''; ?>
                <option value="<?=htmlspecialchars($p['product_id'])?>" <?=$sel?>><?=htmlspecialchars($p['product_name'])?></option>
                <?php endforeach;?></select></td>
              <td><input disabled type="number" name="quantity[]" class="form-control quantity" min="1" value="<?=htmlspecialchars($qty)?>"></td>
              <td><input disabled type="number" step="0.01" name="price[]" class="form-control price" value="<?=htmlspecialchars($pr)?>"></td>
              <td><input disabled type="number" step="0.01" name="discount[]" class="form-control discount" value="<?=htmlspecialchars($disc)?>"></td>
              <td class="subtot">0.00</td>
            </tr>
          <?php endforeach; else: ?>
            <tr>
              <td><select name="product_id[]" class="form-select product-select">
                <option  value="">--Seleccione--</option><?php foreach($products as $p): ?>
                  <option value="<?=htmlspecialchars($p['product_id'])?>"><?=htmlspecialchars($p['product_name'])?></option>
                  <?php endforeach;?></select></td>
              <td><input  type="number" name="quantity[]" class="form-control quantity" min="1" value="1"></td>
              <td><input  type="number" step="0.01" name="price[]" class="form-control price" value="0.00"></td>
              <td><input  type="number" step="0.01" name="discount[]" class="form-control discount" value="0.00"></td>
              <td class="subtot">0.00</td>
             
            </tr>
          <?php endif;?>
        </tbody>
      </table></div>
    
      <div class="mb-3"><label>Monto total:</label><div id="monto-total">Bs. 0.00</div>
      <input type="hidden" name="total_amount" id="total_amount_input" value="<?=htmlspecialchars($pedido['total_amount']??'0.00')?>"></div>

      <div class="d-flex gap-2"><a class="btn btn-secondary" href="index.php">Regresar</a>
      
      <button class="btn btn-primary" type="submit">Guardar</button></div>
      
    </form>
  </div>
</div>

<script>
const productPrices = <?= json_encode($productPrices, JSON_HEX_TAG) ?>;
const tbody = document.querySelector('#tabla-items tbody');

function actualizarFila(tr){
  const q = +tr.querySelector('.quantity').value||0;
  const p = +tr.querySelector('.price').value||0;
  const d = +tr.querySelector('.discount').value||0;
  tr.querySelector('.subtot').textContent = (Math.max(0, q*p*(1-d/100))).toFixed(2);
  actualizarTotal();
}
function actualizarTotal(){ let t=0; tbody.querySelectorAll('tr').forEach(r=> t+= +r.querySelector('.subtot').textContent||0); document.getElementById('monto-total').textContent='Bs. '+t.toFixed(2); document.getElementById('total_amount_input').value=t.toFixed(2); }

document.addEventListener('click', e=>{
  if(e.target.id==='agregar-item'){
    const tr=document.createElement('tr');
    tr.innerHTML = `<?php ob_start(); ?><td><select name="product_id[]" class="form-select product-select"><option value="">--Seleccione--</option><?php foreach($products as $p): ?><option value="<?=htmlspecialchars($p['product_id'])?>"><?=htmlspecialchars($p['product_name'])?></option><?php endforeach;?></select></td><td><input type="number" name="quantity[]" class="form-control quantity" min="1" value="1"></td><td><input type="number" step="0.01" name="price[]" class="form-control price" value="0.00"></td><td><input type="number" step="0.01" name="discount[]" class="form-control discount" value="0.00"></td><td class="subtot">0.00</td><td><button type="button" class="btn btn-sm btn-danger btn-remove">Eliminar</button></td><?php $s=ob_get_clean(); echo addslashes(str_replace(["\n","\r"],'',$s)); ?>`;
    tbody.appendChild(tr);
  }
  if(e.target.classList.contains('btn-remove')){ e.target.closest('tr').remove(); actualizarTotal(); }
});

tbody.addEventListener('change', e=>{
  const tr=e.target.closest('tr');
  if(e.target.classList.contains('product-select')){ const pid=e.target.value; if(productPrices[pid]!==undefined) tr.querySelector('.price').value=productPrices[pid]; }
  if(tr && (e.target.classList.contains('quantity')||e.target.classList.contains('price')||e.target.classList.contains('discount')||e.target.classList.contains('product-select'))) actualizarFila(tr);
});

document.querySelectorAll('#tabla-items tbody tr').forEach(tr=> actualizarFila(tr));
</script>

<?php include("../../templates/footer.php"); ?>