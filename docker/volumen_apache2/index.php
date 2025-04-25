<?php

//$conector = mysqli_connect("localhost", "root", "root", "arduino_db", "5432");
$conector = pg_connect("host=db port=5432 dbname=arduino_db user=root password=root");
if (!$conector) {
    echo "ERROR: No se pudo conectar a PostgreSQL.";
    exit;
}
if (isset($_GET['rfid'])){
    $uid = $_GET['rfid'];
    $consulta = "SELECT num_serie FROM rfid WHERE num_serie = '$uid'";
    $resultado = pg_query($conector, $consulta);

    if ($resultado && $registro = pg_fetch_assoc($resultado)) {
        echo "bien";
    } else {
        echo "ACCESO DENEGADO";
    }
}



// if (isset($_GET['q']) || isset($_GET['r']) || isset($_GET['h']) || isset($_GET['a']) || isset($_GET['b']) || isset($_GET['z']) || isset($_GET['y']) || isset($_GET['w'])) {
//     if (isset($_GET['q'])) {
//         $uid = $_GET['q'];
//         $consulta = "SELECT nombre, apellido FROM usuarios WHERE num_serie_rfid = '$uid' AND baneado = 0";
//     } elseif (isset($_GET['r'])) {
//         $pin = $_GET['r'];
//         $consulta = "SELECT nombre, apellido FROM usuarios WHERE clave = '$pin' AND baneado = 0";
//     } elseif (isset($_GET['h'])) {
//         $huella = $_GET['h'];
//         $consulta = "SELECT nombre, apellido FROM usuarios WHERE huella_id = '$huella' AND baneado = 0";
//     }


    
//     // Solo ejecuta la consulta SELECT si es necesario
//     if (isset($consulta)) {
//         $resultado = mysqli_query($conector, $consulta);

//         if ($resultado && $registro = mysqli_fetch_assoc($resultado)) {
//             echo $registro['nombre'] . " " . $registro['apellido'];
//         } else {
//             echo "ACCESO DENEGADO";
//         }
//     }
// } else {
//     echo "ERROR: Parámetros faltantes";
// }

pg_close($conector);
?>