<?php
include('funciones.php');
$conexion = conectar_base_de_datos();
$id_ruta = $_POST['id_ruta'];
$consulta = "DELETE FROM rutas_activas WHERE id_ruta = $id_ruta";
pg_query($conexion, $consulta);
header("Location: rutas_activas.php");
exit;
pg_close($conexion);
?>
