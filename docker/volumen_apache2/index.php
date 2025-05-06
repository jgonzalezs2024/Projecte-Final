<?php
//$conector = mysqli_connect("localhost", "root", "root", "arduino_db", "5432");
$conector = pg_connect("host=db port=5432 dbname=arduino user=root password=root");
if (!$conector) {
    echo "ERROR: No se pudo conectar a PostgreSQL.";
    exit;
}
if (isset($_GET['rfid']) && count($_GET) === 1){
    $uid = $_GET['rfid'];
    $consulta = "SELECT num_serie FROM rfid WHERE num_serie = '$uid'";
    $resultado = pg_query($conector, $consulta);

    if ($resultado && $registro = pg_fetch_assoc($resultado)) {
        echo "1";
    } else {
        echo "-1";
    }
} else if (isset($_GET['rfid'], $_GET['id_container'], $_GET['pes']) && count($_GET) > 1) {
    $uid = $_GET['rfid'];
    $id_container = (int)$_GET['id_container'];
    $pes = (float)$_GET['pes'];
    $query = "INSERT INTO metricas (id_container, peso_actual, fecha_actual, num_serie)
              VALUES ($id_container, $pes, NOW(), '$uid')";
    // var_dump($query);
    $resultado = pg_query($conector, $query);
    
    if ($resultado) {
        echo "1";
    } else {
        echo "-1";
    }
    
}
pg_close($conector);
?>