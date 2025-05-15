<?php
function obtenerCalleYNumero($lat, $lng, $apiKey) {
    $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lng&key=$apiKey&language=es";

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (!isset($data['results'][0]['address_components'])) {
        return "Geocoding sin resultados";
    }

    $street = '';
    $number = '';

    foreach ($data['results'][0]['address_components'] as $component) {
        if (in_array('route', $component['types'])) {
            $street = $component['long_name'];
        }
        if (in_array('street_number', $component['types'])) {
            $number = $component['long_name'];
        }
    }

    if ($street && $number) {
        return "$street, $number";
    } elseif ($street) {
        return $street;
    } elseif ($number) {
        return "Nº $number";
    } else {
        return "Dirección encontrada pero sin calle/número";
    }
}

?>
