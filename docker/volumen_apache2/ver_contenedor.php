<?php
$servername = "db";
$username = "root";
$password = "root";
$dbname = "arduino";

$api_key = 'xxx';

try {
    $conn = new PDO("pgsql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("ID de contenedor inválido");
}

$sql = "SELECT id, tipo, latitud_actual, longitud_actual FROM container WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $id]);
$contenedor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$contenedor) {
    die("Contenedor no encontrado");
}

$lat = $contenedor['latitud_actual'];
$lng = $contenedor['longitud_actual'];
$map_url = "https://www.google.com/maps/embed/v1/place?key=$api_key&q=$lat,$lng";

// Obtener la última fecha de vaciado
$sql_vaciado = "SELECT fecha_vaciado FROM vaciados WHERE id_container = :id ORDER BY fecha_vaciado DESC LIMIT 1";
$stmt_vaciado = $conn->prepare($sql_vaciado);
$stmt_vaciado->execute(['id' => $id]);
$vaciado = $stmt_vaciado->fetch(PDO::FETCH_ASSOC);
$ultima_fecha_vaciado = $vaciado ? date("d/m/Y H:i", strtotime($vaciado['fecha_vaciado'])) : "Sin registro";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contenedor #<?php echo htmlspecialchars($contenedor['id']); ?></title>
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

        .contenedor-info {
            margin-bottom: 20px;
        }

        .contenedor-info p {
            font-size: 18px;
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Contenedor - Mapa</h1>
            <nav>
                <ul>
                    <li><a href="inicio.php">Inicio</a></li>
                    <li><a href="contenedores.php" class="active">Contenedores</a></li>
                    <li><a href="estadisticas.php">Estadísticas</a></li>
                    <li><a href="usuarios.php">Usuarios</a></li>
                    <li><a href="rutas_activas.php">Rutas Activas</a></li>

                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2>Ubicación del Contenedor</h2>
        <div class="contenedor-info">
            <table class="tabla-contenedores">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tipo</th>
            <th>Último vaciado</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?php echo htmlspecialchars($contenedor['id']); ?></td>
            <td>
                <span class="tag tag-<?php echo strtolower(htmlspecialchars($contenedor['tipo'])); ?>">
                    <?php echo ucfirst(htmlspecialchars($contenedor['tipo'])); ?>
                </span>
            </td>
            <td><?php echo $ultima_fecha_vaciado; ?></td>
        </tr>
    </tbody>
</table>

        </div>
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
