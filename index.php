<?php include("templates/header.php") ?>

<div class="p-5 mb-4 bg-light rounded-3">
    <div class="container-fluid py-5">
        <h2 class="display-5 fw-bold">Bienvenido  <?php echo $registro['usuario'] ?? ($_SESSION['usuario'] ?? ''); ?> a la tienda Bike Store</h2>
        <p class="col-md-8 fs-4 text-center">En esta tienda podras comprar bicicletas deportivas y de uso personal <br>
             de diferentes modelos y precios, ademas encontraras accesorios para bicicletas </p>
        <!--
        <p class="col-md-8 fs-4">
            Using a series of utilities, you can create this jumbotron, just
            like the one in previous versions of Bootstrap. Check out the
            examples below for how you can remix and restyle it to your liking.
        </p>
        <button class="btn btn-primary btn-lg" type="button">
            Example button
        </button>
        -->
    </div>

    <?php
    $jsonFile = __DIR__ . '/carrusel_items.json';
    $items = [];
    if (file_exists($jsonFile)) {
        $items = json_decode(file_get_contents($jsonFile), true);
        if (!is_array($items)) $items = [];
    }
    ?>
    <div class="card-header">
        <h3 class=" text-center">Productos Destacados</h3>
        <?php if ($esAdmin): ?>
        <a name="" id="" class="btn btn-outline-primary" href="carrusel.php" role="button">Agregar imagenes</a>
        <?php else: ?>
        <?php endif; ?>
    </div>
    <br>
    <?php if (count($items) > 0): ?>
        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php foreach ($items as $i => $it): ?>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?php echo $i; ?>" <?php echo $i === 0 ? 'class="active" aria-current="true"' : ''; ?> aria-label="Slide <?php echo $i+1; ?>"></button>
                <?php endforeach; ?>
            </div>

            <div class="carousel-inner">
                <?php foreach ($items as $i => $it): ?>
                    <div class="carousel-item <?php echo $i === 0 ? 'active' : ''; ?>">
                        <?php if (!empty($it['foto'])): ?>
                            <img src="secciones/productos/imagen/<?php echo htmlspecialchars($it['foto']); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($it['product_name']); ?>" style="height:600px; object-fit:cover;">
                        <?php else: ?>
                            <div class="d-block w-100 bg-light" style="height:400px"></div>
                        <?php endif; ?>

                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-2">
                            <h5><?php echo htmlspecialchars($it['product_name'] ?? ''); ?></h5>
                            <p>Categoria: <?php echo htmlspecialchars($it['category_name'] ?? ''); ?></p>
                            <p>Precio: $<?php echo isset($it['price']) ? number_format((float)$it['price'],2) : '0.00'; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: invert(1);"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true" style="filter: invert(1);"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    <?php else: ?>
        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
          </div>
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img src="" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
              <img src="..." class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
              <img src="..." class="d-block w-100" alt="...">
            </div>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: invert(1);"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true" style="filter: invert(1);"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
    <?php endif; ?>
    <br>
    <br>
    <h3 class="text-center">Productos más vendidos</h3>

    <?php $role = strtoupper($_SESSION['role'] ?? $_SESSION['rol'] ?? '');
    $esAdmin = ($role === 'ADMINISTRADOR');?>
    <?php if ($esAdmin): ?>
        <a name="" id="" class="btn btn-outline-primary " href="tarjetas_productos.php" role="button">Agregar productos</a>
    <?php else: ?>
    <?php endif; ?>
    <?php
    $tarjetasFile = __DIR__ . '/tarjetas_items.json';
    $tarjetas = [];
    if (file_exists($tarjetasFile)) {
        $tarjetas = json_decode(file_get_contents($tarjetasFile), true);
        if (!is_array($tarjetas)) $tarjetas = [];
    }
    ?>

    <div class="row row-cols-1 row-cols-md-4 g-4 mt-3">
        <?php for ($i = 0; $i < 4; $i++): 
            $prod = $tarjetas[$i] ?? null;
        ?>
            <div class="col">
                <div class="card h-100">
                    <?php if ($prod && !empty($prod['foto']) && file_exists(__DIR__.'/secciones/productos/imagen/'.$prod['foto'])): ?>
                        <img src="secciones/productos/imagen/<?php echo htmlspecialchars($prod['foto']); ?>" class="card-img-top" style="height:180px; object-fit:cover;" alt="<?php echo htmlspecialchars($prod['product_name'] ?? ''); ?>">
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height:180px;color:#888;">
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($prod['product_name'] ?? ''); ?></h5>
                        <p class="card-text ">Categoria: <?php echo htmlspecialchars($prod['category_name'] ?? ''); ?></p>
                        <p class="card-text ">Precio: <?php echo $prod ? ('$'.number_format((float)$prod['price'],2)) : '&nbsp;'; ?></p>
                    </div>
                </div>
            </div>
        <?php endfor; ?>
    </div>
</div>

<?php include("templates/footer.php") ?>
