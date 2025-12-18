
<?php
include("bd.php");

if ($_POST) {
    $usuario = isset($_POST["usuario"]) ? $_POST["usuario"] : "";
    $clave   = isset($_POST["clave"]) ? $_POST["clave"] : "";
    $email   = isset($_POST["email"]) ? $_POST["email"] : "";
    $role    = isset($_POST["role"]) ? strtoupper(trim($_POST["role"])) : "USUARIO";

    // validar rol simple
    $rolesPermitidos = ['USUARIO','ADMINISTRADOR'];
    if (!in_array($role, $rolesPermitidos, true)) {
        $role = 'USUARIO';
    }

    $sentencia = $conexion->prepare("INSERT INTO usuarios (usuario, clave, email, role) VALUES (:usuario, :clave, :email, :role)");
    $sentencia->bindParam(":usuario", $usuario);
    $sentencia->bindParam(":clave", $clave);
    $sentencia->bindParam(":email", $email);
    $sentencia->bindParam(":role", $role);
    $sentencia->execute();

    header("Location:login.php?mensaje=Registro agregado");
    exit;
}
?>

<head>
        <title>Crear usuario</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />
        <!-- Bootstrap CSS v5.2.1 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
    </head>
<div class="container mt-5 mb-5">
    <div class="card">
        <div class="card-header">Crear usuario</div>
        <div class="card-body">
            <form action="" method="post">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" name="usuario" id="usuario" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="clave" class="form-label">Clave</label>
                    <input type="password" name="clave" id="clave" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Rol</label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="USUARIO">USUARIO</option>
                        <option value="ADMINISTRADOR">ADMINISTRADOR</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Guardar</button>
                <a href="login.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>


