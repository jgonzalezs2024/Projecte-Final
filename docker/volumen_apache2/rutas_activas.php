<?php
// Conexión a la base de datos
$servername = "db";
$username = "root";
$password = "root";
$dbname = "arduino";

try {
    $conn = new PDO("pgsql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

// Consulta para obtener rutas agrupadas con tipos de contenedor concatenados
$sql = "
    SELECT 
        ra.id_ruta,
        STRING_AGG(DISTINCT c.tipo, ', ') AS tipos_contenedores,
        MIN(ra.fecha_inicio) AS fecha_inicio -- fecha más temprana de la ruta
    FROM rutas_activas ra
    JOIN container c ON ra.id_contenedor = c.id
    GROUP BY ra.id_ruta
    ORDER BY ra.id_ruta
";

$stmt = $conn->query($sql);
$rutas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$conn = null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Rutas Activas - Administración</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <header>
        <div class="container">
            <h1>Panel de Administración</h1>
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
        <h2>Rutas Activas</h2>
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
                <?php foreach ($rutas as $ruta): ?>
                    <tr style="cursor:pointer;" onclick="window.location.href='ver_ruta.php?id_ruta=<?php echo $ruta['id_ruta']; ?>'">
                        <td><?php echo htmlspecialchars($ruta['id_ruta']); ?></td>
                        <td><?php echo htmlspecialchars($ruta['tipos_contenedores']); ?></td>
                        <td><?php echo htmlspecialchars($ruta['fecha_inicio']); ?></td>
                        <td>
                        <form method="POST" action="eliminar_ruta.php" onsubmit="return confirm('¿Seguro que quieres eliminar la ruta <?php echo $ruta['id_ruta']; ?>?');" onclick="event.stopPropagation();">
                            <input type="hidden" name="id_ruta" value="<?php echo $ruta['id_ruta']; ?>">
                            <button type="submit" class="btn-cambiar-estado">Eliminar Ruta</button>
                        </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($rutas)): ?>
                    <tr><td colspan="4">No hay rutas activas</td></tr>
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
