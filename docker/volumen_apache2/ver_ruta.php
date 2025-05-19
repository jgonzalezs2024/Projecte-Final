<?php
include('funciones.php');
// Conexión a la base de datos PostgreSQL
$conexion = conectar_base_de_datos();

// Obtiene el ID de la ruta desde la URL, o 0 si no está definido
$id_ruta = $_GET['id_ruta'];
$clave_api = "xxx";

// Consulta los datos de los contenedores asociados a la ruta
$sql = "
    SELECT 
        container.id, 
        container.tipo, 
        container.latitud_actual, 
        container.longitud_actual
    FROM rutas_activas
    JOIN container ON rutas_activas.id_contenedor = container.id
    WHERE rutas_activas.id_ruta = $1
    ORDER BY rutas_activas.id ASC
";

// Ejecuta la consulta con parámetro seguro
$resultado = pg_query_params($conexion, $sql, [$id_ruta]);

// Guarda los resultados en un array
$contenedores = [];
while ($fila = pg_fetch_assoc($resultado)) {
    $contenedores[] = $fila;
}

// Define origen y destino en función del primer y último contenedor
$origen = $contenedores[0]['latitud_actual'] . ',' . $contenedores[0]['longitud_actual'];
$destinacion = end($contenedores)['latitud_actual'] . ',' . end($contenedores)['longitud_actual'];

// Si hay contenedores intermedios, se añaden como waypoints
$destinos = [];
for ($i = 1; $i < count($contenedores) - 1; $i++) {
    $destinos[] = $contenedores[$i]['latitud_actual'] . ',' . $contenedores[$i]['longitud_actual'];
}

// Construye la cadena de destinos intermedios
$destinos_string = '';
foreach ($destinos as $destino) {
    $destinos_string .= $destino . '|';
}
$destinos_string = rtrim($destinos_string, '|'); // Elimina la última barra vertical

// Construye la URL de Google Maps con origen, destino y waypoints si los hay
$url_maps = "https://www.google.com/maps/embed/v1/directions?key=$clave_api&origin=$origen&destination=$destinacion";
$url_maps .= "&waypoints=" . urlencode($destinos_string);


// Cierra la conexión con la base de datos
pg_close($conexion);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ver Ruta</title>
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
                    <li><a href="inicio.php">Inicio</a></li>
                    <li><a href="contenedores.php">Contenedores</a></li>
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
                src="<?php echo htmlspecialchars($url_maps); ?>"
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
