<?php
include('funciones.php');

// Conexión a la base de datos PostgreSQL
$conexion = conectar_base_de_datos();

// Clave API para uso en función de población por coordenadas
$clave_api = 'xxx';


// ==============================================
// CONSULTA POR RFID
// ==============================================
if (isset($_GET['rfid']) && count($_GET) === 1) {
    $uid = $_GET['rfid'];
    $consulta = "SELECT num_serie FROM rfid WHERE num_serie = '$uid'";
    $resultado = pg_query($conexion, $consulta);

    if ($resultado) {
        $registro = pg_fetch_assoc($resultado);
        if ($registro) {
            echo "1";  // RFID encontrado
        } else {
            echo "-1"; // RFID no encontrado
        }
    }
// ==============================================
// INSERCIÓN DE MÉTRICAS
// ==============================================
} else if (isset($_GET['rfid'], $_GET['id_container'], $_GET['pes']) && count($_GET) > 1) {
    $uid = $_GET['rfid'];
    $id_container = (int)$_GET['id_container'];
    $pes = (float)$_GET['pes'];

    $consulta = "INSERT INTO metricas (id_container, peso_actual, fecha_actual, num_serie)
              VALUES ($id_container, $pes, NOW()::TIMESTAMP(0), '$uid')";
    $resultado = pg_query($conexion, $consulta);

    if ($resultado) {
        echo "1";      // Inserción correcta
    } else {
        echo "-1";     // Error en inserción
    }

// ==============================================
// COMPROBACIÓN DE ESTADO DE UN CONTENEDOR
// ==============================================
} else if (isset($_GET['comprovacio'], $_GET['id_container'])) {
    $id_container = (int)$_GET['id_container'];
    $activo = ($_GET['comprovacio'] == 1) ? 'true' : 'false';

    $consulta = "SELECT activo FROM container WHERE id = $id_container";
    $resultado = pg_query($conexion, $consulta);

    if ($resultado) {
        $registro = pg_fetch_assoc($resultado);
        if ($registro) {
            echo $registro['activo'];
        } else {
            echo "0";
        }
    }

// ==============================================
// ACTUALIZACIÓN DE UBICACIÓN
// ==============================================
} else if (isset($_GET['lat'], $_GET['lng'], $_GET['id_container']) && count($_GET) === 3) {
    $id_container = (int)$_GET['id_container'];
    $lat = (double)$_GET['lat'];
    $lng = (double)$_GET['lng'];

    // Obtiene la población a partir de las coordenadas usando la clave API
    $poblacion = poblacion_por_coordenadas($lat, $lng, $clave_api);
    if (!$poblacion) {
        $poblacion = "Desconocida";
    }

    $consulta = "UPDATE container SET latitud_actual = $lat, longitud_actual = $lng, poblacion = '$poblacion' WHERE id = $id_container";
    $resultado = pg_query($conexion, $consulta);

    if ($resultado) {
        echo "1";      // Actualización correcta
    } else {
        echo "-1";     // Error en actualización
    }

// ==============================================
// INSERCIÓN DE REGISTRO DE VACIADO
// ==============================================
} else if (isset($_GET['lat'], $_GET['lng'], $_GET['id_container'], $_GET['pes'])) {
    $id_container = (int)$_GET['id_container'];
    $lat = (double)$_GET['lat'];
    $lng = (double)$_GET['lng'];
    $pes = (float)$_GET['pes'];

    $consulta = "INSERT INTO vaciados (id_container, peso_vaciado, fecha_vaciado, latitud_vaciado, longitud_vaciado)
              VALUES ($id_container, $pes, NOW()::TIMESTAMP(0), $lat, $lng)";
    $resultado = pg_query($conexion, $consulta);

    if ($resultado) {
        echo "1";      // Inserción correcta
    } else {
        echo "-1";     // Error en inserción
    }

// ==============================================
// ACTUALIZACIÓN DEL ESTADO DE UN CONTENEDOR
// ==============================================
}else if (isset($_GET['activo'], $_GET['id_container'])) {
    $id_container = (int)$_GET['id_container'];
    $activo = ($_GET['activo'] == 1) ? 'true' : 'false';

    $consulta = "UPDATE container SET activo = $activo WHERE id = $id_container";
    $resultado = pg_query($conexion, $consulta);

    if ($resultado) {
        echo "1";
        // Consulta población y tipo del contenedor
        $consulta_poblacion = "SELECT poblacion, tipo FROM container WHERE id = $id_container";
        $resultado_poblacion = pg_query($conexion, $consulta_poblacion);

        if ($resultado_poblacion) {
            $registro = pg_fetch_assoc($resultado_poblacion);
            if ($registro) {
                $poblacion = $registro['poblacion'];
                $tipo = $registro['tipo'];

                // Cuenta cuántos contenedores están inactivos en la misma población y tipo
                $consulta_count = "SELECT COUNT(*) AS total FROM container WHERE activo = false AND poblacion = '$poblacion' AND tipo = '$tipo'";
                $resultado_count = pg_query($conexion, $consulta_count);

                if ($resultado_count) {
                    $registro_count = pg_fetch_assoc($resultado_count);
                    if ($registro_count) {
                        $count = $registro_count['total'];

                        // Si hay 3 o más contenedores inactivos, crea una ruta activa
                        if ($count >= 3) {
                            // Obtiene el último ID de ruta activo e incrementamos el valor en 1 para crear el nuevo registro
                            $consulta_id_ruta = "SELECT MAX(id_ruta) AS id_ultima_ruta FROM rutas_activas";
                            $resultado_id_ruta = pg_query($conexion, $consulta_id_ruta);

                            if ($resultado_id_ruta) {
                                $registro_ruta = pg_fetch_assoc($resultado_id_ruta);
                                if ($registro_ruta) {
                                    // Obtener el valor de id_ultima_ruta desde el array
                                    $id_ultima_ruta = $registro_ruta['id_ultima_ruta'];

                                    // Verifica si id_ultima_ruta tiene un valor válido
                                    if ($id_ultima_ruta) {
                                        // Si tiene valor, asigna el siguiente número
                                        $nuevo_id_ruta = $id_ultima_ruta + 1;
                                    } else {
                                        // Si es NULL o 0, empieza desde 1
                                        $nuevo_id_ruta = 1;
                                    }
                                }
                            } else {
                                $nuevo_id_ruta = 1;
                            }

                            // Selecciona todos los contenedores inactivos para esa población y tipo
                            $consulta_ids_container = "SELECT id FROM container WHERE activo = false AND poblacion = '$poblacion' AND tipo = '$tipo'";
                            $resultado_ids_container = pg_query($conexion, $consulta_ids_container);

                            // Creamos registros para cada contenedor de la ruta
                            if ($resultado_ids_container) {
                                while ($id_registro = pg_fetch_assoc($resultado_ids_container)) {
                                    $id_contenedor = $id_registro['id'];
                                    $insert_rutas = "INSERT INTO rutas_activas (id_ruta, id_contenedor) VALUES ($nuevo_id_ruta, $id_contenedor)";
                                    $respuesta_insert_rutas = pg_query($conexion, $insert_rutas);
                                }
                                // Ruta absoluta del archivo de correo
                                $email_temporal ="/tmp/testmail_ruta_$nuevo_id_ruta.txt";

                                // Construye el enlace al mapa
                                $servidor = "localhost:80";
                                $url_api_maps = "http://$servidor/ver_ruta.php?id_ruta=$nuevo_id_ruta";
                                $remitente = "sapone2015@gmail.com";
                                $destinatario = "sapone2015@gmail.com";

                                // Contenido del correo con el enlace
                                $email_content = <<<EOD
                                From: $remitente
                                To: $destinatario
                                Subject: Nueva ruta disponible
                                Se ha generado una nueva ruta disponible para la recogida.

                                Puedes visualizarla en el siguiente enlace:
                                $url_api_maps

                                Saludos,

                                Sistema de Gestión de Contenedores
                                EOD;

                                // Escribir el archivo
                                file_put_contents($email_temporal, $email_content);

                                // Enviar el correo
                                exec("msmtp -a gmail $destinatario < $email_temporal");

                            }
                        }
                    }
                }
            }
        }
    } else {
        echo "-1";
    }
}

pg_close($conexion);
?>
