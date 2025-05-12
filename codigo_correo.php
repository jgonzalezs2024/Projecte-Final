<?php
$destinatario = "destino@correo.com";
$asunto = "Mensaje desde ESP32";

$mensaje = "Temperatura: " . $_POST['temp'] . " °C\n";
$mensaje .= "Humedad: " . $_POST['hum'] . " %";

$headers = "From: esp32@tu-dominio.com";

if (mail($destinatario, $asunto, $mensaje, $headers)) {
    echo "OK";
} else {
    echo "ERROR";
}
?>
