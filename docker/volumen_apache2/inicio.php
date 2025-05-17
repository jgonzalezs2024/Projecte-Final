<?php
include('funciones.php');
$conexion = conectar_base_de_datos();

$consulta_container = "SELECT COUNT(*) AS total_contenedores FROM container";
$resultado_container = pg_query($conexion, $consulta_container);
$total_contenedores = 0;
if ($fila = pg_fetch_assoc($resultado_container)) {
    $total_contenedores = $fila['total_contenedores'];
}

$consulta_container_activos = "SELECT COUNT(*) AS contenedores_activos FROM container WHERE activo = 't'";
$resultado_container_activos = pg_query($conexion, $consulta_container_activos);
$contenedores_activos = 0;
if ($fila = pg_fetch_assoc($resultado_container_activos)) {
    $contenedores_activos = $fila['contenedores_activos'];
}

$consulta_total_usuarios = "SELECT COUNT(*) AS total_usuarios FROM rfid";
$resultado_total_usuarios = pg_query($conexion, $consulta_total_usuarios);
$total_usuarios = 0;
if ($fila = pg_fetch_assoc($resultado_total_usuarios)) {
    $total_usuarios = $fila['total_usuarios'];
}

$consulta_rutas_activas = "SELECT COUNT(DISTINCT id_ruta) AS total_rutas_activas FROM rutas_activas";
$resultado_rutas_activas = pg_query($conexion, $consulta_rutas_activas);
$total_rutas_activas = 0;
if ($fila = pg_fetch_assoc($resultado_rutas_activas)) {
    $total_rutas_activas = $fila['total_rutas_activas'];
}

pg_close($conexion);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Administración</h1>
            <nav>
                <ul>
                    <li><a href="contenedores.php">Contenedores</a></li>
                    <li><a href="estadisticas.php">Estadísticas</a></li>
                    <li><a href="usuarios.php">Usuarios</a></li>
                    <li><a href="rutas_activas.php">Rutas Activas</a></li>

                </ul>
            </nav>
        </div>
    </header>
    <main class="container">
        <section class="resumen">
            <h2>Resumen del Sistema</h2>
            <div class="info">
                <div class="info-box">
                    <h3>Total de Contenedores</h3>
                    <p><?php echo $total_contenedores; ?></p>
                </div>
                <div class="info-box">
                    <h3>Contenedores Activos</h3>
                    <p><?php echo $contenedores_activos; ?></p>
                </div>
                <div class="info-box">
                    <h3>Rutas Activas</h3>
                    <p><?php echo $total_rutas_activas; ?></p>
                </div>
                <div class="info-box">
                    <h3>Total de Usuarios</h3>
                    <p><?php echo $total_usuarios; ?></p>
                </div>
            </div>
        </section>
    </main>
    <footer>
        <div class="container">
            <p>&copy; 2025 Sistema de Gestión de Contenedores</p>
        </div>
    </footer>
</body>
</html>
