<?php include("../../bd.php");

$orderBy = isset($_GET['order_by']) ? $_GET['order_by'] : 'product_name';
$orderDirection = isset($_GET['order_direction']) ? $_GET['order_direction'] : 'ASC';
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';

$validColumns = ['product_name', 'price', 'model_year'];
$orderBy = in_array($orderBy, $validColumns) ? $orderBy : 'product_name';
$orderDirection = strtoupper($orderDirection) === 'DESC' ? 'DESC' : 'ASC';

$query = "SELECT *,
    (SELECT category_name FROM categories WHERE categories.category_id = products.category_id LIMIT 1) AS categoria
    FROM products";
if ($categoryFilter) {
    $query .= " WHERE category_id = :category_id";
}
$query .= " ORDER BY $orderBy $orderDirection";

$sentencia = $conexion->prepare($query);
if ($categoryFilter) {
    $sentencia->bindParam(':category_id', $categoryFilter);
}
$sentencia->execute();
$lista_productos = $sentencia->fetchAll(PDO::FETCH_ASSOC); 


$categorias = $conexion->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include("../../templates/header.php") ?>
<br>
<nav class="navbar navbar-expand navbar-light bg-blue">
    <ul class="nav navbar-nav">
        <li class="nav-item ">
            <a class=" btn btn-outline-primary" href="?order_by=product_name&order_direction=<?php echo ($orderBy == 'product_name' && $orderDirection == 'ASC') ? 'DESC' : 'ASC'; ?>">Nombre <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down-up" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M11.5 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L11 2.707V14.5a.5.5 0 0 0 .5.5m-7-14a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L4 13.293V1.5a.5.5 0 0 1 .5-.5"/>
</svg> </a>
<label for=""> </label>
        </li>
        <li class="nav-item">
            <a class="btn btn-outline-primary" href="?order_by=price&order_direction=<?php echo ($orderBy == 'price' && $orderDirection == 'ASC') ? 'DESC' : 'ASC'; ?>">Precio <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down-up" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M11.5 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L11 2.707V14.5a.5.5 0 0 0 .5.5m-7-14a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L4 13.293V1.5a.5.5 0 0 1 .5-.5"/>
</svg></a>
<label for=""> </label>
        </li>
        <li class="nav-item">
            <a class="btn btn-outline-primary" href="?order_by=model_year&order_direction=<?php echo ($orderBy == 'model_year' && $orderDirection == 'ASC') ? 'DESC' : 'ASC'; ?>">Año <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down-up" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M11.5 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L11 2.707V14.5a.5.5 0 0 0 .5.5m-7-14a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L4 13.293V1.5a.5.5 0 0 1 .5-.5"/>
</svg></a>
<label for=""> </label>
        </li>
        <li class="nav-item dropdown">
            <a class="btn btn-outline-primary" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Categoría
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="?category=">Todas</a></li>
                <?php foreach ($categorias as $categoria): ?>
                    <li><a class="dropdown-item" href="?category=<?php echo $categoria['category_id']; ?>"><?php echo htmlspecialchars($categoria['category_name']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </li>
    </ul>
</nav>
<div class="card">
    <div class="card-body">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach($lista_productos as $registro) { ?>
            <div class="col">
                <div class="card h-100">
                    <?php if(!empty($registro['foto']) && file_exists(__DIR__ . '/imagen/' . $registro['foto'])): ?>
                        <img src="./imagen/<?php echo htmlspecialchars($registro['foto']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($registro['product_name']); ?>" style="object-fit: cover; height: 220px;">
                    <?php else: ?>
                        <div class="bg-light" style="height:220px;"></div>
                    <?php endif; ?>

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-2"><?php echo htmlspecialchars($registro['product_name']); ?></h5>
                        <p class="card-text mb-1 "><B>Categoria: </B><?php echo htmlspecialchars($registro['categoria'] ?? $registro['category_id']); ?></p>
                        <p class="card-text mb-1 small"><b>Modelo: </b> <?php echo htmlspecialchars($registro['model_year']); ?></p>
                        <div class="mt-auto "><b>Precio: </b>$<?php echo number_format($registro['price'], 2); ?></div>
                    </div>

                    <div class="card-footer d-flex justify-content-center">
                        <a class="btn btn-outline-danger btn-sm" href="../pedidos/carrito.php?add_product_id=<?php echo $registro['product_id']; ?>" role="button"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart4" viewBox="0 0 16 16">
  <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5M3.14 5l.5 2H5V5zM6 5v2h2V5zm3 0v2h2V5zm3 0v2h1.36l.5-2zm1.11 3H12v2h.61zM11 8H9v2h2zM8 8H6v2h2zM5 8H3.89l.5 2H5zm0 5a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0m9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0"/>
</svg> Añadir al carrito</a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="card-footer text-muted">Footer</div>
</div>

<?php include("../../templates/footer.php") ?>
<script>
document.addEventListener('click', function(e){
    const btn = e.target.closest('.add-to-cart');
    if (!btn) return;
    const pid = btn.getAttribute('data-product-id');
    if (!pid) return;
    btn.disabled = true;
    fetch('../pedidos/carrito.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'ajax_add=1&product_id=' + encodeURIComponent(pid) + '&quantity=1'
    })
    .then(r => r.json())
    .then(json => {
        alert(json.ok ? 'Producto agregado al carrito' : ('Error: ' + (json.message||'')));
    })
    .catch(() => alert('Error al enviar datos'))
    .finally(()=> btn.disabled = false);
});
</script>
