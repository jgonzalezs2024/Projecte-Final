<?php
function obtenerCalleYNumero($lat, $lng, $clave_api) {
    $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lng&key=$clave_api&language=es";
    $respuesta = file_get_contents($url);
    $archivo = json_decode($respuesta, true);
    if (!isset($archivo['results'][0]['address_components'])) {
        return "Sin resultados";
    }
    $calle = '';
    $numero = '';
    foreach ($archivo['results'][0]['address_components'] as $component) {
        if (in_array('route', $component['types'])) {
            $calle = $component['long_name'];
        }
        if (in_array('street_number', $component['types'])) {
            $numero = $component['long_name'];
        }
    }
    if ($calle && $numero) {
        return "$calle, $numero";
    } else {
        return "Calle sin número";
    }
}
function conectar_base_de_datos() {
    $servidor = "db";
    $usuario = "root";
    $password = "root";
    $base_de_datos = "arduino";

    $datos = "host=$servidor dbname=$base_de_datos user=$usuario password=$password";
    $conexion = pg_connect($datos);

    if (!$conexion) {
        die("Conexión fallida: ");
    }

    return $conexion;
}
function poblacion_por_coordenadas($lat, $lng, $clave_api) {
    $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lng&key=$clave_api&language=es";
    $archivo = json_decode(file_get_contents($url), true);

    foreach ($archivo['results'][0]['address_components'] as $elemento) {
        if (in_array('locality', $elemento['types'])) {
            return $elemento['long_name'];
        }
    }
}


?>
