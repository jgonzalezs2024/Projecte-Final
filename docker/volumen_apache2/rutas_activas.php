<?php
include('funciones.php');
$conexion = conectar_base_de_datos();

$sql = "
SELECT 
    rutas_activas.id_ruta,
    container.tipo,
    rutas_activas.fecha_inicio
FROM rutas_activas
JOIN container ON rutas_activas.id_contenedor = container.id
ORDER BY rutas_activas.id_ruta;
";

$resultado = pg_query($conexion, $sql);
$registros = [];
while ($fila = pg_fetch_assoc($resultado)) {
    $registros[] = $fila;
}

pg_close($conexion);

$rutas = [];

foreach ($registros as $fila) {
    $id_ruta = $fila['id_ruta'];
    $tipo = $fila['tipo'];
    $fecha = $fila['fecha_inicio'];

    if (!isset($rutas[$id_ruta])) {
        $rutas[$id_ruta]['tipos'] = [];
        $rutas[$id_ruta]['fecha_inicio'] = $fecha;
    }

    if (!in_array($tipo, $rutas[$id_ruta]['tipos'])) {
        $rutas[$id_ruta]['tipos'][] = $tipo;
    }
}



?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Rutas Activas</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <header>
        <div class="container">
            <h1>Rutas Activas</h1>
            <nav>
                <ul>
                    <li><a href="inicio.php">Inicio</a></li>
                    <li><a href="contenedores.php" class="active">Contenedores</a></li>
                    <li><a href="estadisticas.php">Estadísticas</a></li>
                    <li><a href="usuarios.php">Usuarios</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2>Detalle de la ruta</h2>
        <table class="tabla-contenedores">
            <thead>
                <tr>
                    <th>ID Ruta</th>
                    <th>Tipos de Contenedor</th>
                    <th>Fecha Inicio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($rutas)): ?>
                    <tr><td colspan="4">No hay rutas activas</td></tr>
                <?php else: ?>
                <?php foreach ($rutas as $id_ruta => $data): ?>
                    <tr>
                        <td><a class="link-numero" href="ver_ruta.php?id_ruta=<?php echo htmlspecialchars($id_ruta); ?>"><?php echo htmlspecialchars($id_ruta); ?></a></td>
                        <td><?php echo htmlspecialchars(implode(', ', $data['tipos'])); ?></td>
                        <td><?php echo htmlspecialchars($data['fecha_inicio']); ?></td>
                        <td>
                            <form method="POST" action="eliminar_ruta.php" onsubmit="return confirm('¿Seguro que quieres eliminar la ruta <?php echo htmlspecialchars($id_ruta); ?>?');">
                                <input type="hidden" name="id_ruta" value="<?php echo htmlspecialchars($id_ruta); ?>">
                                <button type="submit" class="btn-cambiar-estado">Eliminar Ruta</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php endif; ?>
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
