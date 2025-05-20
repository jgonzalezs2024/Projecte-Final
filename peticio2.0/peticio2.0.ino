// ==============================================
// LIBRERÍAS NECESARIAS PARA FUNCIONALIDAD WIFI Y SERIAL
// ==============================================
#include <WiFi.h>                                 // Control de conexión WiFi en ESP32
#include <HTTPClient.h>                           // Cliente HTTP para hacer peticiones GET


// ==============================================
// DEFINICIÓN DE PINES PARA SERIAL2 EN ESP32
// ==============================================
#define RXD2 32                                   // Pin RX del ESP32
#define TXD2 33                                   // Pin TX del ESP32


// ==============================================
// CONFIGURACIÓN DE DATOS DE RED Y SERVIDOR WEB
// ==============================================
String serverName = "http://192.168.110.65:80/";  // URL base del servidor para las peticiones HTTP
const char* ssid = "Aula110";                     // Nombre de la red WiFi
const char* password = "aula110aula110";          // Contraseña WiFi


// ==============================================
// VARIABLES GLOBALES PARA EL PROGRAMA
// ==============================================
int httpResponseCode;                             // Código de respuesta HTTP tras realizar una petición
String url, msg, payload;                         // Variables para contruir la petición y la respuesta


// ==============================================
// OBJETOS GLOBALES PARA EL PROGRAMA
// ==============================================
HTTPClient http;                                  // Crea objeto HTTPClient para gestionar la petición


// ==============================================
// CONFIGURACIÓN INICIAL DEL DISPOSITIVO
// ==============================================
void setup() {
  Serial.begin(115200);                           // Inicializa el monitor serie a 115200 baudios
  Serial2.begin(9600, SERIAL_8N1, RXD2, TXD2);    // Inicializa la comunicación Serial2 con Arduino

  Serial.print("Conectandose a red: ");
  Serial.println(ssid);

  // Inicia la conexión WiFi con SSID y contraseña indicados
  WiFi.begin(ssid, password);

  // Espera activa hasta que la conexión WiFi se establezca
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  // Cuando está conectado, imprime la IP asignada por el router
  Serial.println("WiFi conectado");
  Serial.println("IP asignada:");
  Serial.println(WiFi.localIP());
}


// ==============================================
// BUCLE PRINCIPAL - ESCUCHA Y PROCESA DATOS SERIAL2
// ==============================================
void loop() {
  // Comprueba si hay datos recibidos por Serial2 desde Arduino
  if (Serial2.available()) {
    msg = Serial2.readStringUntil('\n');         // Lee la línea completa recibida
    msg.trim();                                  // Elimina espacios y saltos innecesarios
    url = serverName + msg;                      // Construye la URL completa con la consulta recibida

    // Comprueba que la conexión WiFi siga activa antes de hacer la petición
    if (WiFi.status() == WL_CONNECTED) {
      http.begin(url);                           // Inicia la conexión HTTP con la URL construida
      httpResponseCode = http.GET();             // Realiza la petición GET y recibe el código HTTP

      if (httpResponseCode == 200) {             // Si la petición es exitosa
        payload = http.getString();              // Obtiene la respuesta en texto
        Serial.print("Respuesta HTTP: ");
        Serial2.println(payload);                // Envía la respuesta de vuelta al Arduino
      } else {
        Serial.print("Error HTTP: ");           
        Serial.println(httpResponseCode);
      }

      http.end();                               // Finaliza la conexión HTTP
    } else {
      Serial2.println("WiFi no conectado");    // Indica error de conexión
    }
  }
}
