<?php
include('funciones.php');

// ==============================================
// CONEXIÓN A LA BASE DE DATOS
// ==============================================
$conexion = conectar_base_de_datos();

// ==============================================
// ID RECIBIDO POR GET
// ==============================================
$id_contenedor = $_GET['id'];

// Consulta el estado actual del contenedor
$consulta = "SELECT activo FROM container WHERE id = $1";
$resultado = pg_query_params($conexion, $consulta, array($id_contenedor));

if ($fila = pg_fetch_assoc($resultado)) {
    // Alterna el estado: si está activo lo cambia a inactivo, y viceversa
    $nuevo_estado = ($fila['activo'] === 't') ? 'f' : 't';

    // Actualiza el estado del contenedor en la base de datos
    $actualizar = "UPDATE container SET activo = $1 WHERE id = $2";
    $resultado_actualizar = pg_query_params($conexion, $actualizar, array($nuevo_estado, $id_contenedor));

    // Redirecciona a la vista de contenedores si la actualización fue exitosa
    if ($resultado_actualizar) {
        header('Location: contenedores.php');
        exit();
    }
}

// Cierra la conexión a la base de datos
pg_close($conexion);
?>
