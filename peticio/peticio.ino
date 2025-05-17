#define RXD2 32  // RX del ESP32 ← TX del Arduino (pin 17)
#define TXD2 33  // TX del ESP32 → RX del Arduino (pin 16)
#include <WiFi.h>
#include <HTTPClient.h>
#include <SoftwareSerial.h>

// INCLUDES PER A LA COMUNICACIÓ AMB L'ARDUINO
// I LA MANIPULACIÓ DE CADENES DE CARÀCTERS
#include <StringSplitter.h>
String serverName="http://192.168.110.65:80/";
const char* ssid = "Aula110";
const char* password = "aula110aula110";
// Variable que contindrà el codi de resposta de la petició HTTP
int httpResponseCode;

char c;
String str, peticio, url;
void setup() {
  Serial.begin(115200);
  Serial2.begin(9600, SERIAL_8N1, RXD2, TXD2);
  Serial.println("ESP32 conectado en GPIO 32/33 usando Serial2");
  Serial.println();
  Serial.print("Conectandose a red : ");
  Serial.println(ssid);
    // Establece la IP fija antes de conectar
  // if (!WiFi.config(local_IP, gateway, subnet, primaryDNS, secondaryDNS)) {
  //   Serial.println("❌ Error al configurar la IP estática");
  // }
  
  // Iniciem el mòdul de connexió WIFI amb el SSID i la contrasenya
  WiFi.begin(ssid, password); 
  
  // Mentre la connexió no estigui activa
  while (WiFi.status() != WL_CONNECTED)
  {
    // Esperem a que ho estigui
    delay(500);
    Serial.print(".");
  }

  // Quan la connexió ja estigui establerta imprimim la informació
  // corresponent i l’adreça IP assignada
  Serial.println("");
  Serial.println("WiFi conectado");
  // Mostrem l’adreça IP que tenim assignada
  Serial.println("Tenim la següent IP:");
  Serial.println(WiFi.localIP());
}

void loop() {
  if (Serial2.available()) {
    String msg = Serial2.readStringUntil('\n');
    msg.trim();
    Serial.println(msg);
    url = serverName + msg;
    if (WiFi.status() == WL_CONNECTED) {
      HTTPClient http;
      http.begin(url);
      httpResponseCode = http.GET();
      if (httpResponseCode == 200) {
        String payload = http.getString();
        Serial.print("Respuesta HTTP: ");
        Serial2.println(payload);
      } else {
        Serial.print("Error HTTP: ");
        Serial.println(httpResponseCode);
      }
      http.end();
    } else {
      Serial2.println("WiFi no conectado");
    }
  }
}
