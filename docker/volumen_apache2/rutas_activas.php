<?php
// Incluye el archivo con funciones comunes como la conexión a la base de datos
include('funciones.php');

// Establece la conexión a la base de datos
$conexion = conectar_base_de_datos();

// Consulta SQL para obtener ID de ruta, tipo de contenedor y fecha de inicio de todas las rutas activas
$sql = "
SELECT 
    rutas_activas.id_ruta,
    container.tipo,
    rutas_activas.fecha_inicio
FROM rutas_activas
JOIN container ON rutas_activas.id_contenedor = container.id
ORDER BY rutas_activas.id_ruta;
";

// Ejecuta la consulta
$resultado = pg_query($conexion, $sql);

// Inicializa un array para almacenar los registros
$registros = [];
while ($fila = pg_fetch_assoc($resultado)) {
    $registros[] = $fila; // Guarda cada fila como un array
}

// Cierra la conexión a la base de datos
pg_close($conexion);

// Procesa los registros para agrupar por ID
$rutas = [];
foreach ($registros as $fila) {
    $id_ruta = $fila['id_ruta'];
    $tipo = $fila['tipo'];
    $fecha = $fila['fecha_inicio'];

    // Si aún no se ha registrado esta ruta, inicializa su estructura
    if (!isset($rutas[$id_ruta])) {
        $rutas[$id_ruta]['tipos'] = [];
        $rutas[$id_ruta]['fecha_inicio'] = $fecha;
    }

    // Añade el tipo si no ha sido añadido antes (evita duplicados)
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
                            <td colspan="4">
                                <a href="ver_ruta.php?id_ruta=<?php echo htmlspecialchars($id_ruta); ?>" style="display: block; text-decoration: none; color: inherit;">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td><?php echo htmlspecialchars($id_ruta); ?></td>
                                            <td><?php echo htmlspecialchars(implode(', ', $data['tipos'])); ?></td>
                                            <td><?php echo htmlspecialchars($data['fecha_inicio']); ?></td>
                                            <td>
                                                <form method="POST" action="eliminar_ruta.php" onsubmit="return confirm('¿Seguro que quieres eliminar la ruta <?php echo htmlspecialchars($id_ruta); ?>?');">
                                                    <input type="hidden" name="id_ruta" value="<?php echo htmlspecialchars($id_ruta); ?>">
                                                    <button type="submit" class="btn-cambiar-estado">Eliminar Ruta</button>
                                                </form>
                                            </td>
                                        </tr>
                                    </table>
                                </a>
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
