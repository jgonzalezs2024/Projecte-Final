<?php
// Datos de conexión a PostgreSQL
$servername = "db";    // O la dirección IP de tu servidor PostgreSQL
$username = "root";    // Tu usuario de PostgreSQL
$password = "root"; // Tu contraseña de PostgreSQL
$dbname = "arduino";        // El nombre de tu base de datos

try {
    $conn = new PDO("pgsql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Conexión fallida: " . $e->getMessage();
    exit;
}

// Consulta de usuarios
$sql_usuarios = "SELECT num_serie, nombre, apellido, fecha_nacimiento FROM rfid";
$stmt = $conn->query($sql_usuarios);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - Usuarios</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Panel de Administración</h1>
            <nav>
                <ul>
                    <li><a href="inicio.php">Inicio</a></li>
                    <li><a href="contenedores.php">Contenedores</a></li>
                    <li><a href="estadisticas.php">Estadísticas</a></li>
                    <li><a href="rutas_activas.php">Rutas Activas</a></li>

                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2>Listado de Usuarios</h2>
        <table class="tabla-contenedores">
            <thead>
                <tr>
                    <th>RFID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Año de Nacimiento</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($usuarios) > 0): ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['num_serie']) ?></td>
                            <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                            <td><?= htmlspecialchars($usuario['apellido']) ?></td>
                            <td><?= htmlspecialchars($usuario['fecha_nacimiento']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No se encontraron usuarios.</td></tr>
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
