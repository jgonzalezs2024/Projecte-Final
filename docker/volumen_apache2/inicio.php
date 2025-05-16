<?php
// Datos de conexión a PostgreSQL
$servername = "db";    // O la dirección IP de tu servidor PostgreSQL
$username = "root";    // Tu usuario de PostgreSQL
$password = "root"; // Tu contraseña de PostgreSQL
$dbname = "arduino";        // El nombre de tu base de datos

// Conexión a la base de datos PostgreSQL usando PDO
try {
    $conn = new PDO("pgsql:host=$servername;dbname=$dbname", $username, $password);
} catch (PDOException $e) {
    // Si ocurre un error, mostramos el mensaje de error
    echo "Conexión fallida: " . $e->getMessage();
}

// Consultas para obtener algunos datos generales de la base de datos
$sql_contenedores = "SELECT COUNT(*) AS total_contenedores FROM container";
$sql_activos = "SELECT COUNT(*) AS contenedores_activos FROM container WHERE activo = 't'";

// Nueva consulta para contar los usuarios
$sql_usuarios = "SELECT COUNT(*) AS total_usuarios FROM rfid";

// Ejecutamos las consultas
$result_contenedores = $conn->query($sql_contenedores);
$result_activos = $conn->query($sql_activos);
$result_usuarios = $conn->query($sql_usuarios);

// Obtener los resultados
$total_contenedores = $result_contenedores->fetch(PDO::FETCH_ASSOC)['total_contenedores'];
$contenedores_activos = $result_activos->fetch(PDO::FETCH_ASSOC)['contenedores_activos'];
$total_usuarios = $result_usuarios->fetch(PDO::FETCH_ASSOC)['total_usuarios'];

// Consultar los contenedores inactivos
// $sql_inactivos = "SELECT COUNT(*) AS contenedores_inactivos FROM container WHERE activo = 'f'";
// $result_inactivos = $conn->query($sql_inactivos);
// $contenedores_inactivos = $result_inactivos->fetch(PDO::FETCH_ASSOC)['contenedores_inactivos'];

$sql_rutas_activas = "SELECT COUNT(DISTINCT id_ruta) AS total_rutas_activas FROM rutas_activas";
$result_rutas_activas = $conn->query($sql_rutas_activas);
$total_rutas_activas = $result_rutas_activas->fetch(PDO::FETCH_ASSOC)['total_rutas_activas'];

$conn = null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración - Contenedores</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Panel de Administración</h1>
            <nav>
                <ul>
                    <li><a href="contenedores.php">Contenedores</a></li>
                    <li><a href="estadisticas.php">Estadísticas</a></li>
                    <li><a href="usuarios.php">Usuarios</a></li>
                    <li><a href="rutas_activas.php">Rutas Activas</a></li>

                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="resumen">
            <h2>Resumen del Sistema</h2>
            <div class="info">
                <div class="info-box">
                    <h3>Total de Contenedores</h3>
                    <p><?php echo $total_contenedores; ?></p>
                </div>
                <div class="info-box">
                    <h3>Contenedores Activos</h3>
                    <p><?php echo $contenedores_activos; ?></p>
                </div>
                <div class="info-box">
                    <h3>Rutas Activas</h3>
                    <p><?php echo $total_rutas_activas; ?></p>
                </div>
                <div class="info-box">
                    <h3>Total de Usuarios</h3>
                    <p><?php echo $total_usuarios; ?></p>
                </div>


            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Sistema de Gestión de Contenedores</p>
        </div>
    </footer>
</body>
</html>
