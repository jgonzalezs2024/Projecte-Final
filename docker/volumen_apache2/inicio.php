<?php
$servername = "db";
$username = "root";
$password = "root";
$dbname = "arduino";

try {
    $conn = new PDO("pgsql:host=$servername;dbname=$dbname", $username, $password);
} catch (PDOException $e) {
    echo "Conexión fallida: " . $e->getMessage();
}

$consulta_container = "SELECT COUNT(*) AS total_contenedores FROM container";
$consulta_container_activos = "SELECT COUNT(*) AS contenedores_activos FROM container WHERE activo = 't'";
$consulta_total_usuarios = "SELECT COUNT(*) AS total_usuarios FROM rfid";

$resultado_container = $conn->query($consulta_container);
$resultado_container_activos = $conn->query($consulta_container_activos);
$resultado_total_usuarios = $conn->query($consulta_total_usuarios);

$total_contenedores = $resultado_container->fetch(PDO::FETCH_ASSOC)['total_contenedores'];
$contenedores_activos = $resultado_container_activos->fetch(PDO::FETCH_ASSOC)['contenedores_activos'];
$total_usuarios = $resultado_total_usuarios->fetch(PDO::FETCH_ASSOC)['total_usuarios'];

$consulta_rutas_activas = "SELECT COUNT(DISTINCT id_ruta) AS total_rutas_activas FROM rutas_activas";
$resultado_rutas_activas = $conn->query($consulta_rutas_activas);
$total_rutas_activas = $resultado_rutas_activas->fetch(PDO::FETCH_ASSOC)['total_rutas_activas'];

$conn = null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración</title>
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
