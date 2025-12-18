<?php 
// INICIAR SESION  cambiar passwor por contraseña
session_start();
if($_POST){
    include("./bd.php");
    $sentencia = $conexion->prepare("SELECT * , count(*) AS n_usuario FROM usuarios 
    WHERE usuario=:usuario AND clave=:clave");
    $usuario = $_POST["usuario"];
    $clave = $_POST["clave"];

    $sentencia->bindParam(":usuario",$usuario);
    $sentencia->bindParam(":clave",$clave);
    $sentencia->execute();
    $registro = $sentencia->fetch(PDO::FETCH_LAZY);

    if($registro["n_usuario"]>0){
        $_SESSION["usuario"]=$registro["usuario"];
        $_SESSION["user_id"]=$registro["user_id"];
        $_SESSION["email"]=$registro["email"];
        $_SESSION["role"]=$registro["role"];
        $_SESSION["logueado"]=true;
        header("Location: index.php");
    }else{
        $mensaje="Error: El usuario o la contraseña son incorrectos.";
    }

}
?>
<!doctype html>
<html lang="es">
    <head>
        <title>identificar usuario para iniciar sesion</title>
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

    <body>
        <header>
            
        </header>    
            <main class="container">
                <div class="row">
                    <div class="col-md-4"></div>
                        <div class="col-md-4">
                        <br /><br /> <br> <br> <br>
                        <div class="card">
                            <div class="card-header" style="text-align: center;"><b>iniciar sesion</b></div>
                            <div class="card-body">
                                
                                <?php if(isset($mensaje)){ ?>
                                    <div class="alert alert-danger" role="alert">
                                    <strong><?php echo $mensaje; ?></strong>
                                </div>
                                <?php } ?>
                                <form action="" method="post">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" name="usuario" id="usuario"
                                        aria-describedby="helpId" placeholder="Usuario"/>
                                    </div>
                                    <div class="mb-3">
                                    <input type="password" class="form-control" name="clave" id="clave"
                                    aria-describedby="helpId" placeholder="clave"/>
                                    </div>
                                    <a href="crear_usuario.php" class="btn btn-success">Crear una cuenta</a>
                                    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                                </form>
                            </div>
                        </div>
                        </div>
            </main>
    </body>