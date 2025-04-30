<?php

$conector = mysqli_connect("192.168.110.151", "root", "alumne", "arduino");

if (!$conector) {
    echo "ERROR: " . mysqli_connect_error();
    exit;
}

if (isset($_GET['o'])) {
    $una_hora_atras = date('Y-m-d H:i:s', strtotime('-1 hour'));
    $desbanear = "UPDATE usuarios SET baneado = 0, hora_baneo = NULL WHERE hora_baneo <= '$una_hora_atras'";

    if (mysqli_query($conector, $desbanear)) {
        // Verificamos si se ha afectado alguna fila
        if (mysqli_affected_rows($conector) > 0) {
            echo "Usuarios desbaneados correctamente";
        } else {
            echo "No hay usuarios para desbanear";
        }
    } else {
        echo "Error al desbanear usuarios";
    }
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

    if (isset($_GET['a']) && isset($_GET['b'])) {
        $a = $_GET['a'];
        $b = $_GET['b'];
        if ($a == "si") {
            $acceso_correcto = "si";
        } elseif ($a == "no") {
            $acceso_correcto = "no";
        } else {
            echo "Error: El valor de 'a' no es válido. Solo se permiten los valores 'si' o 'no'.<br>";
            exit;
        }

        $credenciales_erroneas = ($b != "no") ? $b : "";

        $fecha = date('Y-m-d H:i:s');
        $insert = "INSERT INTO logs_accesos (fecha, acceso_correcto, credenciales_erroneas) 
                   VALUES ('$fecha', '$acceso_correcto', '$credenciales_erroneas')";

        if (!mysqli_query($conector, $insert)) {
            echo "Error al insertar el registro";
        } else {
            echo "Registro completado correctamente";
        }
    }
    
    // Nueva funcionalidad para verificar 'z' y actualizar 'y'
    if (isset($_GET['z']) && isset($_GET['y'])) {
        $z = $_GET['z'];
        $y = $_GET['y'];
        
        $verificarHuella = "SELECT * FROM usuarios WHERE huella_id = '$z'";
        $resultadoHuella = mysqli_query($conector, $verificarHuella);
        
        if (mysqli_num_rows($resultadoHuella) == 0) {
            $hora_baneo = date('Y-m-d H:i:s');
            // Si la huella no es válida, se banea al usuario con num_serie_rfid = y
            $banearUsuario = "UPDATE usuarios SET baneado = 1, hora_baneo = '$hora_baneo' WHERE num_serie_rfid = '$y'";
            if (mysqli_query($conector, $banearUsuario)) {
                echo "Usuario baneado correctamente";
            } else {
                echo "Error al banear usuario";
            }
        }
    }
    
    // Nueva funcionalidad para verificar 'w' y actualizar 'y'
    if (isset($_GET['w']) && isset($_GET['y'])) {
        $w = $_GET['w'];
        $y = $_GET['y'];
        
        $verificarClave = "SELECT * FROM usuarios WHERE clave = '$w'";
        $resultadoClave = mysqli_query($conector, $verificarClave);
        
        if (mysqli_num_rows($resultadoClave) == 0) {
            $hora_baneo = date('Y-m-d H:i:s');
            // Si la clave no es válida, se banea al usuario con num_serie_rfid = y
            $banearUsuario = "UPDATE usuarios SET baneado = 1, hora_baneo = '$hora_baneo' WHERE num_serie_rfid = '$y'";
            if (mysqli_query($conector, $banearUsuario)) {
                echo "Usuario baneado correctamente";
            } else {
                echo "Error al banear usuario";
            }
        }
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