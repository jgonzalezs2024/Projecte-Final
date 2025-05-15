<?php
// Incluir el archivo de funciones
include('funciones.php');

// Datos de conexión a PostgreSQL
$servername = "db";    // O la dirección IP de tu servidor PostgreSQL
$username = "root";    // Tu usuario de PostgreSQL
$password = "root"; // Tu contraseña de PostgreSQL
$dbname = "arduino";        // El nombre de tu base de datos

// Conexión a la base de datos PostgreSQL usando PDO
try {
    $conn = new PDO("pgsql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Conexión fallida: " . $e->getMessage();
}

// Obtener el ID del contenedor desde la URL
$id_contenedor = $_GET['id'];

// Obtener el estado actual del contenedor
$sql = "SELECT activo FROM container WHERE id = :id_contenedor";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_contenedor', $id_contenedor);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Cambiar el estado
if ($result) {
    $nuevo_estado = ($result['activo'] === true) ? 'f' : 't'; // Alternar el estado entre 't' y 'f'
    
    $sql_update = "UPDATE container SET activo = :nuevo_estado WHERE id = :id_contenedor";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bindParam(':nuevo_estado', $nuevo_estado);
    $stmt_update->bindParam(':id_contenedor', $id_contenedor);
    $stmt_update->execute();
    
    // Redirigir de vuelta a la página de contenedores
    header('Location: contenedores.php');
    exit();
} else {
    echo "Contenedor no encontrado.";
}

$conn = null;
?>
