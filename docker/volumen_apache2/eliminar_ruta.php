<?php
include('funciones.php');

// ==============================================
// CONEXIÓN A LA BASE DE DATOS
// ==============================================
$conexion = conectar_base_de_datos();

// ==============================================
// ID RECIBIDO POR POST
// ==============================================
$id_ruta = (int)$_POST['id_ruta'];  // casteo por seguridad

// Construcción y ejecución de la consulta DELETE
$consulta = "DELETE FROM rutas_activas WHERE id_ruta = $id_ruta";
pg_query($conexion, $consulta);

// Ruta temporal para email
$email_temporal = "/tmp/testmail_ruta_{$id_ruta}_eliminada.txt";

// Definir URL
$servidor = "localhost:80";  
$url_ruta = "http://$servidor/ver_ruta.php?id_ruta=$id_ruta";

// Datos del remitente y destinatario
$remitente = "sapone2015@gmail.com";
$destinatario = "sapone2015@gmail.com";

// Construcción del contenido del email
$email_content = <<<EOD
From: $remitente
To: $destinatario
Subject: Ruta $id_ruta completada

La ruta con ID $id_ruta ha sido completada.

Los contenedores de la ruta $id_ruta están ahora operativos.

Saludos,

Sistema de Gestión de Contenedores
EOD;

// Escribir el archivo
file_put_contents($email_temporal, $email_content);

// Enviar el email
exec("msmtp -a gmail $destinatario < $email_temporal");

// Redirecciona a rutas activas
header("Location: rutas_activas.php");
exit;

// Cierra la conexión a la base de datos
pg_close($conexion);
?>
