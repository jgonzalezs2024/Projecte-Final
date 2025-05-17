<?php
include('funciones.php');
$conexion = conectar_base_de_datos();
$id_contenedor = $_GET['id'];
$consulta = "SELECT activo FROM container WHERE id = $1";
$resultado = pg_query_params($conexion, $consulta, array($id_contenedor));
if ($fila = pg_fetch_assoc($resultado)) {
    $nuevo_estado = ($fila['activo'] === 't') ? 'f' : 't';

    $actualizar = "UPDATE container SET activo = $1 WHERE id = $2";
    $resultado_actualizar = pg_query_params($conexion, $actualizar, array($nuevo_estado, $id_contenedor));

    if ($resultado_actualizar) {
        header('Location: contenedores.php');
        exit();
    }
}
pg_close($conexion);
?>
