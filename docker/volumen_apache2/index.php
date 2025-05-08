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
              VALUES ($id_container, $pes, NOW()::TIMESTAMP(0), '$uid')";
    // var_dump($query);
    $resultado = pg_query($conector, $query);
    
    if ($resultado) {
        echo "1";
    } else {
        echo "-1";
    }
    
} else if (isset($_GET['activo'], $_GET['id_container'])) {
    $id_container = (int)$_GET['id_container'];
    $activo = ($_GET['activo'] == 1) ? 'true' : 'false';
    $query = "UPDATE container SET activo = $activo WHERE id = $id_container";
    $resultado = pg_query($conector, $query);
    // var_dump($query);
    if ($resultado) {
        echo "1";
    } else {
        echo "-1";
    }
} else if (isset($_GET['comprovacio'], $_GET['id_container'])) {
    $id_container = (int)$_GET['id_container'];
    $activo = ($_GET['comprovacio'] == 1) ? 'true' : 'false';
    $query = "SELECT activo FROM container WHERE id = $id_container";
    $resultado = pg_query($conector, $query);
    // var_dump($query);
    if ($resultado && $registro = pg_fetch_assoc($resultado)) {
        $enviar=($registro['activo']);
        echo "$enviar";
    } else {
        $enviar=($registro['activo']);
        echo "$enviar";
    }
} else if (isset($_GET['lat'], $_GET['lng'], $_GET['id_container']) && count($_GET) === 3){
    $id_container = (int)$_GET['id_container'];
    $lat = (double)$_GET['lat']; 
    $lng = (double)$_GET['lng']; 
    $query = "UPDATE container SET latitud_actual = $lat, longitud_actual = $lng WHERE id = $id_container";
    $resultado = pg_query($conector, $query);
    // var_dump($query);
    if ($resultado) {
        echo "1";
    } else {
        echo "-1";
    }
} else if (isset($_GET['lat'], $_GET['lng'], $_GET['id_container'], $_GET['pes'])){
    $id_container = (int)$_GET['id_container'];
    $lat = (double)$_GET['lat']; 
    $lng = (double)$_GET['lng'];
    $pes = (float)$_GET['pes']; 
    $query = "INSERT INTO vaciados (id_container, peso_vaciado, fecha_vaciado, latitud_vaciado, longitud_vaciado)
              VALUES ($id_container, $pes, NOW()::TIMESTAMP(0), $lat, $lng)";
    $resultado = pg_query($conector, $query);
    var_dump($query);
    if ($resultado) {
        echo "1";
    } else {
        echo "-1";
    }
}

pg_close($conector);
?>