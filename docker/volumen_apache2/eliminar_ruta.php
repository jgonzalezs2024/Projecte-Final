<?php
// Datos conexión (igual que antes)
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_ruta'])) {
    $id_ruta = intval($_POST['id_ruta']);

    // Eliminar todos los registros con id_ruta dado
    $sql = "DELETE FROM rutas_activas WHERE id_ruta = :id_ruta";
    $stmt = $conn->prepare($sql);
    
    try {
        $stmt->execute(['id_ruta' => $id_ruta]);
        // Después de borrar, redirigir a la página de rutas activas
        header("Location: rutas_activas.php?msg=Ruta $id_ruta eliminada");
        exit;
    } catch (PDOException $e) {
        die("Error eliminando la ruta: " . $e->getMessage());
    }
} else {
    die("Solicitud inválida");
}
