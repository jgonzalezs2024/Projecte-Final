<?php
// Incluir el archivo de funciones
include('funciones.php');

// Datos de conexión a PostgreSQL
$servername = "db";    // O la dirección IP de tu servidor PostgreSQL
$username = "root";    // Tu usuario de PostgreSQL
$password = "root"; // Tu contraseña de PostgreSQL
$dbname = "arduino";        // El nombre de tu base de datos

// Clave API de Google Maps para Geocoding
$api_key = 'xxx'; // Reemplaza con tu clave de API

// Conexión a la base de datos PostgreSQL usando PDO
try {
    $conn = new PDO("pgsql:host=$servername;dbname=$dbname", $username, $password);
} catch (PDOException $e) {
    echo "Conexión fallida: " . $e->getMessage();
}

// Consulta para obtener el listado de contenedores con latitud y longitud
$sql_contenedores = "SELECT id, tipo, latitud_actual, longitud_actual, activo, poblacion FROM container";
$result_contenedores = $conn->query($sql_contenedores);
$contenedores = $result_contenedores->fetchAll(PDO::FETCH_ASSOC);

$conn = null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contenedores - Administración</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Panel de Administración - Contenedores</h1>
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

        <!-- Tabla con los contenedores -->
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
                    // Obtener la dirección utilizando la función de geocodificación
                    $direccion = obtenerCalleYNumero($contenedor['latitud_actual'], $contenedor['longitud_actual'], $api_key);
                ?>
                    <tr>
                        <td><?php echo $contenedor['id']; ?></td>
                        <td><?php echo $contenedor['tipo']; ?></td>
                        <td><?php echo $direccion; ?></td> <!-- Aquí mostramos la dirección obtenida -->
                        <td>
                            <?php echo ($contenedor['activo'] === true) ? 'Activo' : 'Inactivo'; ?>
                        </td>
                        <td><?php echo $contenedor['poblacion']; ?></td> <!-- Mostrar la fecha de último llenado -->
                        <td>
                            <!-- Botón para cambiar estado -->
                            <a href="cambiar_estado.php?id=<?php echo $contenedor['id']; ?>" class="btn-cambiar-estado">Cambiar Estado</a>

                            <a href="ver_contenedor.php?id=<?php echo $contenedor['id']; ?>" 
                            class="btn-cambiar-estado" 
                            style="margin-left: 10px;">
                            Ver en Mapa
                            </a>
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