<?php
// Conexión a la base de datos
$servername = "db";
$username = "root";
$password = "root";
$dbname = "arduino";

try {
    $conn = new PDO("pgsql:host=$servername;dbname=$dbname", $username, $password);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

// Recibir id_ruta por GET
$id_ruta = isset($_GET['id_ruta']) ? intval($_GET['id_ruta']) : 0;

if ($id_ruta <= 0) {
    die("ID de ruta inválido");
}

// Consulta para obtener contenedores de la ruta con lat y lng
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

// Construir la URL de Google Maps con waypoints para la ruta
// Formato: https://www.google.com/maps/dir/?api=1&origin=lat,lng&destination=lat,lng&waypoints=lat,lng|lat,lng

$origin = $contenedores[0]['latitud_actual'] . ',' . $contenedores[0]['longitud_actual'];
$destination = end($contenedores)['latitud_actual'] . ',' . end($contenedores)['longitud_actual'];

// Si hay más de dos puntos, los intermedios van en waypoints
$waypoints = [];
if (count($contenedores) > 2) {
    // Saltamos el primero y el último
    for ($i = 1; $i < count($contenedores) - 1; $i++) {
        $waypoints[] = $contenedores[$i]['latitud_actual'] . ',' . $contenedores[$i]['longitud_actual'];
    }
}

$waypoints_str = implode('|', $waypoints);

$url_maps = "https://www.google.com/maps/dir/?api=1&origin=$origin&destination=$destination";
if ($waypoints_str) {
    $url_maps .= "&waypoints=$waypoints_str";
}

// Redirigir directamente a Google Maps
header("Location: $url_maps");
exit;
