<?php
include('funciones.php');
$conexion = conectar_base_de_datos();
$consulta_contenedores = "SELECT id, tipo, latitud_actual, longitud_actual, activo, poblacion FROM container";
$resultado_contenedores = pg_query($conexion, $consulta_contenedores);
$contenedores = [];
if ($resultado_contenedores) {
    while ($contenedor = pg_fetch_assoc($resultado_contenedores)) {
        $contenedores[] = $contenedor;
    }
}
pg_close($conexion);
$clave_api = "xxx"
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contenedores</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <header>
        <div class="container">
            <h1>Contenedores</h1>
            <nav>
                <ul>
                    <li><a href="inicio.php">Inicio</a></li>
                    <li><a href="estadisticas.php">Estadísticas</a></li>
                    <li><a href="usuarios.php">Usuarios</a></li>
                    <li><a href="rutas_activas.php">Rutas Activas</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2>Listado de Contenedores</h2>
        <table class="tabla-contenedores">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Ubicación</th>
                    <th>Estado</th>
                    <th>Población</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contenedores as $contenedor): 
                    $direccion = obtenerCalleYNumero($contenedor['latitud_actual'], $contenedor['longitud_actual'], $clave_api);
                ?>
                <tr>
                    <td><?php echo $contenedor['id']; ?></td>
                    <td><?php echo $contenedor['tipo']; ?></td>
                    <td><?php echo $direccion; ?></td>
                    <td><?php echo ($contenedor['activo'] === 't') ? 'Activo' : 'Inactivo'; ?></td>
                    <td><?php echo $contenedor['poblacion']; ?></td>
                    <td>
                        <a href="cambiar_estado.php?id=<?php echo $contenedor['id']; ?>" class="btn-cambiar-estado">Cambiar Estado</a>
                        <a href="ver_contenedor.php?id=<?php echo $contenedor['id']; ?>" class="btn-cambiar-estado btn-mapa">Ver en Mapa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Sistema de Gestión de Contenedores</p>
        </div>
    </footer>
</body>
</html>
