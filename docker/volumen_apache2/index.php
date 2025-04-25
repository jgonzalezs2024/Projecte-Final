<?php

$conector = mysqli_connect("192.168.110.151", "root", "alumne", "arduino");

if (!$conector) {
    echo "ERROR: " . mysqli_connect_error();
    exit;
}



if (isset($_GET['q']) || isset($_GET['r']) || isset($_GET['h']) || isset($_GET['a']) || isset($_GET['b']) || isset($_GET['z']) || isset($_GET['y']) || isset($_GET['w'])) {
    if (isset($_GET['q'])) {
        $uid = $_GET['q'];
        $consulta = "SELECT nombre, apellido FROM usuarios WHERE num_serie_rfid = '$uid' AND baneado = 0";
    } elseif (isset($_GET['r'])) {
        $pin = $_GET['r'];
        $consulta = "SELECT nombre, apellido FROM usuarios WHERE clave = '$pin' AND baneado = 0";
    } elseif (isset($_GET['h'])) {
        $huella = $_GET['h'];
        $consulta = "SELECT nombre, apellido FROM usuarios WHERE huella_id = '$huella' AND baneado = 0";
    }


    
    // Solo ejecuta la consulta SELECT si es necesario
    if (isset($consulta)) {
        $resultado = mysqli_query($conector, $consulta);

        if ($resultado && $registro = mysqli_fetch_assoc($resultado)) {
            echo $registro['nombre'] . " " . $registro['apellido'];
        } else {
            echo "ACCESO DENEGADO";
        }
    }
} else {
    echo "ERROR: Parámetros faltantes";
}

mysqli_close($conector);
?>