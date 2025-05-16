<?php
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

$id_ruta = isset($_GET['id_ruta']) ? intval($_GET['id_ruta']) : 0;

if ($id_ruta <= 0) {
    die("ID de ruta inválido");
}

$sql = "
    SELECT c.id, c.tipo, c.latitud_actual, c.longitud_actual
    FROM rutas_activas ra
    JOIN container c ON ra.id_contenedor = c.id
    WHERE ra.id_ruta = :id_ruta
    ORDER BY ra.id ASC
";

$stmt = $conn->prepare($sql);
$stmt->execute(['id_ruta' => $id_ruta]);
$contenedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$contenedores) {
    die("No se encontraron contenedores para esta ruta");
}

$origin = $contenedores[0]['latitud_actual'] . ',' . $contenedores[0]['longitud_actual'];
$destination = end($contenedores)['latitud_actual'] . ',' . end($contenedores)['longitud_actual'];

$waypoints = [];
if (count($contenedores) > 2) {
    for ($i = 1; $i < count($contenedores) - 1; $i++) {
        $waypoints[] = $contenedores[$i]['latitud_actual'] . ',' . $contenedores[$i]['longitud_actual'];
    }
}

$waypoints_str = implode('|', $waypoints);

// URL para embebido en iframe
$map_url = "https://www.google.com/maps/embed/v1/directions?key=AIzaSyAWSOFjZn4F9IdNAaW0VlsmFaM1gA1ozEk&origin=$origin&destination=$destination";
if ($waypoints_str) {
    $map_url .= "&waypoints=" . urlencode($waypoints_str);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ver Ruta <?php echo htmlspecialchars($id_ruta); ?></title>
    <link rel="stylesheet" href="styles.css" />
    <style>
        .map-box {
            margin-top: 20px;
            border: 2px solid #ccc;
            border-radius: 12px;
            overflow: hidden;
            height: 500px;
        }

        .map-box iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Detalle de Ruta</h1>
            <nav>
                <ul>
                    <li><a href="contenedores.php">Contenedores</a></li>
                    <li><a href="inicio.php">Inicio</a></li>
                    <li><a href="estadisticas.php">Estadísticas</a></li>
                    <li><a href="usuarios.php">Usuarios</a></li>
                    <li><a href="rutas_activas.php">Rutas Activas</a></li>

                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        
        <h2>Mapa de la Ruta</h2>
        <div class="map-box">
            <iframe
                src="<?php echo htmlspecialchars($map_url); ?>"
                allowfullscreen
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

        
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Sistema de Gestión de Contenedores</p>
        </div>
    </footer>
</body>
</html>
