<?php
include('funciones.php');

// Conexión a la base de datos PostgreSQL
$conexion = conectar_base_de_datos();

// Obtener el ID del contenedor desde el parámetro GET, o 0 si no existe
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ==============================================
// CONSULTA: Obtener datos del contenedor específico
// ==============================================
$consulta = "SELECT id, tipo, latitud_actual, longitud_actual FROM container WHERE id = $1";
$resultado = pg_query_params($conexion, $consulta, [$id]);

// Obtener el primer registro con los datos del contenedor
$contenedor = pg_fetch_assoc($resultado);

// Extraer latitud y longitud para el mapa
$lat = $contenedor['latitud_actual'];
$lng = $contenedor['longitud_actual'];

// API key 
$clave_api = "xxx";

// Construir URL para mostrar mapa con la ubicación del contenedor
$map_url = "https://www.google.com/maps/embed/v1/place?key=$clave_api&q=$lat,$lng";

// ==============================================
// CONSULTA: Obtener la fecha del último vaciado del contenedor
// ==============================================
$consulta_vaciado = "SELECT fecha_vaciado FROM vaciados WHERE id_container = $1 ORDER BY fecha_vaciado DESC LIMIT 1";
$resultado_vaciado = pg_query_params($conexion, $consulta_vaciado, [$id]);

// Obtener la fecha de vaciado más reciente
$vaciado = pg_fetch_assoc($resultado_vaciado);
$ultima_fecha_vaciado = $vaciado ? date("d/m/Y H:i", strtotime($vaciado['fecha_vaciado'])) : "Sin registro";

// Cerrar conexión con la base de datos
pg_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contenedor</title>
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
            <h1>Mapa</h1>
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
                        <td><span><?php echo htmlspecialchars($contenedor['tipo']); ?></span></td>
                        <td><?php echo $ultima_fecha_vaciado; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="map-box">
            <iframe 
                src="<?php echo htmlspecialchars($map_url); ?>"
                allowfullscreen
                loading="lazy">
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
