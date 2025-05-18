<?php
include('funciones.php');

// ==============================================
// CONEXIÓN A LA BASE DE DATOS
// ==============================================
$conexion = conectar_base_de_datos();

// ==============================================
// ID RECIBIDO POR POST
// ==============================================
$id_ruta = $_POST['id_ruta'];

// Construcción y ejecución de la consulta DELETE
$consulta = "DELETE FROM rutas_activas WHERE id_ruta = $id_ruta";
pg_query($conexion, $consulta);

// Redirecciona a la página de rutas activas tras eliminar la ruta
header("Location: rutas_activas.php");
exit;

// Cierra la conexión a la base de datos
pg_close($conexion);
?>
