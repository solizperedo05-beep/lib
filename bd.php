<?php
$servidor = "localhost"; //127.0.0.1
$basededatos = "bike_store";
$usuario = "root";
$clave = "";
try {
    $conexion = new PDO("mysql:host=$servidor;dbname=$basededatos",
    $usuario,$clave);
} catch(Exception $ex) {
    echo $ex->getMessage();
}
?>