<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
function obtenerCalleYNumero($lat, $lng, $apiKey) {
    $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lng&key=$apiKey&language=es";

    // Inicializar cURL
    $ch = curl_init();

    // Configurar opciones de cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Ejecutar la solicitud cURL
    $response = curl_exec($ch);

    // Verificar si hubo error con la solicitud cURL
    if(curl_errno($ch)) {
        return "Error cURL: " . curl_error($ch);
    }

    // Cerrar la sesión cURL
    curl_close($ch);

    // Verificamos si la respuesta es válida
    if ($response === FALSE) {
        return "Error al obtener la respuesta de la API.";
    }

    // Imprimir el JSON completo de la respuesta para depuración
    echo "<pre>"; 
    print_r($response);
    echo "</pre>";

    // Decodificar el JSON
    $data = json_decode($response, true);

    // Verificar que los resultados existen en el JSON
    if (!isset($data['results'])) {
        return "Dirección no disponible";
    }

    // Iterar sobre los resultados para extraer la calle y el número
    foreach ($data['results'] as $result) {
        $street = '';
        $number = '';
        
        foreach ($result['address_components'] as $component) {
            if (in_array('route', $component['types'])) {
                $street = $component['long_name'];
            }
            if (in_array('street_number', $component['types'])) {
                $number = $component['long_name'];
            }
        }

        if ($street || $number) {
            return trim("$street, $number");
        }
    }

    return "Dirección no disponible";
}

// Coordenadas de prueba
$lat = 41.259746;
$lng = 1.77607;

// Tu clave de API (reemplaza 'TU_API_KEY' por la tuya)
$apiKey = 'xxx';

// Llamar a la función y mostrar el resultado
$direccion = obtenerCalleYNumero($lat, $lng, $apiKey);
echo "Dirección obtenida: $direccion";
?>
