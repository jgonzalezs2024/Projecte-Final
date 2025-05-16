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

// Consulta básica, sin agrupación, para obtener todos los registros
$sql = "
SELECT 
    ra.id_ruta,
    c.tipo,
    ra.fecha_inicio
FROM rutas_activas ra
JOIN container c ON ra.id_contenedor = c.id
ORDER BY ra.id_ruta;
";

$stmt = $conn->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$conn = null;

// Agrupar datos por id_ruta en PHP
$rutas = [];
foreach ($rows as $row) {
    $id = $row['id_ruta'];
    if (!isset($rutas[$id])) {
        $rutas[$id] = [
            'tipos' => [],
            'fecha_inicio' => $row['fecha_inicio'],
        ];
    }
    // Guardar fecha mínima
    if ($row['fecha_inicio'] < $rutas[$id]['fecha_inicio']) {
        $rutas[$id]['fecha_inicio'] = $row['fecha_inicio'];
    }
    // Guardar tipos únicos
    if (!in_array($row['tipo'], $rutas[$id]['tipos'])) {
        $rutas[$id]['tipos'][] = $row['tipo'];
    }
}
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
            <h1>Panel de Administración - Rutas Activas</h1>
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
                <?php if(empty($rutas)): ?>
                    <tr><td colspan="4">No hay rutas activas</td></tr>
                <?php else: ?>
                    <?php foreach ($rutas as $id_ruta => $data): ?>
                        <tr style="cursor:pointer;" onclick="window.location.href='ver_ruta.php?id_ruta=<?php echo htmlspecialchars($id_ruta); ?>'">
                            <td><?php echo htmlspecialchars($id_ruta); ?></td>
                            <td><?php echo htmlspecialchars(implode(', ', $data['tipos'])); ?></td>
                            <td><?php echo htmlspecialchars($data['fecha_inicio']); ?></td>
                            <td>
                                <form method="POST" action="eliminar_ruta.php" onsubmit="return confirm('¿Seguro que quieres eliminar la ruta <?php echo htmlspecialchars($id_ruta); ?>?');" onclick="event.stopPropagation();">
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
