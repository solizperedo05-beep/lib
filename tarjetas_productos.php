<?php
include("bd.php");
$jsonFile = __DIR__ . '/tarjetas_items.json';
$items = [];
if (file_exists($jsonFile)) {
    $items = json_decode(file_get_contents($jsonFile), true);
    if (!is_array($items)) $items = [];
}

$productos = $conexion->query("
    SELECT p.product_id, p.product_name, p.foto, p.price, p.category_id, COALESCE(c.category_name,'') AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    ORDER BY p.product_name
")->fetchAll(PDO::FETCH_ASSOC);

$lista_categorias = $conexion->query("SELECT * FROM categories ORDER BY category_name ASC")->fetchAll(PDO::FETCH_ASSOC);
if (isset($_GET['idEliminar'])) {
    $idDel = (int)$_GET['idEliminar'];
    foreach ($items as $k => $it) {
        if (isset($it['id']) && $it['id'] === $idDel) {
            if (!empty($it['foto']) && file_exists(__DIR__ . '/secciones/productos/imagen/' . $it['foto'])) {
                @unlink(__DIR__ . '/secciones/productos/imagen/' . $it['foto']);
            }
            unset($items[$k]);
            $items = array_values($items);
            file_put_contents($jsonFile, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            header("Location:tarjetas_productos.php?mensaje=Eliminado");
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_product_id = isset($_POST['selected_product_id']) ? (int)$_POST['selected_product_id'] : 0;
    $product_name = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $category_name = '';
    if ($category_id) {
        foreach ($lista_categorias as $c) {
            if ((int)$c['category_id'] === $category_id) {
                $category_name = $c['category_name'];
                break;
            }
        }
    }
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;

    $fotoNombre = '';
    if (isset($_FILES['foto']) && !empty($_FILES['foto']['tmp_name'])) {
        $fecha = (new DateTime())->getTimestamp();
        $originalName = basename($_FILES['foto']['name']);
        $fotoNombre = $fecha . '_' . $originalName;
        $dest = __DIR__ . '/secciones/productos/imagen/' . $fotoNombre;
        move_uploaded_file($_FILES['foto']['tmp_name'], $dest);
    }

    if ($fotoNombre === '' && $selected_product_id > 0) {
        foreach ($productos as $p) {
            if ((int)$p['product_id'] === $selected_product_id) {
                $fotoNombre = $p['foto'] ?? '';
                if ($product_name === '') $product_name = $p['product_name'] ?? '';
                if ($price == 0) $price = (float)($p['price'] ?? 0);
                if ($category_id == 0) {
                    $category_id = (int)($p['category_id'] ?? 0);
                    $category_name = $p['category_name'] ?? '';
                }
                break;
            }
        }
    }

    $id = (int)(microtime(true) * 1000);

    $items[] = [
        'id' => $id,
        'product_name' => $product_name,
        'foto' => $fotoNombre,
        'category_id' => $category_id,
        'category_name' => $category_name,
        'price' => $price,
        'created_at' => date('c'),
    ];

    file_put_contents($jsonFile, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    header("Location:tarjetas_productos.php?mensaje=Agregado");
    exit;
}

include("templates/header.php");
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h2 class="mb-4 text-primary"><i class="bi bi-card-image"></i> Administrar Tarjetas de Productos</h2>

            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">Agregar tarjeta</h5>
                </div>
                <div class="card-body p-4">
                    <form action="tarjetas_productos.php" method="post" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label for="select_product" class="form-label fw-bold">Seleccionar producto existente:</label>
                            <select id="select_product" class="form-select mb-2">
                                <option value="">(ninguno)</option>
                                <?php foreach ($productos as $p): ?>
                                    <option value="<?php echo $p['product_id']; ?>"><?php echo htmlspecialchars($p['product_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="selected_product_id" id="selected_product_id" value="">
                        </div>

                        <div class="mb-4">
                            <label for="product_name" class="form-label fw-bold">Nombre:</label>
                            <input type="text" class="form-control" name="product_name" id="product_name" required>
                        </div>

                        <div class="mb-4">
                            <label for="foto" class="form-label fw-bold">Imagen:</label>
                            <div class="mb-2">
                                <img id="previewFoto" src="" alt="" style="max-width:180px; max-height:120px; display:none; object-fit:cover; border:1px solid #ddd; padding:3px;">
                            </div>
                            <input type="file" class="form-control" name="foto" id="foto" accept="image/*">
                        </div>

                        <div class="mb-4">
                            <label for="category_id" class="form-label fw-bold">Categoría:</label>
                            <select class="form-select" name="category_id" id="category_id">
                                <option value="">(sin categoría)</option>
                                <?php foreach ($lista_categorias as $registro) { ?>
                                <option value="<?php echo $registro['category_id']; ?>"><?php echo htmlspecialchars($registro['category_name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="price" class="form-label fw-bold">Precio:</label>
                            <input type="number" step="0.01" class="form-control" name="price" id="price" placeholder="0.00">
                        </div>

                        <div class="d-flex justify-content-end gap-3">
                            <a href="index.php" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success">Agregar tarjeta</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tarjetas actuales</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($items)): ?>
                        <div class="alert alert-info">No hay tarjetas. Añade manualmente.</div>
                    <?php else: ?>
                        <div class="row row-cols-1 row-cols-md-2 g-3">
                            <?php foreach ($items as $it): ?>
                                <div class="col">
                                    <div class="d-flex gap-3 align-items-center border rounded p-3">
                                        <div style="width:120px; height:80px; overflow:hidden;">
                                            <?php if (!empty($it['foto']) && file_exists(__DIR__.'/secciones/productos/imagen/'.$it['foto'])): ?>
                                                <img src="secciones/productos/imagen/<?php echo htmlspecialchars($it['foto']); ?>" style="width:120px;height:80px;object-fit:cover;">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="width:120px;height:80px;color:#999;">No image</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($it['product_name']); ?></h6>
                                            <div class="small text-muted mb-1"><?php echo htmlspecialchars($it['category_name'] ?? ''); ?></div>
                                            <div class="fw-bold">$<?php echo number_format((float)($it['price'] ?? 0),2); ?></div>
                                        </div>
                                        <div>
                                            <a href="tarjetas_productos.php?idEliminar=<?php echo (int)$it['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Eliminar tarjeta?')">Eliminar</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include("templates/footer.php"); ?>

<script>
    const productosData = <?= json_encode(array_column($productos, null, 'product_id'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    document.getElementById('select_product').addEventListener('change', function() {
        const id = this.value ? String(this.value) : '';
        document.getElementById('selected_product_id').value = id;

        if (!id || !productosData[id]) {
            document.getElementById('product_name').value = '';
            document.getElementById('price').value = '';
            document.getElementById('category_id').value = '';
            document.getElementById('previewFoto').style.display = 'none';
            document.getElementById('previewFoto').src = '';
            return;
        }

        const p = productosData[id];
        document.getElementById('product_name').value = p.product_name || '';
        document.getElementById('price').value = p.price || '';
        document.getElementById('category_id').value = p.category_id || '';
        if (p.foto) {
            const src = 'secciones/productos/imagen/' + p.foto;
            document.getElementById('previewFoto').src = src;
            document.getElementById('previewFoto').style.display = '';
        } else {
            document.getElementById('previewFoto').style.display = 'none';
            document.getElementById('previewFoto').src = '';
        }
    });

    document.getElementById('foto').addEventListener('change', function() {
        const f = this.files && this.files[0];
        if (!f) return;
        const reader = new FileReader();
        reader.onload = function(ev) {
            const img = document.getElementById('previewFoto');
            img.src = ev.target.result;
            img.style.display = '';
        };
        reader.readAsDataURL(f);
    });
</script>